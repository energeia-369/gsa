<?php
require 'c:/xampp/htdocs/Mithraa_E_Project/project/config/Database.php';
$db = Database::getConnection();
try {
    $db->exec("ALTER TABLE visitor_passes ADD COLUMN razorpay_payment_id VARCHAR(100) NULL");
    echo "Added column.\n";
} catch (Exception $e) {
    echo "Column may already exist: " . $e->getMessage() . "\n";
}
?>
