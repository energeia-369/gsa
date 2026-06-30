<?php
require 'config/Database.php';
try {
    $db = Database::getConnection();
    
    // Pune
    $stmt1 = $db->prepare("INSERT INTO home_event_cards (event_title, event_type, image, event_date, city, country_or_state, link, status) VALUES ('Pune Championship Edition', 'state', 'assets/images/Pune card.png', '6-13 Oct 2026', 'Pune', 'MAHARASHTRA', 'gsa-pune-2026.php', 'active')");
    $stmt1->execute();
    
    // Thailand
    $stmt2 = $db->prepare("INSERT INTO home_event_cards (event_title, event_type, image, event_date, city, country_or_state, link, status) VALUES ('Thailand Edition', 'overseas', 'assets/images/Thailand Card.png', 'Sep - Nov 2026', 'Phuket', 'THAILAND', 'https://energeia369.com/thailand-event/', 'active')");
    $stmt2->execute();
    
    echo 'Cards inserted!';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
