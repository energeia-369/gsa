<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/OtpVerification.php';
require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/../services/SmsService.php';
require_once __DIR__ . '/../config/JwtUtil.php';

class AuthController {
    private $userModel;
    private $otpModel;
    private $emailService;
    private $smsService;

    public function __construct() {
        $this->userModel = new User();
        $this->otpModel = new OtpVerification();
        $this->emailService = new EmailService();
        $this->smsService = new SmsService();
    }

    public function register($data) {
        $fullName = $data['fullName'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $phoneNumber = $data['phoneNumber'] ?? '';
        $emailOtp = $data['emailOtp'] ?? '';
        $mobileOtp = $data['mobileOtp'] ?? '';
        $role = $data['role'] ?? 'USER';

        if ($this->userModel->findByEmail($email)) {
            return [
                "message" => "Email already exists",
                "token" => null
            ];
        }

        // Verify OTPs
        $otpRecord = $this->otpModel->findByEmail($email);
        if (!$otpRecord) {
            return [
                "message" => "Invalid or expired OTP codes. Please check your inbox and messages.",
                "token" => null
            ];
        }

        $expiryTime = strtotime($otpRecord['expiry_time']);
        if ($expiryTime < time()) {
            $this->otpModel->deleteByEmail($email);
            return [
                "message" => "Invalid or expired OTP codes. Please check your inbox and messages.",
                "token" => null
            ];
        }

        $emailMatches = ($otpRecord['email_otp'] === $emailOtp);
        $mobileMatches = ($otpRecord['mobile_otp'] === $mobileOtp);

        if (!$emailMatches || !$mobileMatches) {
            return [
                "message" => "Invalid or expired OTP codes. Please check your inbox and messages.",
                "token" => null
            ];
        }

        // Verification successful: Clear OTP
        $this->otpModel->deleteByEmail($email);

        if ($role === 'MERCHANT') {
            return $this->registerMerchant($data);
        }

        require_once __DIR__ . '/../config/Settings.php';
        $initialCredits = (int)Settings::get('signup_nxl_bonus', 25);

        $userId = $this->userModel->create($fullName, $email, $password, $role, $phoneNumber, $initialCredits);

        // Auto create NXL Wallet
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO nxl_wallets (user_id, balance) VALUES (?, ?)");
            $stmt->execute([$userId, $initialCredits]);
            
            if ($initialCredits > 0) {
                $stmtTx = $db->prepare("INSERT INTO nxl_transactions (user_id, type, amount, description, ref_id) VALUES (?, 'Earned', ?, 'Signup Bonus', 'signup_bonus')");
                $stmtTx->execute([$userId, $initialCredits]);
                
                // Deduct from master admin (ID = 1)
                $db->prepare("UPDATE users SET credits = credits - ?, wallet_balance = wallet_balance - ? WHERE id = 1")->execute([$initialCredits, $initialCredits]);
                $db->prepare("UPDATE nxl_wallets SET balance = balance - ? WHERE user_id = 1")->execute([$initialCredits]);
                
                $stmtAdminTx = $db->prepare("INSERT INTO nxl_transactions (user_id, type, amount, description, ref_id) VALUES (1, 'Spent', ?, 'Signup Bonus given to new user', 'signup_bonus_admin')");
                $stmtAdminTx->execute([$initialCredits]);
            }
        } catch (Exception $e) {
            error_log("Failed to create wallet for user: " . $e->getMessage());
        }

        $token = JwtUtil::generateToken($email);

        return [
            "message" => "Registration successful",
            "token" => $token,
            "role" => strtoupper($role),
            "userName" => $fullName
        ];
    }

    public function login($data) {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            return [
                "message" => "User not found",
                "token" => null
            ];
        }

        if (!password_verify($password, $user['password'])) {
            return [
                "message" => "Invalid password",
                "token" => null
            ];
        }

        $token = JwtUtil::generateToken($email);

