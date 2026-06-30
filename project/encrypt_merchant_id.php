<?php
require 'config/Database.php';

$db = (new Database())->getConnection();
$encryption_key = 'GSA_SECRET_MERCHANT_KEY_2026';

// 1. Change the column to VARCHAR
$db->exec("ALTER TABLE products MODIFY merchant_id VARCHAR(255) NULL");

// 2. Fetch all products that have a numeric merchant_id
$stmt = $db->query("SELECT id, merchant_id FROM products WHERE merchant_id IS NOT NULL AND merchant_id != ''");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Encrypt and update
$updateStmt = $db->prepare("UPDATE products SET merchant_id = ? WHERE id = ?");

$count = 0;
foreach ($products as $p) {
    // Check if it's already encrypted (if it's a number, encrypt it)
    if (is_numeric($p['merchant_id'])) {
        // Simple AES-256-CBC encryption
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($p['merchant_id'], 'aes-256-cbc', $encryption_key, 0, $iv);
        // Store IV and encrypted data together, base64 encoded
        $final_string = base64_encode($encrypted . '::' . $iv);
        
        $updateStmt->execute([$final_string, $p['id']]);
        $count++;
    }
}

echo "Successfully encrypted $count merchant IDs!";
?>
