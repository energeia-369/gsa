<?php
require_once __DIR__ . '/../config/Database.php';

class GiftCardController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAllActive() {
        $query = "SELECT * FROM gift_cards WHERE status = 'active' ORDER BY price ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return ["success" => true, "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }

    public function getById($id) {
        $query = "SELECT * FROM gift_cards WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $card = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($card) {
            return ["success" => true, "data" => $card];
        }
        return ["success" => false, "message" => "Gift card not found."];
    }

    public function checkout($data) {
        $gift_card_id = $data['gift_card_id'];
        $recipient_name = $data['recipient_name'];
        $recipient_email = $data['recipient_email'];
        $recipient_mobile = $data['recipient_mobile'];
        $sender_name = $data['sender_name'];
        $sender_email = $data['sender_email'] ?? null;
        $message = $data['message'];
        $delivery_date = $data['delivery_date'];
        $quantity = $data['quantity'] ?? 1;
        $amount = $data['amount'];
        $gst = $data['gst'];
        $discount = $data['discount'] ?? 0;
        $final_amount = $data['final_amount'];
        $payment_status = $data['payment_status'] ?? 'pending';
        $nxl_coins_used = intval($data['nxl_coins_used'] ?? 0);
        $nxl_earned = intval($data['nxl_earned'] ?? 0);

        // Find user if using or earning NXL credits
        $userId = null;
        if (($nxl_coins_used > 0 || $nxl_earned > 0) && $sender_email) {
            $uStmt = $this->db->prepare("SELECT id, wallet_balance, credits FROM users WHERE email = ?");
            $uStmt->execute([$sender_email]);
            $user = $uStmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                if ($nxl_coins_used > 0 && $user['wallet_balance'] < $nxl_coins_used) {
                    return ["success" => false, "message" => "Insufficient NXL credits balance."];
                }
                $userId = $user['id'];
            }
        }

        // Generate unique code
        $gift_code = "GSA-GIFT-2026-" . strtoupper(substr(md5(uniqid('', true)), 0, 8));

        // Get validity days
        $cardRes = $this->getById($gift_card_id);
        $validity_days = $cardRes['success'] ? $cardRes['data']['validity_days'] : 365;
        $expiry_date = date('Y-m-d', strtotime("+$validity_days days"));

        $this->db->beginTransaction();

        try {
            $query = "INSERT INTO gift_card_orders 
                      (gift_card_id, gift_code, recipient_name, recipient_email, recipient_mobile, sender_name, sender_email, message, delivery_date, quantity, amount, gst, discount, final_amount, payment_status, balance, expiry_date) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($query);
            $balance = $amount * $quantity; 

            $stmt->execute([
                $gift_card_id, $gift_code, $recipient_name, $recipient_email, $recipient_mobile, 
                $sender_name, $sender_email, $message, $delivery_date, $quantity, $amount, $gst, 
                $discount, $final_amount, $payment_status, $balance, $expiry_date
            ]);

            // Deduct / Add NXL Credits
            if ($userId && ($nxl_coins_used > 0 || $nxl_earned > 0)) {
                $newWalletBalance = $user['wallet_balance'] - $nxl_coins_used + $nxl_earned;
                
                // Update users table
                $updUser = $this->db->prepare("UPDATE users SET wallet_balance = ?, credits = ? WHERE id = ?");
                $updUser->execute([$newWalletBalance, $newWalletBalance, $userId]);

                // Update nxl_wallets table
                $wStmt = $this->db->prepare("SELECT balance FROM nxl_wallets WHERE user_id = ?");
                $wStmt->execute([$userId]);
                if ($wStmt->fetch()) {
                    $updWallet = $this->db->prepare("UPDATE nxl_wallets SET balance = ? WHERE user_id = ?");
                    $updWallet->execute([$newWalletBalance, $userId]);
                } else {
                    // Create wallet if it doesn't exist
                    $insWallet = $this->db->prepare("INSERT INTO nxl_wallets (user_id, balance) VALUES (?, ?)");
                    $insWallet->execute([$userId, $newWalletBalance]);
                }

                // Log transactions
                if ($nxl_coins_used > 0) {
                    $logTrans = $this->db->prepare("INSERT INTO nxl_transactions (user_id, amount, type, description) VALUES (?, ?, 'used', ?)");
                    $logTrans->execute([$userId, $nxl_coins_used, "Redeemed for Gift Card Purchase: $gift_code"]);
                }
                
                if ($nxl_earned > 0) {
                    $logTrans = $this->db->prepare("INSERT INTO nxl_transactions (user_id, amount, type, description) VALUES (?, ?, 'earned', ?)");
                    $logTrans->execute([$userId, $nxl_earned, "Cashback for Gift Card Purchase: $gift_code"]);
                }
            }

            $this->db->commit();
            return [
                "success" => true, 
                "message" => "Gift card purchased successfully.",
                "data" => [
                    "gift_code" => $gift_code,
                    "recipient_name" => $recipient_name,
                    "amount" => $balance,
                    "expiry_date" => $expiry_date
                ]
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ["success" => false, "message" => "Failed to purchase gift card: " . $e->getMessage()];
        }
    }

    public function redeem($code, $email) {
        // 1. Find gift card
        $query = "SELECT * FROM gift_card_orders WHERE gift_code = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$code]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return ["success" => false, "message" => "Invalid gift card code."];
        }

        if ($order['payment_status'] !== 'completed') {
            return ["success" => false, "message" => "Gift card payment not completed."];
        }

        if ($order['redeem_status'] === 'redeemed') {
            return ["success" => false, "message" => "Gift card has already been redeemed."];
        }

        if ($order['redeem_status'] === 'expired' || strtotime($order['expiry_date']) < time()) {
            return ["success" => false, "message" => "Gift card has expired."];
        }

        // Apply balance to user's wallet
        $balanceToRedeem = $order['balance'];
        
        // Find user_id from email
        $userQuery = "SELECT id, wallet_balance, credits FROM users WHERE email = ?";
        $uStmt = $this->db->prepare($userQuery);
        $uStmt->execute([$email]);
        $user = $uStmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ["success" => false, "message" => "User not found."];
        }

        $userId = $user['id'];
        
        $this->db->beginTransaction();
        try {
            // Update order status
            $updateOrder = "UPDATE gift_card_orders SET redeem_status = 'redeemed', balance = 0 WHERE id = ?";
            $upStmt = $this->db->prepare($updateOrder);
            $upStmt->execute([$order['id']]);

            // Add redemption record
            $insertRedeem = "INSERT INTO gift_card_redemptions (gift_code, user_email, redeemed_amount) VALUES (?, ?, ?)";
            $insStmt = $this->db->prepare($insertRedeem);
            $insStmt->execute([$code, $email, $balanceToRedeem]);

            // Update user core balance and credits
            $newWalletBalance = $user['wallet_balance'] + $balanceToRedeem;
            $newCredits = $user['credits'] + $balanceToRedeem;
            $updateUser = "UPDATE users SET wallet_balance = ?, credits = ? WHERE id = ?";
            $usStmt = $this->db->prepare($updateUser);
            $usStmt->execute([$newWalletBalance, $newCredits, $userId]);

            // Add to nxl_wallets
            $walletQuery = "SELECT id, balance FROM nxl_wallets WHERE user_id = ?";
            $wStmt = $this->db->prepare($walletQuery);
            $wStmt->execute([$userId]);
            $wallet = $wStmt->fetch(PDO::FETCH_ASSOC);

            if ($wallet) {
                $newBalance = $wallet['balance'] + $balanceToRedeem;
                $updateWallet = "UPDATE nxl_wallets SET balance = ? WHERE user_id = ?";
                $uwStmt = $this->db->prepare($updateWallet);
                $uwStmt->execute([$newBalance, $userId]);
            } else {
                $createWallet = "INSERT INTO nxl_wallets (user_id, balance) VALUES (?, ?)";
                $cwStmt = $this->db->prepare($createWallet);
                $cwStmt->execute([$userId, $balanceToRedeem]);
            }

            // Create transaction record
            $insertTrans = "INSERT INTO nxl_transactions (user_id, amount, type, description) VALUES (?, ?, 'earned', ?)";
            $itStmt = $this->db->prepare($insertTrans);
            $itStmt->execute([$userId, $balanceToRedeem, "Gift Card Redeemed: " . $code]);

            $this->db->commit();

            return [
                "success" => true, 
                "message" => "Gift card redeemed successfully.",
                "amount" => $balanceToRedeem,
                "new_balance" => $newWalletBalance
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ["success" => false, "message" => "Redemption failed: " . $e->getMessage()];
        }
    }

    public function getUserGiftCards($email) {
        // Purchased by user OR received by user
        $query = "SELECT * FROM gift_card_orders WHERE sender_email = ? OR recipient_email = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email, $email]);
        return ["success" => true, "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }
}
