<?php
require 'config/Database.php';
try {
    $db = Database::getConnection();
    $stmt = $db->query("SHOW TABLE STATUS LIKE 'home_event_cards'");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
