<?php
require 'config/Database.php';
$pdo = Database::getConnection();

try {
    $pdo->exec("ALTER TABLE events ADD COLUMN timer_start_date DATE NULL DEFAULT NULL AFTER start_date");
    echo "Column timer_start_date added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
