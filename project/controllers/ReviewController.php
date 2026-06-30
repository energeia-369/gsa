<?php

class ReviewController {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../config/Database.php';
        $this->db = Database::getConnection();
    }

    public function handleRequest($method, $pathParts) {
        if ($method === 'GET') {
            $this->getReviews();
        } elseif ($method === 'POST') {
            $this->addReview();
        } elseif ($method === 'DELETE') {
            if (isset($pathParts[1])) {
                $this->deleteReview($pathParts[1]);
            } else {
                $this->sendResponse(400, false, "Review ID missing");
            }
        } else {
            $this->sendResponse(405, false, "Method not allowed");
        }
    }

    private function getReviews() {
        try {
            $stmt = $this->db->query("SELECT * FROM page_reviews ORDER BY created_at DESC");
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Format rating as int
            foreach ($reviews as &$r) {
                $r['rating'] = intval($r['rating']);
            }

            echo json_encode($reviews);
            exit();
        } catch (Exception $e) {
            $this->sendResponse(500, false, "Database Error: " . $e->getMessage());
        }
    }

    private function addReview() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['author']) || !isset($data['comment']) || !isset($data['rating'])) {
                $this->sendResponse(400, false, "Missing required fields");
                return;
            }

            $author = $data['author'];
            $role = isset($data['role']) && $data['role'] === 'Merchant' ? 'Merchant' : 'User';
            $rating = intval($data['rating']);
            $comment = $data['comment'];

            if ($rating < 1 || $rating > 5) {
                $this->sendResponse(400, false, "Rating must be between 1 and 5");
                return;
            }

            $stmt = $this->db->prepare("INSERT INTO page_reviews (author, role, rating, comment) VALUES (:author, :role, :rating, :comment)");
            $stmt->execute([
                ':author' => $author,
                ':role' => $role,
                ':rating' => $rating,
                ':comment' => $comment
            ]);

            $this->sendResponse(201, true, "Review added successfully");
        } catch (Exception $e) {
            $this->sendResponse(500, false, "Database Error: " . $e->getMessage());
        }
    }

    private function deleteReview($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM page_reviews WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            if ($stmt->rowCount() > 0) {
                $this->sendResponse(200, true, "Review deleted successfully");
            } else {
                $this->sendResponse(404, false, "Review not found");
            }
        } catch (Exception $e) {
            $this->sendResponse(500, false, "Database Error: " . $e->getMessage());
        }
    }

    private function sendResponse($code, $success, $message, $data = null) {
        http_response_code($code);
        $response = ["success" => $success, "message" => $message];
        if ($data !== null) {
            $response = array_merge($response, $data);
        }
        echo json_encode($response);
        exit();
    }
}
