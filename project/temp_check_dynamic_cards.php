<?php
require 'config/Database.php';
$dbConn = Database::getConnection();

$overseasStmt = $dbConn->query("SELECT * FROM home_carousel_events WHERE status = 'published' AND show_in_overseas = 1 ORDER BY display_order ASC");
$newOverseasCards = $overseasStmt->fetchAll(PDO::FETCH_ASSOC);

$dynamicCards = [];
foreach ($newOverseasCards as $c) {
    $slug = $c['slug'] ?? '';
    $btnLink = $c['btn_url'] ?? '';
    $link = (!empty($btnLink)) ? $btnLink : 'home/events/' . $slug;
    
    $countryStr = $c['country'] ?? '';
    if (empty($countryStr)) $countryStr = $c['state'] ?? '';
    
    $type = (strtolower(trim($countryStr)) === 'india' || in_array(strtolower(trim($countryStr)), ['maharashtra', 'karnataka', 'tamil nadu', 'delhi', 'goa', 'kerala', 'rajasthan', 'gujarat', 'pune'])) ? 'national' : 'international';

    $dynamicCards[] = [
        'id' => (int)$c['id'] + 1000,
        'event_title' => $c['title'] ?? '', 
        'image' => $c['carousel_img'] ?: $c['hero_banner'] ?: '',
        'country' => strtoupper($countryStr),
        'city' => $c['state'] ?? '',
        'date' => $c['event_date'] ?? '',
        'link' => $link,
        'type' => $type,
        'country_or_state' => $countryStr
    ];
}
print_r($dynamicCards);
