<?php
require 'config/Database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT custom_html FROM home_carousel_events WHERE slug='gsa-thailand-2026'");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
file_put_contents('custom_html_output.html', $row['custom_html']);
