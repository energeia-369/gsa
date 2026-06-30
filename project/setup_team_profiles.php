<?php
require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS team_profiles (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      qualification VARCHAR(100),
      role VARCHAR(100) NOT NULL,
      description TEXT,
      image VARCHAR(255),
      status ENUM('active','inactive') DEFAULT 'active',
      display_order INT DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $db->exec($sql);
    echo "Table 'team_profiles' created successfully.\n";
} catch (Exception $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>
