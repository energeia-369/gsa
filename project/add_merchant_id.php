<?php
// One-time migration: add merchant_id column to products table
require_once __DIR__ . '/config/Database.php';

$db = Database::getConnection();

try {
    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM products LIKE 'merchant_id'");
    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE products ADD COLUMN merchant_id INT DEFAULT NULL AFTER id");
        echo "<p style='color:green;'>✅ merchant_id column added to products table.</p>";
    } else {
        echo "<p style='color:orange;'>⚠️ merchant_id column already exists.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
}
echo "<p><a href='merchant.php'>← Back to Merchant Dashboard</a></p>";
?>
