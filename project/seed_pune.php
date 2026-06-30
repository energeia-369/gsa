<?php
require 'c:/xampp/htdocs/Mithraa_E_Project/project/config/Database.php';
$db = Database::getConnection();
$pricing = json_encode([
    'pune' => [
        'standard' => ['size' => '3m x 3m', 'price' => 30000],
        'premium' => ['size' => '6m x 3m', 'price' => 60000],
        'corner' => ['size' => '6m x 6m', 'price' => 90000],
        'pavilion' => ['size' => 'Custom', 'price' => '2,00,000+']
    ]
]);
$stmt = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('exhibitor_pricing', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
$stmt->execute([$pricing, $pricing]);
echo "Pricing seeded for pune.\n";
