<?php
require 'config/Database.php';
try {
    $db = Database::getConnection();
    $stmt = $db->prepare("INSERT INTO home_event_cards (event_title, event_type, image, event_date, city, country_or_state, link, status) VALUES ('Malaysia Edition', 'overseas', 'assets/images/maytriya card.png', '20-22 Nov 2026', 'Kuala Lumpur', 'MALAYSIA', 'https://energeia369.com/malaysia', 'active')");
    $stmt->execute();
    echo 'Card inserted! ';

    $stmt2 = $db->query("SELECT * FROM home_event_cards");
    print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
