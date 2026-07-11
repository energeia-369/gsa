<?php
require 'config/Database.php';
$pdo = Database::getConnection();
try {
    $pdo->exec("ALTER TABLE home_carousel_events ADD COLUMN badge_text VARCHAR(255) DEFAULT NULL AFTER location");
    echo "Column badge_text added.";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
