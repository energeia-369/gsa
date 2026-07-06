<?php
require 'config/Database.php';
$dbConn = Database::getConnection();

// Legacy fallback queries
$cardsStmt = $dbConn->query("SELECT * FROM home_event_cards WHERE status = 'active' ORDER BY id ASC");
$dbCarouselCards = $cardsStmt->fetchAll(PDO::FETCH_ASSOC);

$dynamicCards = [];
foreach ($dbCarouselCards as $c) {
    $isDynamic = $c['dynamic_page_enabled'] ?? 0;
    $btnLink = $c['button_link'] ?? '#';
    $slug = $c['slug'] ?? '';
    $link = ($isDynamic == 1 && $slug !== '') ? 'home-event.php?slug=' . $slug : $btnLink;
    
    $countryStr = $c['country'] ?? '';
    if(isset($c['country_or_state'])) $countryStr = $c['country_or_state'];
    $type = (strtolower(trim($countryStr)) === 'india' || in_array(strtolower(trim($countryStr)), ['maharashtra', 'karnataka', 'tamil nadu', 'delhi', 'goa', 'kerala', 'rajasthan', 'gujarat', 'pune'])) ? 'national' : 'international';

    $dynamicCards[] = [
        'id' => (int)$c['id'],
        'event_title' => $c['title'] ?? '', // For the custom banners
        'image' => $c['thumbnail'] ?? '',
        'country' => strtoupper($countryStr),
        'city' => $c['location'] ?? '',
        'date' => $c['event_date'] ?? '',
        'link' => $link,
        'type' => $type,
        'country_or_state' => $countryStr // Legacy JS compatibility
    ];
}

print_r($dynamicCards);
