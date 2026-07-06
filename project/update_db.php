<?php
require_once 'config/Database.php';
$db = Database::getConnection();
try {
    $db->exec("ALTER TABLE home_event_cards ADD COLUMN module_type VARCHAR(50) DEFAULT 'home_carousel'");
    echo "Column added";
} catch (Exception $e) {
    echo "Error or column exists: " . $e->getMessage();
}
