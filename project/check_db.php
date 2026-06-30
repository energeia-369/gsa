<?php
require 'config/Database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT slug, title, hero_banner_url FROM events WHERE slug = 'gsa-malaysia-2026'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));