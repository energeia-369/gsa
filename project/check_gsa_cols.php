<?php
require 'config/Database.php';
$pdo = Database::getConnection();
$stmt = $pdo->query('SHOW COLUMNS FROM gsa_carousel_events');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
