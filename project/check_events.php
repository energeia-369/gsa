<?php
require 'config/Database.php';
$db = Database::getConnection();
$stmt = $db->query('SELECT id, title, slug, btn_url, show_in_overseas FROM home_carousel_events');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
