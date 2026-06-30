<?php
require 'config/Database.php';
$pdo = Database::getConnection();

try {
    $pdo->exec("ALTER TABLE events ADD COLUMN gala_title VARCHAR(255) NULL AFTER logo_url");
    echo "Column gala_title added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
