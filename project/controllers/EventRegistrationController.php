<?php
require_once __DIR__ . '/../models/EventRegistration.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/Settings.php';

class EventRegistrationController {
    private $registrationModel;
    private $userModel;

    public function __construct() {
        $this->registrationModel = new EventRegistration();
        $this->userModel = new User();
    }

    public function getAllRegistrations() {
        return $this->registrationModel->findAll();
    }

    public function registerEvent($data) {
        $teamName = $data['teamName'] ?? ($data['team_name'] ?? '');
        $captainName = $data['captainName'] ?? ($data['captain_name'] ?? '');
        $captainContact = $data['captainContact'] ?? ($data['captain_contact'] ?? '');
        $email = $data['email'] ?? '';
        $sport = $data['sport'] ?? '';
        $registrationType = $data['registrationType'] ?? ($data['registration_type'] ?? '');
        $teamCategory = $data['teamCategory'] ?? ($data['team_category'] ?? '');
        $teamMembers = intval($data['teamMembers'] ?? ($data['team_members'] ?? 0));
        $notes = $data['notes'] ?? '';
        $registrationFee = floatval($data['registrationFee'] ?? ($data['registration_fee'] ?? 2499));
        $paymentStatus = $data['paymentStatus'] ?? ($data['payment_status'] ?? 'PENDING');
        $nxlRedeemed = intval($data['nxlRedeemed'] ?? ($data['nxl_redeemed'] ?? 0));

        // Apply Spring Boot defaults
        if (empty($paymentStatus)) {
            $paymentStatus = 'PENDING';
        }

        $registration = $this->registrationModel->create(
            $teamName,
            $captainName,
            $captainContact,
            $email,
            $sport,
            $registrationType,
            $teamCategory,
            $teamMembers,
            $notes,
            $registrationFee,
            $paymentStatus,
            $nxlRedeemed
        );

        // If user email is present, increment their events_joined count and calculate membership cashback
        if (!empty($email)) {
            $user = $this->userModel->findByEmail($email);
            if ($user) {
                $this->userModel->incrementEventsJoined($user['id']);
                
                // Calculate NXL cashback credits based on active membership tier (standard: 5%, premium: 10%, elite: 15%)
                if ($registrationFee > 0 && strtoupper(trim($paymentStatus)) === 'PAID') {
                    $tier = strtolower(trim($user['membership_tier'] ?? 'none'));
                    // Fetch the global NXL cashback percentage (usually 5%)
                    $cashbackRate = (float)Settings::get('nxl_cashback_percentage', 0.05);
                    
                    if ($cashbackRate > 0) {
                        $earnedCredits = floor($registrationFee * $cashbackRate);
                        if ($earnedCredits > 0) {
                            $currentCredits = intval($user['credits'] ?? 0);
                            $newCredits = $currentCredits + $earnedCredits;
                            $this->userModel->updateCredits($user['id'], $newCredits);
                            $this->userModel->addTransaction($user['id'], 'Earned', $earnedCredits, 'Cashback reward earned on Event Registration: ' . $sport);
                            
                            if (is_array($registration)) {
                                $registration['nxlCoinsEarned'] = $earnedCredits;
                            }
                        }
                    }
                }
            }
        }

        return $registration;
    }
}
