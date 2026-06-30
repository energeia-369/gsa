<?php
require 'config/Database.php';
try {
    $db = Database::getConnection();
    $db->exec("CREATE TABLE IF NOT EXISTS booth_options (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        price DECIMAL(10,2) NOT NULL DEFAULT 0.00
    )");
    
    // Insert defaults only if table is empty
    $count = $db->query("SELECT COUNT(*) FROM booth_options")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO booth_options (name, price) VALUES 
            ('Standard (3x3m)', 5000), 
            ('Premium (6x3m)', 10000), 
            ('Custom Built', 15000)
        ");
    }
    echo "Booth options table created and populated successfully";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
