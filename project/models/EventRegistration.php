<?php
require_once __DIR__ . '/../config/Database.php';

class EventRegistration {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->initializeDatabase();
    }

    private function initializeDatabase() {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS event_registrations (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                team_name VARCHAR(100) NULL,
                captain_name VARCHAR(100) NULL,
                captain_contact VARCHAR(20) NULL,
                email VARCHAR(100) NULL,
                sport VARCHAR(50) NULL,
                registration_type VARCHAR(50) NULL,
                team_category VARCHAR(50) NULL,
                team_members INT DEFAULT 0,
                notes TEXT NULL,
                registration_fee DOUBLE DEFAULT 0.0,
                nxl_redeemed INT DEFAULT 0,
                payment_status VARCHAR(50) NULL,
                event_date DATE NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        try {
            $this->db->exec("ALTER TABLE event_registrations ADD COLUMN team_category VARCHAR(50) NULL AFTER registration_type");
        } catch (PDOException $e) {}
        try {
            $this->db->exec("ALTER TABLE event_registrations ADD COLUMN nxl_redeemed INT DEFAULT 0 AFTER registration_fee");
        } catch (PDOException $e) {}
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM event_registrations");
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM event_registrations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($teamName, $captainName, $captainContact, $email, $sport, $registrationType, $teamCategory, $teamMembers, $notes, $registrationFee, $paymentStatus, $nxlRedeemed = 0) {
        // Look up event_date from tournaments
        $eventDate = null;
        $tStmt = $this->db->prepare("SELECT end_date FROM tournaments WHERE name COLLATE utf8mb4_unicode_ci = ? LIMIT 1");
        $tStmt->execute([$sport]);
        $tour = $tStmt->fetch(PDO::FETCH_ASSOC);
        if ($tour && !empty($tour['end_date'])) {
            $eventDate = $tour['end_date'];
        }

        $stmt = $this->db->prepare("INSERT INTO event_registrations (team_name, captain_name, captain_contact, email, sport, registration_type, team_category, team_members, notes, registration_fee, payment_status, event_date, nxl_redeemed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$teamName, $captainName, $captainContact, $email, $sport, $registrationType, $teamCategory, $teamMembers, $notes, $registrationFee, $paymentStatus, $eventDate, $nxlRedeemed]);
        $newId = $this->db->lastInsertId();

        // Deduct redeemed coins from user balance in database!
        if ($nxlRedeemed > 0) {
            $uStmt = $this->db->prepare("SELECT id, credits FROM users WHERE email = ?");
            $uStmt->execute([$email]);
            $user = $uStmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $newCredits = intval($user['credits']) - $nxlRedeemed;
                if ($newCredits < 0) $newCredits = 0;
                
                // Update credits
                $upCredits = $this->db->prepare("UPDATE users SET credits = ? WHERE id = ?");
                $upCredits->execute([$newCredits, $user['id']]);
                
                // Add Transaction
                $tIns = $this->db->prepare("INSERT INTO nxl_transactions (user_id, type, amount, description, ref_id) VALUES (?, 'Redeemed', ?, ?, ?)");
                $tIns->execute([$user['id'], -$nxlRedeemed, 'Redeemed for Event Registration: ' . $sport, 'EVT-' . $newId]);
                
                // Auto-add spent NXL back to master admin wallet
                $this->db->prepare("UPDATE users SET credits = credits + ?, wallet_balance = wallet_balance + ? WHERE id = 1")
                         ->execute([$nxlRedeemed, $nxlRedeemed]);
                $this->db->prepare("UPDATE nxl_wallets SET balance = balance + ? WHERE user_id = 1")
                         ->execute([$nxlRedeemed]);
            }
        }

        $record = $this->findById($newId);
        // Attach registration_id explicitly for QR generation
        if ($record) { $record['registration_id'] = $newId; }
        return $record;
    }

    public function updatePaymentStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE event_registrations SET payment_status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}
