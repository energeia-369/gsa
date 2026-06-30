<?php
require 'config/Database.php';
$pdo = Database::getConnection();

try {
    $pdo->exec("ALTER TABLE events ADD COLUMN schedule_data TEXT NULL AFTER description");
    echo "Column schedule_data added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
