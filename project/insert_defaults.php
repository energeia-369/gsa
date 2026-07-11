<?php
require_once __DIR__ . '/config/Database.php';

$pdo = Database::getConnection();

$defaults = [
    // International
    ['Singapore Edition', 'SINGAPORE', 'Central Core', 'Oct 2026', 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800&q=80', 'Marina Bay', 'international'],
    ['Dubai Expo', 'DUBAI', 'UAE', 'Nov 2026', 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&q=80', 'Downtown', 'international'],
    ['London Cup', 'LONDON', 'UK', 'Dec 2026', 'https://images.unsplash.com/photo-1529655683826-aba9b3e77383?w=800&q=80', 'Wembley', 'international'],
    ['NY Masters', 'NEW YORK', 'USA', 'Jan 2027', 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800&q=80', 'Manhattan', 'international'],
    // National
    ['Mumbai Finals', 'INDIA', 'Maharashtra', 'Oct 2026', 'https://images.unsplash.com/photo-1529253355930-ddbe423a2ac7?w=800&q=80', 'Bandra', 'national'],
    ['Delhi Open', 'INDIA', 'Delhi', 'Nov 2026', 'https://images.unsplash.com/photo-1587474260584-136574528ed5?w=800&q=80', 'CP', 'national'],
    ['Bangalore Series', 'INDIA', 'Karnataka', 'Dec 2026', 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=800&q=80', 'Whitefield', 'national'],
    ['Goa Retreat', 'INDIA', 'Goa', 'Jan 2027', 'https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=800&q=80', 'Panaji', 'national'],
];

foreach ($defaults as $idx => $d) {
    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM home_carousel_events WHERE title = ? AND show_in_overseas = 1");
    $stmt->execute([$d[0]]);
    if ($stmt->rowCount() == 0) {
        $insert = $pdo->prepare("INSERT INTO home_carousel_events 
            (title, country, state, event_date, carousel_img, show_in_overseas, status, display_order, slug) 
            VALUES (?, ?, ?, ?, ?, 1, 'published', ?, ?)");
        $slug = strtolower(str_replace(' ', '-', $d[0]));
        $insert->execute([$d[0], $d[1], $d[2], $d[3], $d[4], $idx, $slug]);
        echo "Inserted {$d[0]}\n";
    } else {
        echo "Skipped {$d[0]} (already exists)\n";
    }
}
echo "Done.\n";
