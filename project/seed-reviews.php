<?php
require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getConnection();
    
    $stmt = $db->prepare("INSERT INTO client_reviews (name, role, rating, review_text) VALUES (?, ?, ?, ?)");
    
    $stmt->execute(['Arjun Sharma', 'Premium Member', 5, 'An absolutely phenomenal platform! The seamless booking process and the incredible community events have completely transformed how I engage with sports. Highly recommended to all enthusiasts.']);
    $stmt->execute(['Sarah Jenkins', 'Corporate Exhibitor', 4, 'As an exhibitor, the ENERGEIA summit provided unparalleled exposure for my brand. The management team was incredibly supportive, and the ROI was beyond expectations.']);
    $stmt->execute(['Karthik M.', 'GSA League Athlete', 5, 'The NXL credits system is a game changer! It makes shopping in the sports store so rewarding. I\'ve bought multiple merchandise items completely using my earned credits.']);
    
    echo "Seed data inserted!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
