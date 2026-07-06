<?php
require 'config/Database.php';
$db = Database::getConnection();
try {
    $db->exec('ALTER TABLE events ADD COLUMN custom_html TEXT NULL');
    echo 'Column added successfully';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
