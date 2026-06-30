<?php
require_once __DIR__ . '/config/Database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id) {
    try {
        $db = Database::getConnection();
        
        $stmt = $db->prepare("SELECT image FROM blogs WHERE id = ?");
        $stmt->execute([$id]);
        $blog = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($blog && !empty($blog['image'])) {
            $imagePath = __DIR__ . '/' . $blog['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $stmt = $db->prepare("DELETE FROM blogs WHERE id = ?");
        $stmt->execute([$id]);
    } catch (Exception $e) {
        // Silently fail if deletion fails.
    }
}

header("Location: admin-blogs.php");
exit;
?>
