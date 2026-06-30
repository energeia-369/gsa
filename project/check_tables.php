<?php
require 'config/Database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("DESCRIBE products");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt2 = $db->query("DESCRIBE orders");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
