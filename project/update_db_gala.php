<?php
require 'config/Database.php';
$pdo = Database::getConnection();

try {
    $pdo->exec("ALTER TABLE events 
        ADD COLUMN gala_venue VARCHAR(255) NULL AFTER timer_start_date,
        ADD COLUMN gala_date VARCHAR(255) NULL AFTER gala_venue,
        ADD COLUMN gala_time VARCHAR(255) NULL AFTER gala_date,
        ADD COLUMN gala_description TEXT NULL AFTER gala_time
    ");
    echo "Columns added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
