<?php
require_once __DIR__ . '/config/Database.php';

try {
    $pdo = Database::getConnection();
    
    // Migrate from home_event_cards
    $stmt = $pdo->query("SELECT * FROM home_event_cards WHERE status='active'");
    $homeCards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($homeCards as $card) {
        $category = 'Nexus'; // default
        if (strpos(strtolower($card['event_title']), 'maytriya') !== false) {
            $category = 'Maytriya';
        }
        
        $insert = $pdo->prepare("INSERT INTO home_carousel_events 
            (title, category, country, state, hero_banner, carousel_img, status, show_on_home, show_on_gsa, slug) 
            VALUES (?, ?, ?, ?, ?, ?, 'published', 1, 0, ?)");
        
        $slug = 'migrated-home-' . uniqid();
        
        // try to insert
        try {
            $insert->execute([
                $card['event_title'],
                $category,
                $card['country_or_state'] ?? '',
                $card['city'] ?? '',
                $card['image'] ?? '',
                $card['image'] ?? '',
                $slug
            ]);
        } catch(Exception $e) {
            echo "Error inserting home card: " . $e->getMessage() . "\n";
        }
    }
    
    echo "Migration complete.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
