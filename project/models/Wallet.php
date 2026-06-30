<?php
require_once __DIR__ . '/../config/Database.php';

class Wallet {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM nxl_wallets WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function create($userId, $balance = 0) {
        $stmt = $this->db->prepare("INSERT INTO nxl_wallets (user_id, balance) VALUES (?, ?)");
        $stmt->execute([$userId, $balance]);
        return $this->findByUserId($userId);
    }

    public function updateBalance($userId, $balance) {
        $stmt = $this->db->prepare("UPDATE nxl_wallets SET balance = ? WHERE user_id = ?");
        return $stmt->execute([$balance, $userId]);
    }
}
