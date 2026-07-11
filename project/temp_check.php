<?php
require 'config/Database.php';
$pdo = Database::getConnection();
$stmt = $pdo->query("SHOW COLUMNS FROM home_carousel_events");
$cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
print_r($cols);
