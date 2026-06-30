<?php
require_once __DIR__ . '/../config/Database.php';

class NewsletterSubscriber {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM newsletter_subscribers WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create($email, $status = 'ACTIVE') {
        $stmt = $this->db->prepare("INSERT INTO newsletter_subscribers (email, status, date) VALUES (?, ?, NOW())");
        $stmt->execute([$email, $status]);
        return $this->findByEmail($email);
    }

    public function updateStatus($email, $status) {
        $stmt = $this->db->prepare("UPDATE newsletter_subscribers SET status = ? WHERE email = ?");
        return $stmt->execute([$status, $email]);
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM newsletter_subscribers ORDER BY date DESC");
        return $stmt->fetchAll();
    }
}
