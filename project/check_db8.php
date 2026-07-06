<?php
require_once __DIR__ . '/config/Database.php';

try {
    $pdo = Database::getConnection();
    
    $stmt = $pdo->query('SELECT * FROM home_event_cards LIMIT 1');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r(array_keys($row));
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
