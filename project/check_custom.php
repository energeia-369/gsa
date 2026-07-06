<?php
require 'config/Database.php';
$db = Database::getConnection();
$stmt = $db->query("SELECT * FROM custom_destinations");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
