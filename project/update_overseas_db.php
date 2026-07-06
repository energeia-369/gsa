<?php
require_once __DIR__ . '/config/Database.php';
try {
    $pdo = Database::getConnection();
    
    $stmt = $pdo->query("SHOW COLUMNS FROM home_carousel_events LIKE 'show_in_overseas'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE home_carousel_events ADD COLUMN show_in_overseas TINYINT(1) DEFAULT 0 AFTER show_home_banner");
        echo "Added show_in_overseas column.\n";
    }
    
    // Set Thailand and UAE and Pune to show_in_overseas
    $pdo->exec("UPDATE home_carousel_events SET show_in_overseas = 1, show_on_home = 0 WHERE title IN ('Thailand Edition', 'UAE Edition', 'Pune Championship Edition')");
    
    echo "Updated DB.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
