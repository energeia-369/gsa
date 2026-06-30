<?php
require 'config/Database.php';
$db = Database::getConnection();
$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Tables:\n";
print_r($tables);

$exhibitor_schema = $db->query("SHOW CREATE TABLE exhibitors")->fetch(PDO::FETCH_ASSOC);
echo "Exhibitors schema:\n";
print_r($exhibitor_schema);
