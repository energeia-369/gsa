<?php
require 'config/Database.php';
$pdo = Database::getConnection();

try {
    $pdo->exec("ALTER TABLE events ADD COLUMN sports_data TEXT NULL AFTER schedule_data");
    echo "Column sports_data added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
