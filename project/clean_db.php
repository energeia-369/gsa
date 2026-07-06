<?php
require_once 'config/Database.php';
$db = Database::getConnection();
$db->exec("DELETE FROM custom_destinations WHERE country IN ('Test', 'A', 'Deleted')");
echo "Cleaned DB";
