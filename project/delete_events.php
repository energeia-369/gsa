<?php
require_once __DIR__ . '/config/Database.php';
try {
    $pdo = Database::getConnection();
    $pdo->exec("DELETE FROM home_carousel_events WHERE title IN ('Pune Championship Edition', 'Thailand Edition', 'UAE Edition')");
    echo "Deleted.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
