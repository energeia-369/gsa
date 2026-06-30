<?php
require 'config/Database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT slug, title, hero_banner_url FROM events");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
