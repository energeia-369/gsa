<?php
require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getConnection();
    
    // Delete any reviews that say "Guest User" to clean up the user's test reviews
    $stmt = $db->query("DELETE FROM client_reviews WHERE name = 'Guest User'");
    
    echo "Deleted " . $stmt->rowCount() . " Guest User reviews.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
