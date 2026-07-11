<?php
require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getConnection();
    
    // Delete the static seed reviews
    $stmt = $db->query("DELETE FROM client_reviews WHERE name IN ('Arjun Sharma', 'Sarah Jenkins', 'Karthik M.')");
    
    echo "Deleted " . $stmt->rowCount() . " static reviews.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
