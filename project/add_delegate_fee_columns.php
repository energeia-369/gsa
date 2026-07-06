<?php
require 'config/Database.php';
$db = Database::getConnection();
try {
    $db->exec("ALTER TABLE events ADD COLUMN delegate_fee DECIMAL(10,2) NULL");
    $db->exec("ALTER TABLE events ADD COLUMN delegate_currency VARCHAR(10) NULL");
    echo "Columns added successfully";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
