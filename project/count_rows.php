<?php
require 'config/Database.php';
$db = Database::getConnection();
echo $db->query('SELECT COUNT(*) FROM home_carousel_events')->fetchColumn();
?>
