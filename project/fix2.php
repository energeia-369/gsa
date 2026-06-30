<?php
require 'config/Database.php';
$db = Database::getConnection();
$db->exec("INSERT INTO system_settings (setting_key, setting_value) VALUES ('exhibitor_fee', '5000') ON DUPLICATE KEY UPDATE setting_value=setting_value");
echo "Done";
