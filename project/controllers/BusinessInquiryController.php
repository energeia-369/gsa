<?php
require_once __DIR__ . '/../config/Database.php';

class BusinessInquiryController {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getAllInquiries() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM business_inquiries ORDER BY created_at DESC");
            $stmt->execute();
            $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $inquiries;
        } catch (Exception $e) {
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }
}
