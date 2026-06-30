<?php
require 'config/Database.php';
try {
    $db = Database::getConnection();
    $db->exec("ALTER TABLE exhibitors ADD COLUMN custom_build_details TEXT NULL");
    echo "Column added successfully";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column already exists";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
