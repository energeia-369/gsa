<?php
require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getConnection();
    
    // Check if column exists first to avoid errors if run multiple times
    $stmt = $db->query("SHOW COLUMNS FROM tournaments LIKE 'created_at'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE tournaments ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP");
        echo "Column 'created_at' added successfully to 'tournaments' table.\n";
    } else {
        echo "Column 'created_at' already exists in 'tournaments' table.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
