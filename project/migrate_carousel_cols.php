<?php
require_once __DIR__ . '/config/Database.php';

try {
    $pdo = Database::getConnection();
    
    // Add show_on_home and show_on_gsa
    $sql = "ALTER TABLE home_carousel_events 
            ADD COLUMN show_on_home TINYINT(1) DEFAULT 1,
            ADD COLUMN show_on_gsa TINYINT(1) DEFAULT 0";
            
    $pdo->exec($sql);
    echo "Columns added successfully.\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Columns already exist.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>
