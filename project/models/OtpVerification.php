<?php
require_once __DIR__ . '/../config/Database.php';

class OtpVerification {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM otp_verifications WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function deleteByEmail($email) {
        $stmt = $this->db->prepare("DELETE FROM otp_verifications WHERE email = ?");
        return $stmt->execute([$email]);
    }

    public function save($email, $phoneNumber, $emailOtp, $mobileOtp, $expiryTimeMinutes = 10) {
        // First clean up any existing OTP
        $this->deleteByEmail($email);

        $expiryTime = date('Y-m-d H:i:s', strtotime("+$expiryTimeMinutes minutes"));
        $stmt = $this->db->prepare("INSERT INTO otp_verifications (email, phone_number, email_otp, mobile_otp, expiry_time, verified) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$email, $phoneNumber, $emailOtp, $mobileOtp, $expiryTime, 0]);
        return $this->findByEmail($email);
    }

    public function setVerified($email, $verified = true) {
        $stmt = $this->db->prepare("UPDATE otp_verifications SET verified = ? WHERE email = ?");
        return $stmt->execute([$verified ? 1 : 0, $email]);
    }
}
