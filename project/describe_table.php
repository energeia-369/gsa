<?php
require 'config/Database.php';
$db = Database::getConnection();
$stmt = $db->query('DESCRIBE home_carousel_events');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
