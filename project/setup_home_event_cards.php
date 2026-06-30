<?php
require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getConnection();

    $sql = "CREATE TABLE IF NOT EXISTS home_event_cards (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_title VARCHAR(255) NOT NULL,
        event_type ENUM('overseas', 'state') NOT NULL,
        image VARCHAR(255) NOT NULL,
        event_date VARCHAR(100) NOT NULL,
        city VARCHAR(100) NOT NULL,
        country_or_state VARCHAR(100) NOT NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $db->exec($sql);
    echo "Table 'home_event_cards' created successfully or already exists.\n";

} catch (Exception $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>
