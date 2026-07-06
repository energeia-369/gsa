<?php
$sourceFile = 'c:\\xampp\\htdocs\\Mithraa_E_Project\\project\\event-details.php';
$targetFile = 'c:\\xampp\\htdocs\\Mithraa_E_Project\\project\\home-event-details.php';

$content = file_get_contents($sourceFile);

// 1. Change the table from 'events' to 'home_carousel_events'
$content = str_replace(
    'SELECT * FROM events WHERE slug = ?',
    'SELECT * FROM home_carousel_events WHERE slug = ?',
    $content
);

// 2. Change hero_banner_url to hero_banner
$content = str_replace(
    '$event[\'hero_banner_url\']',
    '$event[\'hero_banner\']',
    $content
);

file_put_contents($targetFile, $content);
echo "Frontend layout ported successfully.\n";
?>
