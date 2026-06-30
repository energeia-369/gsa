<?php
require_once __DIR__ . '/../config/Database.php';

class ContactMessage {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create($name, $email, $subject, $message) {
        $stmt = $this->db->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        return $this->db->lastInsertId();
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM contact_messages ORDER BY id DESC");
        return $stmt->fetchAll();
    }
}
