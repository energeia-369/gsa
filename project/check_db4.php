<?php
require 'config/Database.php';
try {
    $db = Database::getConnection();
    $stmt = $db->query('SELECT * FROM custom_destinations');
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
