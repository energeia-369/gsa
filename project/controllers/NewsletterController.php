<?php
require_once __DIR__ . '/../models/NewsletterSubscriber.php';

class NewsletterController {
    private $subscriberModel;

    public function __construct() {
        $this->subscriberModel = new NewsletterSubscriber();
    }

    public function subscribe($data) {
        $email = $data['email'] ?? '';
        if (empty(trim($email))) {
            return [
                "success" => false,
                "message" => "Email address is required"
            ];
        }

        $existing = $this->subscriberModel->findByEmail($email);
        if ($existing) {
            if ($existing['status'] === 'UNSUBSCRIBED') {
                $this->subscriberModel->updateStatus($email, 'ACTIVE');
                return [
                    "success" => true,
                    "message" => "Successfully resubscribed to newsletter!"
                ];
            }
            return [
                "success" => true,
                "message" => "You are already subscribed to the newsletter!"
            ];
        }

        $this->subscriberModel->create($email, 'ACTIVE');
        return [
            "success" => true,
            "message" => "Successfully subscribed to the newsletter!"
        ];
    }

    public function getSubscribers() {
        return $this->subscriberModel->findAll();
    }
}
