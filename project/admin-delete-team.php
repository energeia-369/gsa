<?php
require_once __DIR__ . '/config/Database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $db = Database::getConnection();
        
        // Fetch image path to delete it
        $stmt = $db->prepare("SELECT image FROM team_profiles WHERE id = ?");
        $stmt->execute([$id]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($member && !empty($member['image'])) {
            $imagePath = __DIR__ . '/' . $member['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Delete from database
        $stmt = $db->prepare("DELETE FROM team_profiles WHERE id = ?");
        $stmt->execute([$id]);
    } catch (Exception $e) {
        // Handle error silently or log it
    }
}

header("Location: admin-team-profiles.php");
exit;
?>