        return [
            "message" => "Login successful",
            "token" => $token,
            "role" => strtoupper($user['role']),
            "userName" => $user['full_name'],
            "phoneNumber" => $user['phone_number'],
            "membershipTier" => $user['membership_tier']
        ];
    }

    public function registerSendOtp($data) {
        $email = $data['email'] ?? '';
        $phoneNumber = $data['phoneNumber'] ?? '';
        $role = $data['role'] ?? 'USER';

        if ($role === 'MERCHANT') {
            $secretCode = $data['secretCode'] ?? '';
            if ($secretCode !== 'GSA-MERCHANT-2026') {
                return [
                    "success" => false,
                    "message" => "Invalid Merchant Secret Code"
                ];
            }
            
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT id FROM merchants WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return [
                    "success" => false,
                    "message" => "Email already exists in merchant records"
                ];
            }
        } else {
            if ($this->userModel->findByEmail($email)) {
                return [
                    "success" => false,
                    "message" => "Email already exists"
                ];
            }
        }

        // Generate 6-digit random codes
        $emailOtp = strval(rand(100000, 999000));
        $mobileOtp = strval(rand(100000, 999000));

        // Save
        $this->otpModel->save($email, $phoneNumber, $emailOtp, $mobileOtp);

        // Send OTPs
        $this->emailService->sendEmailOtp($email, $emailOtp);
        $this->smsService->sendSmsOtp($phoneNumber, $mobileOtp);

        // Mask phone number
        $maskedPhone = $phoneNumber;
        if (strlen($phoneNumber) >= 4) {
            $lastFour = substr($phoneNumber, -4);
            $maskedPhone = "+91 ******" . $lastFour;
        }

        return [
            "success" => true,
            "maskedPhone" => $maskedPhone,
            "message" => "Verification codes successfully dispatched to your Gmail inbox and mobile phone!",
            "devEmailOtp" => $emailOtp,
            "devMobileOtp" => $mobileOtp
        ];
    }

    public function sendOtp($data) {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $isMerchant = false;
        $user = null;

        // Check Merchants table FIRST
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM merchants WHERE email = ?");
        $stmt->execute([$email]);
        $merchant = $stmt->fetch();

        if ($merchant) {
            $user = [
                'password' => $merchant['password'],
                'phone_number' => $merchant['phone'],
                'full_name' => $merchant['merchant_name'],
                'role' => 'MERCHANT'
            ];
            $isMerchant = true;
        } else {
            // Fallback to regular users table
            $user = $this->userModel->findByEmail($email);
            if (!$user) {
                throw new Exception("User not found");
            }
        }

        if (!password_verify($password, $user['password'])) {
            throw new Exception("Invalid email or password");
        }

        $phone = $user['phone_number'];
        if (empty($phone)) {
            throw new Exception("No registered phone number found for email: $email. Please register with a phone number first.");
        }

        // Generate 6-digit random code
        $otpStr = strval(rand(100000, 999000));

        // In PHP, since there is no long-running singleton, we can cache login OTPs in php session or database otp_verifications table.
        // Caching in otp_verifications table with a special type/placeholder or just saving it as both email and mobile otp is extremely robust.
        $this->otpModel->save($email, $phone, $otpStr, $otpStr, 10);

        // Send Email
        $this->emailService->sendLoginOtp($email, $otpStr);

        // Send SMS (simulated/real)
        $this->smsService->sendSmsOtp($phone, $otpStr);

        // Mask
        $maskedPhone = $phone;
        if (strlen($phone) >= 4) {
            $lastFour = substr($phone, -4);
            $maskedPhone = "+91 ******" . $lastFour;
        }

        return [
            "success" => true,
            "maskedPhone" => $maskedPhone,
            "message" => "A secure login verification code has been sent to your Gmail inbox!",
            "devOtp" => $otpStr
        ];
    }

    public function verifyOtp($data) {
        $email = $data['email'] ?? '';
        $enteredOtp = $data['otp'] ?? '';

        $otpRecord = $this->otpModel->findByEmail($email);
        if (!$otpRecord) {
            return [
                "message" => "No active verification code found for this user",
                "token" => null
            ];
        }

        $expiryTime = strtotime($otpRecord['expiry_time']);
        if ($expiryTime < time()) {
            $this->otpModel->deleteByEmail($email);
            return [
                "message" => "No active verification code found for this user",
                "token" => null
            ];
        }

        // The login OTP was stored in both fields
        if ($otpRecord['email_otp'] !== $enteredOtp) {
            return [
                "message" => "Invalid OTP code. Please check your messages.",
                "token" => null
            ];
        }

        // Verification successful: Clear OTP and issue token
        $this->otpModel->deleteByEmail($email);

        // Check Merchants table FIRST
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM merchants WHERE email = ?");
        $stmt->execute([$email]);
        $merchant = $stmt->fetch();
        
        if ($merchant) {
            // Set PHP Sessions for merchant
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['merchant_id'] = $merchant['id'];
            $_SESSION['merchant_name'] = $merchant['merchant_name'];
            $_SESSION['merchant_email'] = $merchant['email'];
            $_SESSION['role'] = 'merchant';

            return [
                "message" => "Merchant Login successful",
                "token" => "MERCHANT_SESSION_ACTIVE", // To pass the JS check
                "role" => "MERCHANT",
                "userName" => $merchant['merchant_name']
            ];
        }

        // Fallback to regular users table
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            throw new Exception("User not found");
        }

        $token = JwtUtil::generateToken($email);

        return [
            "message" => "OTP Verification successful",
            "token" => $token,
            "role" => strtoupper($user['role']),
            "userName" => $user['full_name'],
            "phoneNumber" => $user['phone_number'],
            "membershipTier" => $user['membership_tier']
        ];
    }

    public function registerMerchant($data) {
        $fullName = $data['fullName'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $phoneNumber = $data['phoneNumber'] ?? '';
        $secretCode = $data['secretCode'] ?? '';

        if ($secretCode !== 'GSA-MERCHANT-2026') {
            return [
                "success" => false,
                "message" => "Invalid Merchant Secret Code"
            ];
        }

        try {
            $db = Database::getConnection();

            // Check duplicate email
            $stmt = $db->prepare("SELECT id FROM merchants WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return [
                    "success" => false,
                    "message" => "Email already exists in merchant records"
                ];
            }

            // Check duplicate phone
            $stmt = $db->prepare("SELECT id FROM merchants WHERE phone = ?");
            $stmt->execute([$phoneNumber]);
            if ($stmt->fetch()) {
                return [
                    "success" => false,
                    "message" => "Phone number already exists in merchant records"
                ];
            }

            // Insert new merchant
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO merchants (merchant_name, email, phone, password, secret_code, status) VALUES (?, ?, ?, ?, ?, 'active')");
            $stmt->execute([$fullName, $email, $phoneNumber, $hashedPassword, $secretCode]);
            
            // ALSO insert into users table so the wallet system works seamlessly
            $stmt = $db->prepare("INSERT INTO users (full_name, email, password, role, phone_number, wallet_balance, credits, total_orders, events_joined) VALUES (?, ?, ?, 'MERCHANT', ?, 0, 0, 0, 0)");
            $stmt->execute([$fullName, $email, $hashedPassword, $phoneNumber]);

            return [
                "success" => true,
                "message" => "Merchant registration successful!"
            ];
        } catch (Exception $e) {
            error_log("Merchant Registration Error: " . $e->getMessage());
            return [
                "success" => false,
                "message" => "Failed to register merchant account"
            ];
        }
    }

    public function loginMerchant($data) {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $secretCode = $data['secretCode'] ?? '';

        if ($secretCode !== 'GSA-MERCHANT-2026') {
            return [
                "success" => false,
                "message" => "Invalid Merchant Secret Code"
            ];
        }

        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM merchants WHERE email = ?");
            $stmt->execute([$email]);
            $merchant = $stmt->fetch();

            if (!$merchant) {
                return [
                    "success" => false,
                    "message" => "Merchant not found"
                ];
            }

            if (!password_verify($password, $merchant['password'])) {
                return [
                    "success" => false,
                    "message" => "Invalid password"
                ];
            }

            if ($merchant['status'] !== 'active') {
                return [
                    "success" => false,
                    "message" => "Merchant account is not active. Please contact support."
                ];
            }

            if ($merchant['secret_code'] !== $secretCode) {
                return [
                    "success" => false,
                    "message" => "Secret Code mismatch"
                ];
            }

            // Set PHP Sessions for merchant
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['merchant_id'] = $merchant['id'];
            $_SESSION['merchant_name'] = $merchant['merchant_name'];
            $_SESSION['merchant_email'] = $merchant['email'];
            $_SESSION['role'] = 'merchant';

            return [
                "success" => true,
                "message" => "Merchant Login successful!"
            ];
        } catch (Exception $e) {
            error_log("Merchant Login Error: " . $e->getMessage());
            return [
                "success" => false,
                "message" => "Server error during merchant login"
            ];
        }
    }
}
