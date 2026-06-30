<?php
require 'c:/xampp/htdocs/Mithraa_E_Project/project/config/Database.php';
$db = Database::getConnection();
$stmt = $db->query('SELECT DISTINCT membership_tier FROM users');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
