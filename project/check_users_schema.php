<?php
require 'c:\xampp\htdocs\Mithraa_E_Project\project\config\Database.php';
$db = Database::getConnection();
$stmt = $db->query("DESCRIBE users");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
