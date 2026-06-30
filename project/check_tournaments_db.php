<?php
require 'config/Database.php';
$pdo = Database::getConnection();

try {
    print_r($pdo->query('DESCRIBE event_tournaments')->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
