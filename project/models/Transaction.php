<?php
require_once __DIR__ . '/../config/Database.php';

class Transaction {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM nxl_transactions WHERE user_id = ? ORDER BY date DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function create($userId, $type, $amount, $description, $refId) {
        $stmt = $this->db->prepare("INSERT INTO nxl_transactions (user_id, type, amount, description, ref_id, date) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$userId, $type, $amount, $description, $refId]);
        return $this->db->lastInsertId();
    }

    public function findAllWithUsers($limit = 100) {
        $stmt = $this->db->prepare("SELECT t.*, u.full_name as name, u.email FROM nxl_transactions t JOIN users u ON t.user_id = u.id ORDER BY t.date DESC LIMIT ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
