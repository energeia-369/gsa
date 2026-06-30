<?php
require_once __DIR__ . '/../config/Database.php';

class Order {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM orders ORDER BY order_date DESC");
        return $stmt->fetchAll();
    }

    public function create($userId, $totalAmount, $subtotal, $discountAmount, $paymentStatus, $orderStatus, $shippingAddress, $customerPhone, $nxlCoinsEarned, $nxlCoinsUsed, $itemsJson) {
        $orderId = 'ORD-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        $stmt = $this->db->prepare("INSERT INTO orders (id, user_id, total_amount, subtotal, discount_amount, payment_status, order_status, shipping_address, customer_phone, nxl_coins_earned, nxl_coins_used, items_json, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $orderId, $userId, $totalAmount, $subtotal, $discountAmount, $paymentStatus, $orderStatus, $shippingAddress, $customerPhone, $nxlCoinsEarned, $nxlCoinsUsed, $itemsJson
        ]);
        return $this->findById($orderId);
    }

    public function updateStatus($orderId, $status) {
        $stmt = $this->db->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
        return $this->findById($orderId);
    }

    public function createPaymentLog($orderId, $razorpayPaymentId, $amount, $method, $status, $txnId) {
        $stmt = $this->db->prepare("INSERT INTO payments (order_id, razorpay_payment_id, amount, method, status, txn_id, timestamp) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([$orderId, $razorpayPaymentId, $amount, $method, $status, $txnId]);
    }
}
