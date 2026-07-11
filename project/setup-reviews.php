<?php
require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getConnection();
    
    $query = "CREATE TABLE IF NOT EXISTS client_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        name VARCHAR(100) NOT NULL,
        role VARCHAR(100) NULL,
        rating INT NOT NULL DEFAULT 5,
        review_text TEXT NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $db->exec($query);
    echo "Table client_reviews created successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
