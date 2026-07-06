<?php
require 'config/Database.php';
$db = Database::getConnection();
$stmt = $db->query("SELECT * FROM home_event_cards WHERE link LIKE '%2027%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt2 = $db->query("SELECT * FROM home_carousel_events WHERE btn_url LIKE '%2027%'");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
