<?php
require 'config/Database.php';
$db = Database::getConnection();
$stmt = $db->query("SELECT * FROM home_carousel_events WHERE title LIKE '%2027%' OR slug LIKE '%2027%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt = $db->query("SELECT * FROM home_event_cards WHERE event_title LIKE '%2027%' OR link LIKE '%2027%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
