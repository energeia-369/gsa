<?php
require 'config/Database.php';
$pdo = Database::getConnection();

try {
    $pdo->exec("ALTER TABLE events ADD COLUMN sponsors_data LONGTEXT NULL AFTER sports_data");
    echo "Column sponsors_data added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
