<?php
require_once __DIR__ . '/config/Database.php';

try {
    $pdo = Database::getConnection();
    
    $sql = "ALTER TABLE home_carousel_events 
        ADD COLUMN stat1_val VARCHAR(255) DEFAULT '',
        ADD COLUMN stat1_label VARCHAR(255) DEFAULT '',
        ADD COLUMN stat2_val VARCHAR(255) DEFAULT '',
        ADD COLUMN stat2_label VARCHAR(255) DEFAULT '',
        ADD COLUMN stat3_val VARCHAR(255) DEFAULT '',
        ADD COLUMN stat3_label VARCHAR(255) DEFAULT '',
        ADD COLUMN stat4_val VARCHAR(255) DEFAULT '',
        ADD COLUMN stat4_label VARCHAR(255) DEFAULT '';";
        
    $pdo->exec($sql);
    echo "Columns added successfully";
} catch (Exception $e) {
    echo "Error adding columns: " . $e->getMessage();
}
?>
