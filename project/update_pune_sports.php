<?php
require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getConnection();
    
    $puneSports = [
        [
            "title" => "Badminton Championship",
            "categories" => "U14, U18, Open, Doubles",
            "price_individual" => 1500,
            "price_pair" => 2500,
            "price_team" => 0
        ],
        [
            "title" => "Table Tennis Championship",
            "categories" => "U14, U18, Open, Doubles",
            "price_individual" => 1200,
            "price_pair" => 2000,
            "price_team" => 0
        ],
        [
            "title" => "Lawn Tennis Championship",
            "categories" => "U14, U18, Open, Doubles",
            "price_individual" => 2500,
            "price_pair" => 4000,
            "price_team" => 0
        ],
        [
            "title" => "Football Cup",
            "categories" => "U14, U18, Open, Corporate",
            "price_individual" => 0,
            "price_pair" => 0,
            "price_team" => 10000
        ]
    ];
    
    $sportsJson = json_encode($puneSports);
    
    $stmt = $db->prepare("UPDATE events SET sports_data = ? WHERE slug = 'gsa-pune-2026'");
    $stmt->execute([$sportsJson]);
    
    echo "Updated GSA Pune 2026 sports data.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
