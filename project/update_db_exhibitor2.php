<?php
require 'config/Database.php';
$db = Database::getConnection();
$db->exec("ALTER TABLE exhibitors 
           ADD COLUMN user_id bigint(20) DEFAULT NULL, 
           ADD COLUMN approval_status enum('pending','approved','rejected') DEFAULT 'pending', 
           ADD COLUMN fee_amount decimal(10,2) DEFAULT 0.00");
echo "Done";
