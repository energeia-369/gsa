<?php
require_once __DIR__ . '/../config/Database.php';

class ChatbotFaq {
    private $conn;
    private $table_name = "chatbot_faqs";

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function findAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($question, $answer) {
        $query = "INSERT INTO " . $this->table_name . " (question, answer) VALUES (:question, :answer)";
        $stmt = $this->conn->prepare($query);
        
        $question = htmlspecialchars(strip_tags($question));
        $answer = htmlspecialchars(strip_tags($answer));
        
        $stmt->bindParam(":question", $question);
        $stmt->bindParam(":answer", $answer);
        
        return $stmt->execute();
    }

    public function update($id, $question, $answer) {
        $query = "UPDATE " . $this->table_name . " SET question = :question, answer = :answer WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $question = htmlspecialchars(strip_tags($question));
        $answer = htmlspecialchars(strip_tags($answer));
        $id = htmlspecialchars(strip_tags($id));
        
        $stmt->bindParam(":question", $question);
        $stmt->bindParam(":answer", $answer);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }
}
