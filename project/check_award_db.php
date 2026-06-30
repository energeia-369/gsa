<?php
require_once __DIR__ . '/config/Database.php';
$db = Database::getConnection();
$tables = $db->query('SHOW TABLES LIKE "award%"')->fetchAll(PDO::FETCH_COLUMN);
echo implode(', ', $tables);
echo "\n";

// Also show columns of award_registrations
try {
    $cols = $db->query('DESCRIBE award_registrations')->fetchAll(PDO::FETCH_COLUMN);
    echo "award_registrations columns: " . implode(', ', $cols) . "\n";
} catch(Exception $e) {
    echo "Table not found: " . $e->getMessage() . "\n";
}
