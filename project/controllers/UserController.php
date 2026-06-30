<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/JwtUtil.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function getUserProfile($email) {
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            header("HTTP/1.1 404 Not Found");
            return ["error" => "User not found"];
        }
        // Exclude password for security
        unset($user['password']);

        // Map snake_case DB columns → camelCase keys for the JS dashboard
        $user['totalOrders']  = intval($user['total_orders']  ?? 0);
        $user['eventsJoined'] = intval($user['events_joined'] ?? 0);
        $user['membershipTier'] = $user['membership_tier'] ?? 'none';
        $user['membershipExpiry'] = $user['membership_expiry'] ?? null;

        // Auto-expire membership if past due, or flag if already expired
        if ($user['membershipExpiry'] && strtotime($user['membershipExpiry']) < time()) {
            if ($user['membershipTier'] !== 'none') {
                $user['membershipTier'] = 'none';
                try {
                    require_once __DIR__ . '/../config/Database.php';
                    $db = Database::getConnection();
                    $db->prepare("UPDATE users SET membership_tier = 'none' WHERE id = ?")->execute([$user['id']]);
                } catch (Exception $e) {
                    // Ignore DB error
                }
            }
            $user['isExpiredMember'] = true; // Tell the frontend to show the expiration popup
        }

        // Always read walletBalance from nxl_wallets (authoritative source)
        // and self-heal any sync drift in the users table
        try {
            require_once __DIR__ . '/../config/Database.php';
            $db = Database::getConnection();
            $wStmt = $db->prepare("SELECT balance FROM nxl_wallets WHERE user_id = ?");
            $wStmt->execute([$user['id']]);
            $wallet = $wStmt->fetch(PDO::FETCH_ASSOC);
            if ($wallet) {
                $nxlBalance = intval($wallet['balance']);
                // Sync if drifted
                if (intval($user['wallet_balance'] ?? 0) !== $nxlBalance || intval($user['credits'] ?? 0) !== $nxlBalance) {
                    $db->prepare("UPDATE users SET wallet_balance = ?, credits = ? WHERE id = ?")
                       ->execute([$nxlBalance, $nxlBalance, $user['id']]);
                }
                $user['walletBalance'] = $nxlBalance;
                $user['credits']       = $nxlBalance;
            } else {
                $user['walletBalance'] = floatval($user['wallet_balance'] ?? 0);
            }
        } catch (Exception $e) {
            $user['walletBalance'] = floatval($user['wallet_balance'] ?? 0);
        }

        return $user;
    }

    public function getAllUsers() {
        $users = $this->userModel->findAll();
        foreach ($users as &$user) {
            unset($user['password']);
        }
        return $users;
    }

    public function updateUser($id, $data) {
        $fullName = $data['fullName'] ?? '';
        $email = $data['email'] ?? '';
        $phoneNumber = $data['phoneNumber'] ?? '';
        $role = $data['role'] ?? 'USER';

        $updatedUser = $this->userModel->update($id, $fullName, $email, $phoneNumber, $role);
        if ($updatedUser) {
            unset($updatedUser['password']);
        }
        return $updatedUser;
    }

    public function deleteUser($id) {
        $this->userModel->delete($id);
        return ["deleted" => true];
    }

    public function getUserTransactions($email) {
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            header("HTTP/1.1 404 Not Found");
            return ["success" => false, "message" => "User not found"];
        }
        $transactions = $this->userModel->getUserTransactions($user['id']);
        return ["success" => true, "data" => $transactions];
    }

    public function getNotifications($email) {
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            header("HTTP/1.1 404 Not Found");
            return ["success" => false, "message" => "User not found"];
        }
        
        $notifications = [];
        
        // 1. Fetch NXL Transactions
        $transactions = $this->userModel->getUserTransactions($user['id']);
        foreach ($transactions as $txn) {
            $amount = intval($txn['amount']);
            $typeStr = strtolower($txn['type']);
            $isCredit = in_array($typeStr, ['earned', 'admin_add', 'credited', 'recharge', 'add']);
            
            $action = $isCredit ? "credited" : "deducted";
            $title = $isCredit ? "NXL Credits Received" : "NXL Credits Deducted";
            $message = "You have been " . $action . " " . abs($amount) . " NXL Credits. " . ($txn['description'] ? "(" . $txn['description'] . ")" : "");
            
            $notifications[] = [
                "type" => "NXL_TRANSACTION",
                "title" => $title,
                "message" => $message,
                "date" => $txn['date'],
                "timestamp" => strtotime($txn['date'])
            ];
        }
        
        // 2. Fetch Tournaments
        $db = Database::getConnection();
        try {
            $stmt = $db->query("SELECT * FROM tournaments ORDER BY created_at DESC LIMIT 20");
            $tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($tournaments as $tour) {
                $dateStr = isset($tour['created_at']) ? $tour['created_at'] : date('Y-m-d H:i:s');
                $notifications[] = [
                    "type" => "NEW_TOURNAMENT",
                    "title" => "New Tournament: " . $tour['name'],
                    "message" => "A new " . $tour['sport'] . " match has been added at " . $tour['venue'] . "! Join now.",
                    "date" => $dateStr,
                    "timestamp" => strtotime($dateStr)
                ];
            }
        } catch(PDOException $e) {
            // Ignore if created_at doesn't exist yet
        }
        
        // 3. Sort by timestamp descending
        usort($notifications, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        return ["success" => true, "data" => $notifications];
    }

    public function redeemCredits($data) {
        $email = $data['email'] ?? '';
        $rewardCost = intval($data['rewardCost'] ?? 0);
        $rewardName = $data['rewardName'] ?? 'Unknown Reward';

        if (!$email || $rewardCost <= 0) {
            header("HTTP/1.1 400 Bad Request");
            return ["success" => false, "message" => "Invalid request data"];
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            header("HTTP/1.1 404 Not Found");
            return ["success" => false, "message" => "User not found"];
        }

        $currentCredits = intval($user['credits']);
        if ($currentCredits < $rewardCost) {
            header("HTTP/1.1 400 Bad Request");
            return ["success" => false, "message" => "Insufficient credits"];
        }

        // Deduct credits
        $newCredits = $currentCredits - $rewardCost;
        $this->userModel->updateCredits($user['id'], $newCredits);

        // Add transaction
        $this->userModel->addTransaction($user['id'], 'Redeemed', -$rewardCost, "Redeemed: " . $rewardName);

        // Auto-add spent NXL back to master admin wallet
        $masterUser = $this->userModel->findById(1);
        if ($masterUser) {
            $this->userModel->updateCredits(1, $masterUser['credits'] + $rewardCost);
            $this->userModel->updateWalletBalance(1, $masterUser['wallet_balance'] + $rewardCost);
            
            // Note: If using walletModel, we could also update it. However, the requirement is to update admin credits.
            require_once __DIR__ . '/WalletController.php'; // ensure WalletModel is accessible if needed, or we just rely on user table.
            // Let's use direct DB query to be safe, since UserController doesn't have walletModel injected here
            $db = Database::getConnection();
            $db->prepare("UPDATE nxl_wallets SET balance = balance + ? WHERE user_id = 1")->execute([$rewardCost]);
        }

        return ["success" => true, "message" => "Successfully redeemed " . $rewardName, "new_credits" => $newCredits];
    }
    
    public function updateMembership($data) {
        $email = $data['email'] ?? '';
        $plan = $data['plan'] ?? 'none';

        if (!$email) {
            header("HTTP/1.1 400 Bad Request");
            return ["success" => false, "message" => "Email required"];
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            header("HTTP/1.1 404 Not Found");
            return ["success" => false, "message" => "User not found"];
        }

        $earnedCredits = intval($data['earnedCredits'] ?? 0);
        $expiryDate = date('Y-m-d H:i:s', strtotime('+1 year'));
        if ($plan === 'none') {
            $expiryDate = null;
        }

        $this->userModel->updateMembershipTier($user['id'], $plan, $expiryDate);
        
        if ($earnedCredits > 0) {
            $currentCredits = intval($user['credits']);
            $newCredits = $currentCredits + $earnedCredits;
            
            $this->userModel->updateCredits($user['id'], $newCredits);
            $this->userModel->updateWalletBalance($user['id'], $newCredits);
            
            $db = Database::getConnection();
            // Ensure nxl_wallets is updated to prevent self-healing sync drift
            $db->prepare("UPDATE nxl_wallets SET balance = balance + ? WHERE user_id = ?")->execute([$earnedCredits, $user['id']]);
            
            $this->userModel->addTransaction($user['id'], 'Earned', $earnedCredits, 'Earned from Membership Purchase: ' . ucfirst($plan));
            
            // Deduct from master admin (ID = 1)
            $db->prepare("UPDATE users SET credits = credits - ?, wallet_balance = wallet_balance - ? WHERE id = 1")->execute([$earnedCredits, $earnedCredits]);
            $db->prepare("UPDATE nxl_wallets SET balance = balance - ? WHERE user_id = 1")->execute([$earnedCredits]);
            $db->prepare("INSERT INTO nxl_transactions (user_id, type, amount, description, ref_id) VALUES (1, 'Spent', ?, ?, 'membership_bonus_admin')")->execute([$earnedCredits, 'Membership Bonus given to user']);
        }

        return ["success" => true, "message" => "Membership updated to " . $plan];
    }
    
    public function issueCoins($data) {
        $email = $data['email'] ?? '';
        $merchantEmail = $data['merchantEmail'] ?? '';
        $amount = intval($data['amount'] ?? 0);
        $reason = $data['reason'] ?? 'Issued by Merchant';

        if (!$email || !$merchantEmail || $amount <= 0) {
            header("HTTP/1.1 400 Bad Request");
            return ["success" => false, "message" => "Invalid request data"];
        }

        // 1. Check merchant
        $merchant = $this->userModel->findByEmail($merchantEmail);
        if (!$merchant || $merchant['role'] !== 'MERCHANT') {
            header("HTTP/1.1 403 Forbidden");
            return ["success" => false, "message" => "Only merchants can issue coins"];
        }

        require_once __DIR__ . '/../models/Wallet.php';
        $walletModel = new Wallet();
        
        $merchantWallet = $walletModel->findByUserId($merchant['id']);
        $merchantBal = $merchantWallet ? intval($merchantWallet['balance']) : 0;
        
        if ($merchantBal < $amount) {
            header("HTTP/1.1 400 Bad Request");
            return ["success" => false, "message" => "Insufficient merchant balance"];
        }

        // 2. Check user
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            header("HTTP/1.1 404 Not Found");
            return ["success" => false, "message" => "User not found"];
        }

        // 3. Deduct from merchant
        $newMerchantBal = $merchantBal - $amount;
        $walletModel->updateBalance($merchant['id'], $newMerchantBal);
        $this->userModel->updateWalletBalance($merchant['id'], $newMerchantBal);
        $this->userModel->updateCredits($merchant['id'], $newMerchantBal);
        $this->userModel->addTransaction($merchant['id'], 'Debited', -$amount, "Issued coins to " . $email);

        // 4. Credit to user (with self-healing)
        $userWallet = $walletModel->findByUserId($user['id']);
        $userCredits = intval($user['credits']);
        $userWalletBal = 0;
        
        if (!$userWallet) {
            $userWallet = $walletModel->create($user['id'], $userCredits);
            $userWalletBal = $userCredits;
        } else {
            $userWalletBal = intval($userWallet['balance']);
        }
        
        $correctUserBal = max($userCredits, $userWalletBal);
        $newUserBal = $correctUserBal + $amount;
        
        $walletModel->updateBalance($user['id'], $newUserBal);
        $this->userModel->updateWalletBalance($user['id'], $newUserBal);
        $this->userModel->updateCredits($user['id'], $newUserBal);
        $this->userModel->addTransaction($user['id'], 'Credited', $amount, $reason);

        return ["success" => true, "message" => "Successfully issued " . $amount . " coins."];
    }

    public function updateProfile() {
        $email = $_POST['email'] ?? '';
        $fullName = $_POST['full_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        if (empty($email)) {
            header("HTTP/1.1 400 Bad Request");
            return ["success" => false, "message" => "Email is required to identify user"];
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            header("HTTP/1.1 404 Not Found");
            return ["success" => false, "message" => "User not found"];
        }

        $profilePicPath = null;
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/profiles/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                $fileName = 'profile_' . $user['id'] . '_' . time() . '.' . $fileExtension;
                $targetFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
                    $profilePicPath = 'uploads/profiles/' . $fileName;
                }
            }
        }

        $result = $this->userModel->updateProfile($user['id'], $fullName, $phone, $profilePicPath);
        
        if ($result) {
            return ["success" => true, "message" => "Profile updated successfully", "profile_pic" => $profilePicPath];
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            return ["success" => false, "message" => "Failed to update profile"];
        }
    }

    public function getLatestPass($email) {
        if (!$email) {
            header("HTTP/1.1 400 Bad Request");
            return ["success" => false, "message" => "Email required"];
        }

        $db = Database::getConnection();
        
        // Check visitor_passes
        $stmt = $db->prepare("SELECT id, 'visitor' as type, created_at FROM visitor_passes WHERE email = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$email]);
        $visitorPass = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check exhibitors
        $stmt2 = $db->prepare("SELECT id, 'exhibitor' as type, created_at FROM exhibitors WHERE email = ? ORDER BY created_at DESC LIMIT 1");
        $stmt2->execute([$email]);
        $exhibitorPass = $stmt2->fetch(PDO::FETCH_ASSOC);

        $latestPass = null;
        if ($visitorPass && $exhibitorPass) {
            if (strtotime($visitorPass['created_at']) > strtotime($exhibitorPass['created_at'])) {
                $latestPass = $visitorPass;
            } else {
                $latestPass = $exhibitorPass;
            }
        } else if ($visitorPass) {
            $latestPass = $visitorPass;
        } else if ($exhibitorPass) {
            $latestPass = $exhibitorPass;
        }

        if ($latestPass) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            $qrUrl = $protocol . $_SERVER['HTTP_HOST'] . "/Mithraa_E_Project/project/verify-pass.php?type=" . $latestPass['type'] . "&id=" . $latestPass['id'];
            return ["success" => true, "qrUrl" => $qrUrl, "type" => $latestPass['type']];
        }

        return ["success" => false, "message" => "No pass found"];
    }

    public function getAllPasses($email) {
        if (!$email) {
            header("HTTP/1.1 400 Bad Request");
            return ["success" => false, "message" => "Email required"];
        }

        $db = Database::getConnection();
        
        // Auto-expire passes where event_date has passed
        $db->exec("UPDATE visitor_passes SET status = 'expired' WHERE event_date IS NOT NULL AND event_date < CURDATE() AND status = 'active'");
        $db->exec("UPDATE exhibitors SET status = 'expired' WHERE event_date IS NOT NULL AND event_date < CURDATE() AND status = 'active'");
        
        $stmt = $db->prepare("SELECT id, full_name as pass_name, 'visitor' as type, created_at, status, event_date, event FROM visitor_passes WHERE email = ? ORDER BY created_at DESC");
        $stmt->execute([$email]);
        $visitorPasses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt2 = $db->prepare("SELECT id, company_name as pass_name, 'exhibitor' as type, created_at, status, event_date, event FROM exhibitors WHERE email = ? ORDER BY created_at DESC");
        $stmt2->execute([$email]);
        $exhibitorPasses = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        $allPasses = array_merge($visitorPasses, $exhibitorPasses);
        
        usort($allPasses, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        
        foreach ($allPasses as &$pass) {
            $pass['qrUrl'] = $protocol . $_SERVER['HTTP_HOST'] . "/Mithraa_E_Project/project/verify-pass.php?type=" . $pass['type'] . "&id=" . $pass['id'];
        }

        return ["success" => true, "passes" => $allPasses];
    }
}
