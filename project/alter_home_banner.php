<?php
require_once __DIR__ . '/config/Database.php';

try {
    $pdo = Database::getConnection();
    
    $stmt = $pdo->query("SHOW COLUMNS FROM home_carousel_events LIKE 'home_banner_img'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE home_carousel_events ADD COLUMN home_banner_img VARCHAR(255) DEFAULT NULL AFTER mobile_banner");
        echo "Added home_banner_img column.\n";
    }

    $stmt = $pdo->query("SHOW COLUMNS FROM home_carousel_events LIKE 'show_home_banner'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE home_carousel_events ADD COLUMN show_home_banner TINYINT(1) DEFAULT 0 AFTER show_on_gsa");
        echo "Added show_home_banner column.\n";
    }

    echo "Schema update completed.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
