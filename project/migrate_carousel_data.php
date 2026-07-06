<?php
require_once __DIR__ . '/config/Database.php';

try {
    $pdo = Database::getConnection();
    
    // Migrate from home_event_cards
    $stmt = $pdo->query("SELECT * FROM home_event_cards WHERE status='active'");
    $homeCards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($homeCards as $card) {
        $category = 'Nexus'; // default
        if (strpos(strtolower($card['title']), 'maytriya') !== false) {
            $category = 'Maytriya';
        }
        
        $insert = $pdo->prepare("INSERT INTO home_carousel_events 
            (title, category, country, state, hero_banner, carousel_img, status, show_on_home, show_on_gsa, slug) 
            VALUES (?, ?, ?, ?, ?, ?, 'published', 1, 0, ?)");
        
        $slug = $card['slug'] ?? strtolower(str_replace(' ', '-', $card['title']));
        
        // try to insert, ignore duplicates
        try {
            $insert->execute([
                $card['title'],
                $category,
                $card['country'] ?? '',
                $card['location'] ?? '',
                $card['thumbnail'] ?? '',
                $card['thumbnail'] ?? '',
                $slug
            ]);
        } catch(Exception $e) {}
    }
    
    // Migrate from gsa_carousel_events
    $stmtGsa = $pdo->query("SELECT * FROM gsa_carousel_events WHERE status='published'");
    $gsaCards = $stmtGsa->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($gsaCards as $card) {
        $insert = $pdo->prepare("INSERT INTO home_carousel_events 
            (title, short_desc, description, category, country, state, hero_banner, carousel_img, status, show_on_home, show_on_gsa, slug) 
            VALUES (?, ?, ?, 'GSA', ?, ?, ?, ?, 'published', 0, 1, ?)");
            
        $slug = $card['slug'] ?? strtolower(str_replace(' ', '-', $card['tournament_name']));
        
        try {
            $insert->execute([
                $card['tournament_name'],
                $card['description'] ?? '',
                $card['description'] ?? '',
                $card['country'] ?? '',
                $card['state'] ?? '',
                $card['hero_banner'] ?? '',
                $card['carousel_img'] ?? '',
                $slug
            ]);
        } catch(Exception $e) {}
    }
    
    echo "Migration complete.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
