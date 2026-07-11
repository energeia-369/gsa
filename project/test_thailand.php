<?php
require_once __DIR__ . '/config/Database.php';
$pdo = Database::getConnection();

echo "From events table:\n";
$stmt1 = $pdo->query("SELECT title, exhibitor_data FROM events WHERE title LIKE '%Thailand%'");
print_r($stmt1->fetchAll(PDO::FETCH_ASSOC));

echo "\nFrom home_carousel_events table:\n";
$stmt2 = $pdo->query("SELECT title, exhibitor_data FROM home_carousel_events WHERE title LIKE '%Thailand%'");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
