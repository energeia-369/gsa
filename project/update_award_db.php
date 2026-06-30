<?php
require 'config/Database.php';
$db = Database::getConnection();
try {
    $db->query("ALTER TABLE award_registrations ADD COLUMN coupon_code VARCHAR(50) NULL AFTER gst_amount");
} catch(Exception $e) {}
try {
    $db->query("ALTER TABLE award_registrations ADD COLUMN nxl_redeemed INT DEFAULT 0 AFTER discount_amount");
} catch(Exception $e) {}
try {
    // Check if user_id exists first. create_award_tables has user_id
    $db->query("ALTER TABLE award_registrations ADD COLUMN user_id INT NULL AFTER id");
} catch(Exception $e) {}
echo "Columns added.";
