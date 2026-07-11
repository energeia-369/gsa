<?php
require 'config/Database.php';
$dbConn = Database::getConnection();

try {
    $stmt = $dbConn->query("SELECT * FROM custom_destinations");
    $dest = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($dest);
} catch (Exception $e) {
    echo "No custom_destinations table";
}
