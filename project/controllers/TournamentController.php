<?php
require_once __DIR__ . '/../models/Tournament.php';

class TournamentController {
    private $tournamentModel;

    public function __construct() {
        $this->tournamentModel = new Tournament();
    }

    public function getAllTournaments() {
        return $this->tournamentModel->findAll();
    }

    public function getTournamentById($id) {
        $tournament = $this->tournamentModel->findById($id);
        if (!$tournament) {
            header("HTTP/1.1 404 Not Found");
            return ["error" => "Tournament not found"];
        }
        return $tournament;
    }

    public function createTournament($data) {
        $name = $data['name'] ?? '';
        $sport = $data['sport'] ?? '';
        $venue = $data['venue'] ?? '';
        $date = $data['date'] ?? '';
        $registrationFee = floatval($data['registrationFee'] ?? ($data['registration_fee'] ?? 0));
        $maxTeams = intval($data['maxTeams'] ?? ($data['max_teams'] ?? 16));
        $currentTeams = intval($data['currentTeams'] ?? ($data['current_teams'] ?? 0));

        return $this->tournamentModel->create($name, $sport, $venue, $date, $registrationFee, $maxTeams, $currentTeams);
    }

    public function updateTournament($id, $data) {
        $name = $data['name'] ?? '';
        $sport = $data['sport'] ?? '';
        $venue = $data['venue'] ?? '';
        $date = $data['date'] ?? '';
        $registrationFee = floatval($data['registrationFee'] ?? ($data['registration_fee'] ?? 0));
        $maxTeams = intval($data['maxTeams'] ?? ($data['max_teams'] ?? 16));
        $currentTeams = intval($data['currentTeams'] ?? ($data['current_teams'] ?? 0));

        return $this->tournamentModel->update($id, $name, $sport, $venue, $date, $registrationFee, $maxTeams, $currentTeams);
    }

    public function deleteTournament($id) {
        $this->tournamentModel->delete($id);
        return "Tournament deleted successfully";
    }
}
