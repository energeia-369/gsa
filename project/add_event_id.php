<?php
require 'config/Database.php';
$db = Database::getConnection();
try {
    $db->exec('ALTER TABLE delegates ADD COLUMN event_id INT NULL');
    echo 'Column added';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
