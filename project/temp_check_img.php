<?php
require 'config/Database.php';
$pdo = Database::getConnection();
$stmt = $pdo->query("SELECT id, title, carousel_img, hero_banner, home_banner_img FROM home_carousel_events WHERE title LIKE '%Thailand%' OR country LIKE '%Thailand%'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
