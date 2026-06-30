<?php
require 'config/Database.php';
$db = (new Database())->getConnection();
try {
    $db->exec('ALTER TABLE events ADD COLUMN locations_data LONGTEXT NULL;');
    echo 'Column added successfully.';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
