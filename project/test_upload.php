<?php
$url = 'http://localhost/Mithraa_E_Project/project/api/index.php/settings/sponsors';

// Create a dummy image
file_put_contents('dummy.png', 'fake image content');

$cfile = new CURLFile('dummy.png', 'image/png', 'dummy.png');

$post = [
    'sponsor_title' => $cfile
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
echo "Response: " . $response . "\n";
curl_close($ch);

unlink('dummy.png');

// Check DB
require 'config/Database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE 'sponsor%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
