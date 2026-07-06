<?php
require_once __DIR__ . '/config/Database.php';

try {
    $pdo = Database::getConnection();
    
    $stmt = $pdo->query('SELECT COUNT(*) FROM home_carousel_events');
    echo 'home_carousel_events: ' . $stmt->fetchColumn() . "\n";
    
    $stmt = $pdo->query('SELECT COUNT(*) FROM gsa_carousel_events');
    echo 'gsa_carousel_events: ' . $stmt->fetchColumn() . "\n";

    $stmt = $pdo->query('SELECT COUNT(*) FROM home_event_cards');
    echo 'home_event_cards: ' . $stmt->fetchColumn() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
