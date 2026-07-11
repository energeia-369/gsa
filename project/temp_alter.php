<?php
require 'config/Database.php';
$pdo = Database::getConnection();
try {
    $pdo->exec("ALTER TABLE home_carousel_events ADD COLUMN location TEXT AFTER state");
    echo "Column added.";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
