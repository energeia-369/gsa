<?php
require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getConnection();
    // Add membership_expiry to users
    $db->exec("ALTER TABLE users ADD COLUMN membership_expiry DATETIME NULL DEFAULT NULL");
    echo "Column membership_expiry added successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
