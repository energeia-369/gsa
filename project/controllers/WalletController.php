<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Wallet.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../config/JwtUtil.php';

class WalletController {
    private $userModel;
    private $walletModel;
    private $transactionModel;

    public function __construct() {
        $this->userModel = new User();
        $this->walletModel = new Wallet();
        $this->transactionModel = new Transaction();
    }

    private function getEmail($emailParam, $authHeader) {
        if (!empty($emailParam)) {
            return $emailParam;
        }
        if (!empty($authHeader) && strpos($authHeader, "Bearer ") === 0) {
            $token = substr($authHeader, 7);
            if (JwtUtil::validateToken($token)) {
                return JwtUtil::extractEmail($token);
            }
        }
        return null;
    }

    public function getWalletBalance($emailParam, $authHeader) {
        $email = $this->getEmail($emailParam, $authHeader);
        if ($email === null) {
            throw new Exception("Email is required");
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            throw new Exception("User not found");
        }
        
        if (strtoupper($user['role']) === 'ADMIN') {
            $userIdToUse = 1; // Master admin wallet ID
            // For admins, we just fetch the master wallet balance directly
            $wallet = $this->walletModel->findByUserId($userIdToUse);
            if (!$wallet) {
                $wallet = $this->walletModel->create($userIdToUse, 100000);
            }
            $walletBalance = floatval($wallet['balance']);
            $userCredits = $walletBalance;
        } else {
            $userCredits = floatval($user['credits']);
            $userIdToUse = $user['id'];

            $wallet = $this->walletModel->findByUserId($userIdToUse);
            if (!$wallet) {
                $wallet = $this->walletModel->create($userIdToUse, $userCredits);
            }

            $walletBalance = floatval($wallet['balance']);
            
            // Self-heal out of sync balances for non-admins
            if ($userCredits !== $walletBalance) {
                $correctBalance = max($userCredits, $walletBalance);
                $this->walletModel->updateBalance($userIdToUse, $correctBalance);
                $this->userModel->updateCredits($userIdToUse, $correctBalance);
                $this->userModel->updateWalletBalance($userIdToUse, $correctBalance);
                $walletBalance = $correctBalance;
            }
        }

        return [
            "userId" => $user['id'], // Keep their actual ID in the response for UI
            "email" => $email,
            "nxlCredits" => $walletBalance,
            "walletBalance" => $walletBalance
        ];
    }

    public function getWalletTransactions($emailParam, $authHeader) {
        $email = $this->getEmail($emailParam, $authHeader);
        if ($email === null) {
            throw new Exception("Email is required");
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            throw new Exception("User not found");
        }

        $userIdToUse = (strtoupper($user['role']) === 'ADMIN') ? 1 : $user['id'];
        return $this->transactionModel->findByUserId($userIdToUse);
    }

    public function rechargeWallet($data) {
        $email = $data['email'] ?? '';
        $amount = floatval($data['amount'] ?? 0);

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            throw new Exception("User not found");
        }

        $wallet = $this->walletModel->findByUserId($user['id']);
        if (!$wallet) {
            $wallet = $this->walletModel->create($user['id'], 0);
        }

        // Conversion rate: recharge of 100 INR gives 105 NXL credits (amount * 1.05 = NXL)
        $creditsToEarn = intval($amount * 1.05);
        if ($creditsToEarn <= 0) {
            $creditsToEarn = 1;
        }

        // Deduct from master admin wallet (ID 1)
        if ($user['id'] != 1) { 
            $masterUser = $this->userModel->findById(1);
            if ($masterUser && $masterUser['credits'] >= $creditsToEarn) {
                $this->userModel->updateCredits(1, $masterUser['credits'] - $creditsToEarn);
                $this->userModel->updateWalletBalance(1, $masterUser['wallet_balance'] - $creditsToEarn);
                $adminWallet = $this->walletModel->findByUserId(1);
                if ($adminWallet) {
                    $this->walletModel->updateBalance(1, $adminWallet['balance'] - $creditsToEarn);
                }
            } else {
                throw new Exception("Insufficient NXL reserves in Admin Master Wallet!");
            }
        }

        $newBalance = $wallet['balance'] + $creditsToEarn;
        $this->walletModel->updateBalance($user['id'], $newBalance);

        // Update User properties
        $this->userModel->updateWalletBalance($user['id'], $newBalance);
        $this->userModel->updateCredits($user['id'], $newBalance);

        // Log transaction
        $refId = "RECHARGE-" . round(microtime(true) * 1000);
        $this->transactionModel->create($user['id'], "EARNED", $creditsToEarn, "Wallet recharge of ₹" . $amount, $refId);

        return [
            "success" => true,
            "amount" => $amount,
            "creditsEarned" => $creditsToEarn,
            "nxlCredits" => $newBalance
        ];
    }

    public function adminAdjustWallet($data) {
        $email = $data['email'] ?? '';
        $amount = floatval($data['amount'] ?? 0);
        $actionType = $data['action'] ?? 'ADD';

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            throw new Exception("User not found");
        }

        $wallet = $this->walletModel->findByUserId($user['id']);
        if (!$wallet) {
            $wallet = $this->walletModel->create($user['id'], 0);
        }

        $newBalance = $wallet['balance'];
        $refId = "ADMIN-" . round(microtime(true) * 1000);

        if (strcasecmp($actionType, "SUBTRACT") === 0) {
            if ($wallet['balance'] < $amount) {
                throw new Exception("User does not have sufficient NXL balance");
            }
            $newBalance -= $amount;
            $this->transactionModel->create($user['id'], "ADMIN_SUB", $amount, "Deducted by administrator adjustment", $refId);
            
            // Refund to master admin wallet
            $this->userModel->updateCredits(1, $this->userModel->findById(1)['credits'] + $amount);
            $this->userModel->updateWalletBalance(1, $this->userModel->findById(1)['wallet_balance'] + $amount);
            $masterWallet = $this->walletModel->findByUserId(1);
            if ($masterWallet) $this->walletModel->updateBalance(1, $masterWallet['balance'] + $amount);
        } else {
            $newBalance += $amount;
            $this->transactionModel->create($user['id'], "ADMIN_ADD", $amount, "Credited by administrator adjustment", $refId);
            
            // Deduct from master admin wallet
            $masterUser = $this->userModel->findById(1);
            if ($masterUser && $masterUser['credits'] >= $amount) {
                $this->userModel->updateCredits(1, $masterUser['credits'] - $amount);
                $this->userModel->updateWalletBalance(1, $masterUser['wallet_balance'] - $amount);
                $masterWallet = $this->walletModel->findByUserId(1);
                if ($masterWallet) $this->walletModel->updateBalance(1, $masterWallet['balance'] - $amount);
            }
        }

        $this->walletModel->updateBalance($user['id'], $newBalance);

        // Update User properties
        $this->userModel->updateWalletBalance($user['id'], $newBalance);
        $this->userModel->updateCredits($user['id'], $newBalance);

        return [
            "success" => true,
            "action" => $actionType,
            "amount" => $amount,
            "nxlCredits" => $newBalance
        ];
    }

    public function getAllTransactions() {
        try {
            $transactions = $this->transactionModel->findAllWithUsers(100);
            return [
                "success" => true,
                "transactions" => $transactions
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => "An error occurred: " . $e->getMessage()
            ];
        }
    }
}
