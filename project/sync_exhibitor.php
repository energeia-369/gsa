<?php
require_once __DIR__ . '/config/Database.php';
$pdo = Database::getConnection();

$stmt = $pdo->query("SELECT title, exhibitor_data FROM home_carousel_events WHERE exhibitor_data IS NOT NULL AND exhibitor_data != ''");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($events as $e) {
    $title = $e['title'];
    $data = $e['exhibitor_data'];
    $searchTitle = '%' . trim(str_ireplace('GSA', '', $title)) . '%';
    $syncStmt = $pdo->prepare("UPDATE events SET exhibitor_data = ? WHERE title LIKE ? OR slug LIKE ?");
    $syncStmt->execute([$data, $searchTitle, $searchTitle]);
    echo "Synced: $title\n";
}
echo "Done.\n";
