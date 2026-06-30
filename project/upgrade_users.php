<?php
require 'config/Database.php';
$db = Database::getConnection();
$db->query("UPDATE users SET membership_tier = 'elite', credits = 1500");
echo "All users updated to elite and given 1500 credits!";
