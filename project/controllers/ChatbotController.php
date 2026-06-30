<?php
require_once __DIR__ . '/../models/ChatbotFaq.php';

class ChatbotController {
    private $faqModel;

    public function __construct() {
        $this->faqModel = new ChatbotFaq();
    }

    public function getAllFaqs() {
        $faqs = $this->faqModel->findAll();
        return ["success" => true, "data" => $faqs];
    }

    public function createFaq($data) {
        $question = $data['question'] ?? '';
        $answer = $data['answer'] ?? '';

        if (empty($question) || empty($answer)) {
            header("HTTP/1.1 400 Bad Request");
            return ["success" => false, "message" => "Question and Answer are required"];
        }

        $result = $this->faqModel->create($question, $answer);
        if ($result) {
            return ["success" => true, "message" => "FAQ created successfully"];
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            return ["success" => false, "message" => "Failed to create FAQ"];
        }
    }

    public function updateFaq($id, $data) {
        $question = $data['question'] ?? '';
        $answer = $data['answer'] ?? '';

        if (empty($question) || empty($answer)) {
            header("HTTP/1.1 400 Bad Request");
            return ["success" => false, "message" => "Question and Answer are required"];
        }

        $result = $this->faqModel->update($id, $question, $answer);
        if ($result) {
            return ["success" => true, "message" => "FAQ updated successfully"];
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            return ["success" => false, "message" => "Failed to update FAQ"];
        }
    }

    public function deleteFaq($id) {
        $result = $this->faqModel->delete($id);
        if ($result) {
            return ["success" => true, "message" => "FAQ deleted successfully"];
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            return ["success" => false, "message" => "Failed to delete FAQ"];
        }
    }
}
