<?php
require_once __DIR__ . '/config/Database.php';
try {
    $db = (new Database())->getConnection();
    // Clear old tournaments
    $db->exec("TRUNCATE TABLE tournaments");
    
    // Insert new tournaments from the Pune 2027 event page
    $stmt = $db->prepare("INSERT INTO tournaments (name, sport, venue, date, registration_fee, max_teams, current_teams) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute(['Badminton Championship', 'Badminton', 'Shree Shiv Chhatrapati Sports Complex, Pune', 'Oct 6-13, 2027', 1500, 32, 0]);
    $stmt->execute(['Table Tennis Championship', 'Table Tennis', 'Shree Shiv Chhatrapati Sports Complex, Pune', 'Oct 6-13, 2027', 1200, 32, 0]);
    $stmt->execute(['Lawn Tennis Championship', 'Lawn Tennis', 'Shree Shiv Chhatrapati Sports Complex, Pune', 'Oct 6-13, 2027', 2500, 32, 0]);
    $stmt->execute(['Football Cup', 'Football', 'Shree Shiv Chhatrapati Sports Complex, Pune', 'Oct 6-13, 2027', 10000, 16, 0]);
    
    echo "Tournaments updated successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
