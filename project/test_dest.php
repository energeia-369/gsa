<?php
require 'config/Database.php';
try {
    $db = Database::getConnection();
    $stmt = $db->query('SELECT 1 FROM custom_destinations LIMIT 1');
    echo 'Table exists.';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
