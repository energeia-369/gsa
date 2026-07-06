<?php
require 'config/Database.php';
$db = Database::getConnection();
$stmt = $db->query('SELECT * FROM home_event_cards');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
