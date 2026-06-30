<?php
require_once __DIR__ . '/config/Database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id) {
    try {
        $db = Database::getConnection();
        
        // Optionally fetch the image to delete the file
        $stmt = $db->prepare("SELECT image FROM home_event_cards WHERE id = ?");
        $stmt->execute([$id]);
        $card = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($card && !empty($card['image'])) {
            $imagePath = __DIR__ . '/' . $card['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $stmt = $db->prepare("DELETE FROM home_event_cards WHERE id = ?");
        $stmt->execute([$id]);
    } catch (Exception $e) {
        // Log error if needed
    }
}

header("Location: admin-home-event-cards.php");
exit;
?>
