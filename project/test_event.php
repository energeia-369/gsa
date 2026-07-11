<?php
require_once __DIR__ . '/config/Database.php';
$pdo = Database::getConnection();
$stmt = $pdo->prepare("SELECT sports_data FROM home_carousel_events WHERE slug='gsa-thailand-2026'");
$stmt->execute();
$event = $stmt->fetch();
$sports = json_decode($event['sports_data'] ?? '[]', true) ?? [];
echo "Count: " . count($sports) . "\n";
print_r($sports);
