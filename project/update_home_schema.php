<?php
require 'config/Database.php';
$pdo = Database::getConnection();

$cols = [
    'sports_data' => 'LONGTEXT',
    'sponsors_data' => 'LONGTEXT',
    'exhibitor_data' => 'LONGTEXT',
    'locations_data' => 'LONGTEXT',
    'gala_passes_data' => 'LONGTEXT',
    'schedule_data' => 'LONGTEXT',
    'custom_html' => 'LONGTEXT',
    'end_date' => 'DATE',
    'timer_start_date' => 'DATE',
    'gala_venue' => 'VARCHAR(255)',
    'gala_date' => 'VARCHAR(100)',
    'gala_time' => 'VARCHAR(100)',
    'gala_description' => 'TEXT',
    'gala_title' => 'VARCHAR(255)',
    'delegate_fee' => 'DECIMAL(10,2)',
    'delegate_currency' => 'VARCHAR(10)'
];

echo "Updating home_carousel_events schema...\n";

foreach ($cols as $col => $type) {
    try {
        $pdo->exec("ALTER TABLE home_carousel_events ADD COLUMN $col $type NULL");
        echo "Added $col\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "Column $col already exists.\n";
        } else {
            echo "Error adding $col: " . $e->getMessage() . "\n";
        }
    }
}
echo "Schema update complete.\n";
