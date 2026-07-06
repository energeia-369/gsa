<?php
require 'config/Database.php';

$db = Database::getConnection();

try {
    $db->exec("SET FOREIGN_KEY_CHECKS=0");
    $db->exec("DROP TABLE IF EXISTS home_carousel_dynamic_pages");
    $db->exec("DROP TABLE IF EXISTS home_carousel_events");
    $db->exec("DROP TABLE IF EXISTS gsa_carousel_events");
    $db->exec("SET FOREIGN_KEY_CHECKS=1");

    // 1. Create home_carousel_events table
    $db->exec("
        CREATE TABLE IF NOT EXISTS home_carousel_events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            subtitle VARCHAR(255),
            short_desc TEXT,
            description LONGTEXT,
            category VARCHAR(50),
            country VARCHAR(100),
            state VARCHAR(100),
            hero_banner VARCHAR(255),
            carousel_img VARCHAR(255),
            mobile_banner VARCHAR(255),
            gallery_data JSON,
            btn_text VARCHAR(100) DEFAULT 'Explore',
            btn_url VARCHAR(255),
            event_date DATE,
            display_order INT DEFAULT 0,
            is_featured BOOLEAN DEFAULT FALSE,
            status VARCHAR(50) DEFAULT 'draft',
            seo_title VARCHAR(255),
            seo_desc TEXT,
            seo_keywords TEXT,
            slug VARCHAR(255) UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "home_carousel_events table recreated.\n";

    // 2. Create gsa_carousel_events table
    $db->exec("
        CREATE TABLE IF NOT EXISTS gsa_carousel_events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tournament_name VARCHAR(255) NOT NULL,
            sport_category VARCHAR(100),
            country VARCHAR(100),
            state VARCHAR(100),
            venue VARCHAR(255),
            hero_banner VARCHAR(255),
            carousel_img VARCHAR(255),
            mobile_banner VARCHAR(255),
            description LONGTEXT,
            gallery_data JSON,
            reg_status VARCHAR(50) DEFAULT 'open',
            reg_url VARCHAR(255),
            prize_pool VARCHAR(255),
            schedule_data JSON,
            rules_data LONGTEXT,
            event_date DATE,
            display_order INT DEFAULT 0,
            status VARCHAR(50) DEFAULT 'draft',
            seo_title VARCHAR(255),
            seo_desc TEXT,
            seo_keywords TEXT,
            slug VARCHAR(255) UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "gsa_carousel_events table recreated.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
