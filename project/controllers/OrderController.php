<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Wallet.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../config/JwtUtil.php';

class OrderController {
    private $userModel;
    private $orderModel;
    private $walletModel;
    private $transactionModel;

    public function __construct() {
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->walletModel = new Wallet();
        $this->transactionModel = new Transaction();
    }

    public function placeOrder($data, $authHeader) {
        $email = null;

        // Try extracting from Auth Header
        if (!empty($authHeader) && strpos($authHeader, "Bearer ") === 0) {
            $token = substr($authHeader, 7);
            if (JwtUtil::validateToken($token)) {
                $email = JwtUtil::extractEmail($token);
            }
        }

        // Fallback: extract from payload
        if ($email === null && isset($data['email'])) {
            $email = $data['email'];
        }

        if ($email === null) {
            throw new Exception("Authentication email required for order placement");
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            throw new Exception("User not found for email: " . $email);
        }

        $totalAmount = floatval($data['total'] ?? 0);
        $nxlCoinsUsed = intval($data['nxlCoinsUsed'] ?? 0);
        $nxlCoinsEarned = intval($data['nxlCoinsEarned'] ?? 0);
        $subtotal = isset($data['subtotal']) ? floatval($data['subtotal']) : ($totalAmount + floatval($nxlCoinsUsed));
        $discountAmount = floatval($data['discountAmount'] ?? 0);
        $paymentStatus = $data['paymentStatus'] ?? 'PAID';
        $orderStatus = $data['status'] ?? 'confirmed';
        $shippingAddress = $data['shippingAddress'] ?? '';
        $customerPhone = $data['customerPhone'] ?? '';
        $itemsJson = isset($data['items']) ? (is_string($data['items']) ? $data['items'] : json_encode($data['items'])) : '[]';

        $paymentMethod = $data['paymentMethod'] ?? 'CARD';
        $rzpPaymentId = $data['paymentId'] ?? null;

        // 1. Process Wallet Credits
        $wallet = $this->walletModel->findByUserId($user['id']);
        if (!$wallet) {
            $wallet = $this->walletModel->create($user['id'], 0);
        }

        $newBalance = intval($wallet['balance']);

        // Deduct used coins
        if ($nxlCoinsUsed > 0) {
            $productPrice = $subtotal - $discountAmount;
            if ($productPrice < 0) $productPrice = 0;
            
            $membershipDiscountAmount = floatval($data['membershipDiscountAmount'] ?? 0);
            $priceAfterMembership = $productPrice - $membershipDiscountAmount;
            
            $gstAmount = floatval($data['gstAmount'] ?? 0);
            $amountAfterGST = $priceAfterMembership + $gstAmount;
            
            if ($nxlCoinsUsed > $amountAfterGST) {
                throw new Exception("Cannot redeem more coins than the order amount after GST");
            }

            if ($wallet['balance'] < $nxlCoinsUsed) {
                throw new Exception("Insufficient NXL Credits balance");
            }
            
            $newBalance -= $nxlCoinsUsed;

            $debitRef = $rzpPaymentId !== null ? $rzpPaymentId : "ORDER-" . round(microtime(true) * 1000);
            $this->transactionModel->create($user['id'], "USED", $nxlCoinsUsed, "Redeemed at order checkout", $debitRef);
        }

        // Earn new coins
        if ($nxlCoinsEarned > 0) {
            $newBalance += $nxlCoinsEarned;

            $creditRef = $rzpPaymentId !== null ? $rzpPaymentId : "ORDER-" . round(microtime(true) * 1000);
            $this->transactionModel->create($user['id'], "EARNED", $nxlCoinsEarned, "Cashback reward earned on order", $creditRef);
        }

        // Save wallet balance
        $this->walletModel->updateBalance($user['id'], $newBalance);

        // Update User entity fields to synchronize
        $this->userModel->updateWalletBalance($user['id'], $newBalance);
        $this->userModel->updateCredits($user['id'], $newBalance);
        $this->userModel->incrementTotalOrders($user['id']);

        // 2. Save Order
        $savedOrder = $this->orderModel->create(
            $user['id'],
            $totalAmount,
            $subtotal,
            $discountAmount,
            $paymentStatus,
            $orderStatus,
            $shippingAddress,
            $customerPhone,
            $nxlCoinsEarned,
            $nxlCoinsUsed,
            $itemsJson
        );

        // 3. Log Payment Details
        $paymentLogRzpId = $rzpPaymentId !== null ? $rzpPaymentId : "FREE_ORDER";
        $paymentLogMethod = $paymentMethod !== null ? strtoupper($paymentMethod) : "FREE";
        $paymentLogTxnId = $rzpPaymentId !== null ? $rzpPaymentId : "TXN-" . round(microtime(true) * 1000);

        if ($rzpPaymentId && strpos($rzpPaymentId, 'pay_') === 0) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/payments/' . $rzpPaymentId);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
            if ($result !== false) {
                $rzpData = json_decode($result, true);
                if (isset($rzpData['method'])) {
                    $paymentLogMethod = strtoupper($rzpData['method']);
                }
            }
            curl_close($ch);
        }

        $this->orderModel->createPaymentLog(
            $savedOrder['id'],
            $paymentLogRzpId,
            $totalAmount,
            $paymentLogMethod,
            "SUCCESS",
            $paymentLogTxnId
        );

        return $savedOrder;
    }

    public function getOrdersByUserEmail($emailParam, $authHeader) {
        $email = $emailParam;

        if ($email === null && !empty($authHeader) && strpos($authHeader, "Bearer ") === 0) {
            $token = substr($authHeader, 7);
            if (JwtUtil::validateToken($token)) {
                $email = JwtUtil::extractEmail($token);
            }
        }

        if ($email === null) {
            throw new Exception("User email is required");
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            throw new Exception("User not found");
        }

        return $this->orderModel->findByUserId($user['id']);
    }

    public function getAllOrders() {
        return $this->orderModel->findAll();
    }

    public function updateOrderStatus($orderId, $status) {
        return $this->orderModel->updateStatus($orderId, $status);
    }
}
