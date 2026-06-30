<?php
require_once __DIR__ . '/config/Database.php';

$db = Database::getConnection();

try {
    // 1. Get the admin/first merchant's ID or any valid merchant ID to assign orphaned products
    $stmt = $db->query("SELECT id FROM users WHERE role IN ('merchant', 'admin') ORDER BY id ASC LIMIT 1");
    $merchant = $stmt->fetch();
    
    if ($merchant) {
        $merchantId = $merchant['id'];
        
        // 2. Update all products that currently have NO merchant assigned
        $updateStmt = $db->prepare("UPDATE products SET merchant_id = ? WHERE merchant_id IS NULL");
        $updateStmt->execute([$merchantId]);
        $count = $updateStmt->rowCount();
        
        echo json_encode(["success" => true, "message" => "$count orphaned products assigned to Merchant ID $merchantId"]);
    } else {
        echo json_encode(["success" => false, "message" => "No merchant found in the database to assign products to."]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
