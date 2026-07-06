<?php
require_once 'config/Database.php';
$db = Database::getConnection();

echo "HOME_CAROUSEL_DESTINATIONS:\n";
try {
    $q = $db->query("SELECT * FROM home_carousel_destinations");
    print_r($q->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nHOME_CAROUSEL_EVENTS:\n";
try {
    $q2 = $db->query("SELECT * FROM home_carousel_events");
    print_r($q2->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
