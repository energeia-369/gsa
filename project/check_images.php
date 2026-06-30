<?php
require 'config/Database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT id, name, image_url FROM products");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
