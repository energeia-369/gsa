<?php
require 'config/Database.php';
$db = Database::getConnection();
$stmt = $db->query("SELECT id, event_title, event_type, module_type FROM home_event_cards");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
