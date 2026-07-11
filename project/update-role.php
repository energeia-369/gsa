<?php
require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getConnection();
    
    // Update existing reviews that say "Community Member" to "User"
    $stmt = $db->query("UPDATE client_reviews SET role = 'User' WHERE role = 'Community Member'");
    
    echo "Updated " . $stmt->rowCount() . " reviews to 'User'.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
