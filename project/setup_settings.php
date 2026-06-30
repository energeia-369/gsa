<?php
require 'config/Database.php';
$db = Database::getConnection();
try {
    $db->exec("CREATE TABLE IF NOT EXISTS system_settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value VARCHAR(255)
    )");
    $db->exec("INSERT IGNORE INTO system_settings (setting_key, setting_value) VALUES ('event_fee', '0')");
    echo "Settings table created and seeded.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
