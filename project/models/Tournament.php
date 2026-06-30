<?php
require_once __DIR__ . '/../config/Database.php';

class Tournament {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM tournaments");
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tournaments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($name, $sport, $venue, $date, $registrationFee, $maxTeams, $currentTeams) {
        $stmt = $this->db->prepare("INSERT INTO tournaments (name, sport, venue, date, registration_fee, max_teams, current_teams) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $sport, $venue, $date, $registrationFee, $maxTeams, $currentTeams]);
        return $this->findById($this->db->lastInsertId());
    }

    public function update($id, $name, $sport, $venue, $date, $registrationFee, $maxTeams, $currentTeams) {
        $stmt = $this->db->prepare("UPDATE tournaments SET name = ?, sport = ?, venue = ?, date = ?, registration_fee = ?, max_teams = ?, current_teams = ? WHERE id = ?");
        $stmt->execute([$name, $sport, $venue, $date, $registrationFee, $maxTeams, $currentTeams, $id]);
        return $this->findById($id);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tournaments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
