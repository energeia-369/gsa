<?php
require 'c:\xampp\htdocs\Mithraa_E_Project\project\config\Database.php';
$db = (new Database())->getConnection();

try {
    $db->exec("ALTER TABLE event_registrations ADD COLUMN registration_type VARCHAR(50) NULL AFTER sport");
    echo "Column registration_type added successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
