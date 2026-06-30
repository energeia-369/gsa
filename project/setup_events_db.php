<?php
require_once __DIR__ . '/config/Database.php';

try {
    $pdo = Database::getConnection();

    // 1. events
    $pdo->exec("CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        slug VARCHAR(255) UNIQUE NOT NULL,
        title VARCHAR(255) NOT NULL,
        status ENUM('active', 'inactive', 'draft') DEFAULT 'draft',
        hero_banner_url VARCHAR(255),
        logo_url VARCHAR(255),
        description TEXT,
        start_date DATE,
        end_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 2. event_locations
    $pdo->exec("CREATE TABLE IF NOT EXISTS event_locations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        image_url VARCHAR(255),
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 3. event_tournaments
    $pdo->exec("CREATE TABLE IF NOT EXISTS event_tournaments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        dates VARCHAR(255),
        registration_link VARCHAR(255),
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 4. event_countries
    $pdo->exec("CREATE TABLE IF NOT EXISTS event_countries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        country_name VARCHAR(255) NOT NULL,
        flag_url VARCHAR(255),
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 5. event_sports
    $pdo->exec("CREATE TABLE IF NOT EXISTS event_sports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        sport_category VARCHAR(100) NOT NULL, -- Motorsports, Water Sports, Esports
        name VARCHAR(255) NOT NULL,
        image_url VARCHAR(255),
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 6. event_pricing
    $pdo->exec("CREATE TABLE IF NOT EXISTS event_pricing (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        tier_name VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        benefits_json TEXT, -- JSON array of benefits
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 7. event_registration_modules
    $pdo->exec("CREATE TABLE IF NOT EXISTS event_registration_modules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        module_name VARCHAR(255) NOT NULL,
        link VARCHAR(255) NOT NULL,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 8. event_payment_gateways
    $pdo->exec("CREATE TABLE IF NOT EXISTS event_payment_gateways (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        gateway_name VARCHAR(100) NOT NULL,
        config_json TEXT, -- Optional JSON config
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 9. event_user_journey
    $pdo->exec("CREATE TABLE IF NOT EXISTS event_user_journey (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        step_number INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 10. event_security
    $pdo->exec("CREATE TABLE IF NOT EXISTS event_security (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        policy_name VARCHAR(255) NOT NULL,
        details TEXT,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 11. event_technology_stack
    $pdo->exec("CREATE TABLE IF NOT EXISTS event_technology_stack (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        tech_name VARCHAR(255) NOT NULL,
        description TEXT,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 12. event_invited_participants
    $pdo->exec("CREATE TABLE IF NOT EXISTS event_invited_participants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        category VARCHAR(100) NOT NULL,
        name VARCHAR(255) NOT NULL,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");


    // ---------------------------------------------------------
    // Seed Sample Data (USA, Thailand, Malaysia, Indonesia, India)
    // ---------------------------------------------------------
    $sample_events = [
        ['slug' => 'gsa-pune-2026', 'title' => 'GSA Pune 2026', 'status' => 'active', 'hero_banner_url' => 'assets/images/gsa-pune-bg.jpg'],
        ['slug' => 'gsa-thailand-2026', 'title' => 'GSA Thailand 2026', 'status' => 'active', 'hero_banner_url' => 'assets/images/gsa-thailand-bg.jpg'],
        ['slug' => 'gsa-malaysia-2026', 'title' => 'GSA Malaysia 2026', 'status' => 'active', 'hero_banner_url' => 'assets/images/gsa-malaysia-bg.jpg'],
        ['slug' => 'gsa-indonesia-2026', 'title' => 'GSA Indonesia 2026', 'status' => 'active', 'hero_banner_url' => 'assets/images/gsa-indonesia-bg.jpg'],
        ['slug' => 'gsa-usa-2026', 'title' => 'GSA USA 2026', 'status' => 'active', 'hero_banner_url' => 'assets/images/gsa-usa-bg.jpg'],
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO events (slug, title, status, hero_banner_url) VALUES (?, ?, ?, ?)");
    foreach ($sample_events as $ev) {
        $stmt->execute([$ev['slug'], $ev['title'], $ev['status'], $ev['hero_banner_url']]);
    }

    echo "Event management tables created and seeded successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
