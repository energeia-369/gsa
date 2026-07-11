<?php
require_once 'config/Database.php';
$db = Database::getConnection();
$stmt = $db->query("DESCRIBE delegates");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
