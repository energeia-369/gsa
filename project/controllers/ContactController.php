<?php
require_once __DIR__ . '/../models/ContactMessage.php';

class ContactController {
    private $contactModel;

    public function __construct() {
        $this->contactModel = new ContactMessage();
    }

    public function saveMessage($data) {
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $subject = $data['subject'] ?? '';
        $message = $data['message'] ?? '';

        $id = $this->contactModel->create($name, $email, $subject, $message);
        return [
            "id" => intval($id),
            "name" => $name,
            "email" => $email,
            "subject" => $subject,
            "message" => $message
        ];
    }

    public function getAllMessages() {
        return $this->contactModel->findAll();
    }
}
