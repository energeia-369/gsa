<?php
require 'config/Database.php';
$db = Database::getConnection();
$db->exec("UPDATE custom_destinations SET link = 'gsa-pune-2026.php' WHERE link = 'gsa-pune-2027.php'");
echo 'Updated';
