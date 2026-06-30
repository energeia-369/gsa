<?php
require_once __DIR__ . '/../config/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll();
    }

    public function updateProfile($id, $fullName, $phoneNumber, $profilePic = null) {
        if ($profilePic !== null) {
            $stmt = $this->db->prepare("UPDATE users SET full_name = ?, phone_number = ?, profile_pic = ? WHERE id = ?");
            return $stmt->execute([$fullName, $phoneNumber, $profilePic, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET full_name = ?, phone_number = ? WHERE id = ?");
            return $stmt->execute([$fullName, $phoneNumber, $id]);
        }
    }

    public function create($fullName, $email, $password, $role = 'USER', $phoneNumber = null, $initialCredits = 0) {
        // Spring Boot uses BCrypt/PasswordEncoder by default
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (full_name, email, password, role, phone_number, wallet_balance, credits, total_orders, events_joined, membership_tier) VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, 'none')");
        $stmt->execute([$fullName, $email, $hashedPassword, $role, $phoneNumber, $initialCredits, $initialCredits]);
        return $this->db->lastInsertId();
    }

    public function update($id, $fullName, $email, $phoneNumber, $role) {
        $stmt = $this->db->prepare("UPDATE users SET full_name = ?, email = ?, phone_number = ?, role = ? WHERE id = ?");
        $stmt->execute([$fullName, $email, $phoneNumber, $role, $id]);
        return $this->findById($id);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return true;
    }

    public function updateWalletBalance($id, $balance) {
        $stmt = $this->db->prepare("UPDATE users SET wallet_balance = ? WHERE id = ?");
        return $stmt->execute([$balance, $id]);
    }

    public function updateCredits($id, $credits) {
        $stmt = $this->db->prepare("UPDATE users SET credits = ? WHERE id = ?");
        return $stmt->execute([$credits, $id]);
    }

    public function updateMembershipTier($id, $tier, $expiryDate = null) {
        if ($expiryDate) {
            $stmt = $this->db->prepare("UPDATE users SET membership_tier = ?, membership_expiry = ? WHERE id = ?");
            return $stmt->execute([$tier, $expiryDate, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET membership_tier = ? WHERE id = ?");
            return $stmt->execute([$tier, $id]);
        }
    }

    public function incrementTotalOrders($id) {
        $stmt = $this->db->prepare("UPDATE users SET total_orders = total_orders + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function incrementEventsJoined($id) {
        $stmt = $this->db->prepare("UPDATE users SET events_joined = events_joined + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getUserTransactions($userId) {
        $stmt = $this->db->prepare("SELECT * FROM nxl_transactions WHERE user_id = ? ORDER BY date DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function addTransaction($userId, $type, $amount, $description, $refId = null) {
        $stmt = $this->db->prepare("INSERT INTO nxl_transactions (user_id, type, amount, description, ref_id) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $type, $amount, $description, $refId]);
    }
}
