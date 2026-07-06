<?php
require 'config/Database.php';
$db = Database::getConnection();
$stmt = $db->query("
    SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE REFERENCED_TABLE_NAME = 'home_carousel_events'
");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
