<?php
require 'config/Database.php';
$pdo = Database::getConnection();

echo "Starting migration...\n";

// Fetch from old events table
$stmt = $pdo->query("SELECT * FROM events");
$oldEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

$migratedCount = 0;

foreach ($oldEvents as $event) {
    // Check if it already exists in the new table by slug
    $check = $pdo->prepare("SELECT id FROM gsa_carousel_events WHERE slug = ?");
    $check->execute([$event['slug']]);
    if ($check->rowCount() > 0) {
        continue;
    }

    // Map status
    $newStatus = ($event['status'] === 'active') ? 'published' : 'draft';

    // Try to extract a prize pool from sports_data json if it exists
    $prizePool = '';
    if (!empty($event['sports_data'])) {
        $sports = json_decode($event['sports_data'], true);
        if (is_array($sports) && count($sports) > 0) {
            if (isset($sports[0]['prize'])) {
                $currency = $sports[0]['prize_currency'] ?? 'USD';
                $prizePool = $currency . ' ' . $sports[0]['prize'];
            }
        }
    }

    // Insert into new gsa_carousel_events
    $insert = $pdo->prepare("INSERT INTO gsa_carousel_events (
        tournament_name, 
        sport_category, 
        venue, 
        hero_banner, 
        description, 
        reg_status, 
        prize_pool, 
        event_date, 
        status, 
        seo_title, 
        slug,
        country
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // We use a generic 'Multi-Sport' category since old events were multi-sport festivals
    $insert->execute([
        $event['title'],
        'Multi-Sport Festival',
        $event['location'],
        $event['hero_banner_url'],
        $event['description'],
        'open',
        $prizePool,
        $event['start_date'],
        $newStatus,
        $event['title'],
        $event['slug'],
        'Multiple Locations'
    ]);
    
    $migratedCount++;
    echo "Migrated: " . $event['title'] . "\n";
}

echo "Migration complete! Migrated $migratedCount events.";
