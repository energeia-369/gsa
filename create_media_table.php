<?php
require_once __DIR__ . '/project/config/Database.php';

try {
    $db = Database::getConnection();
    
    $query = "
    CREATE TABLE IF NOT EXISTS media_hub (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL,
        video_link VARCHAR(255) NOT NULL,
        thumbnail VARCHAR(255) NOT NULL,
        tournament_name VARCHAR(255),
        stadium VARCHAR(255),
        duration VARCHAR(50),
        views VARCHAR(50) DEFAULT '0',
        status VARCHAR(50),
        date_time VARCHAR(100),
        short_description TEXT,
        visibility BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $db->exec($query);
    echo "media_hub table created successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
