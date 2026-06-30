<?php
require_once __DIR__ . '/config/Database.php';
$db = Database::getConnection();
$stmt = $db->query("SELECT id, name, merchant_id FROM products");
$prods = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($prods);
?>
