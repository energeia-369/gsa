<?php
require 'config/Database.php';
try {
    $db = Database::getConnection();
    $db->exec('ALTER TABLE home_event_cards ADD COLUMN link VARCHAR(255) NULL AFTER country_or_state');
    echo 'Column added.';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
