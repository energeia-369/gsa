<?php
require 'config/Database.php';
$pdo = Database::getConnection();

try {
    $pdo->exec("ALTER TABLE events ADD COLUMN exhibitor_data LONGTEXT NULL AFTER sponsors_data");
    echo "Column exhibitor_data added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
