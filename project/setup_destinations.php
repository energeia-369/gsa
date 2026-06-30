<?php
require_once __DIR__ . '/config/Config.php';

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS custom_destinations (
        id BIGINT PRIMARY KEY,
        country VARCHAR(255) NOT NULL,
        image VARCHAR(500) NOT NULL,
        date VARCHAR(255) NOT NULL,
        city VARCHAR(255) NOT NULL,
        region VARCHAR(255) NOT NULL,
        type VARCHAR(50) NOT NULL,
        link VARCHAR(500) DEFAULT NULL,
        is_deleted TINYINT(1) DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $db->exec($sql);
    echo "Table 'custom_destinations' checked/created successfully.\n";

} catch(PDOException $e) {
    die("Database Error: " . $e->getMessage() . "\n");
}
?>
