<?php
require 'config/Database.php';
$db = Database::getConnection();
$stmt = $db->query('SHOW TABLES');
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
?>
