<?php
require 'config/Database.php';
$db = (new Database())->getConnection();

// Seed Products if none exist
$stmt = $db->query("SELECT COUNT(*) FROM products");
if ($stmt->fetchColumn() == 0) {
    $db->exec("INSERT INTO products (name, category, price, stock, image_url) VALUES 
        ('GSA Official Jersey', 'Apparel', 1500.00, 50, ''),
        ('Premium Paintball Marker', 'Equipment', 25000.00, 15, ''),
        ('Tactical Gloves Pro', 'Accessories', 850.00, 100, '')
    ");
}

// Seed Orders if none exist
$stmt = $db->query("SELECT COUNT(*) FROM orders");
if ($stmt->fetchColumn() == 0) {
    $db->exec("INSERT INTO orders (id, user_id, total_amount, subtotal, payment_status, order_status, nxl_coins_earned, order_date) VALUES 
        ('ORD-88392A', 1, 1500.00, 1500.00, 'paid', 'processing', 15, NOW()),
        ('ORD-99120B', 2, 25850.00, 25850.00, 'pending', 'received', 258, NOW() - INTERVAL 1 DAY),
        ('ORD-44910C', 3, 3000.00, 3000.00, 'paid', 'shipped', 30, NOW() - INTERVAL 2 DAY)
    ");
}

echo "Database Seeded successfully.";
