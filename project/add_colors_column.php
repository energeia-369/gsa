<?php
require_once __DIR__ . '/config/Database.php';
try {
    $db = Database::getConnection();
    $stmt = $db->query("SHOW COLUMNS FROM products LIKE 'colors'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE products ADD COLUMN colors VARCHAR(255) DEFAULT NULL");
        echo "Added colors column.\n";
    } else {
        echo "Colors column already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
