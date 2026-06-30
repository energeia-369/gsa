<?php
require_once __DIR__ . '/config/Database.php';

$db = Database::getConnection();

try {
    // Re-assign all products from admin (ID 1) to the actual first merchant (ID 5)
    $updateStmt = $db->prepare("UPDATE products SET merchant_id = 5 WHERE merchant_id = 1");
    $updateStmt->execute();
    $count = $updateStmt->rowCount();
    
    echo json_encode(["success" => true, "message" => "$count products reassigned to Merchant ID 5 (sanket01@gmail.com)"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
