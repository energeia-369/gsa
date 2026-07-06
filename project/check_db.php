<?php
require 'config/Database.php';
$pdo = Database::getConnection();

$stmt = $pdo->query("SELECT * FROM gsa_carousel_events");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
