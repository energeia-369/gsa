<?php
require 'config/Database.php';
$pdo = Database::getConnection();

try {
    $pdo->exec("ALTER TABLE events ADD COLUMN location VARCHAR(500) NULL AFTER description");
    echo "Column location added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
