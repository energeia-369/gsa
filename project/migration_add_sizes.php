<?php
require_once __DIR__ . '/config/Database.php';

$db = Database::getConnection();

try {
    // Check if column exists first
    $checkStmt = $db->query("SHOW COLUMNS FROM products LIKE 'sizes'");
    if ($checkStmt->rowCount() > 0) {
        echo "sizes column already exists.";
    } else {
        $db->exec("ALTER TABLE products ADD COLUMN sizes TEXT DEFAULT NULL");
        echo "sizes column added successfully.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
