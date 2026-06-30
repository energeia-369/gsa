<?php
require 'config/Database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE 'sponsor%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
