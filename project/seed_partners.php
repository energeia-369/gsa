<?php
require_once __DIR__ . '/config/Database.php';
$db = (new Database())->getConnection();

// Create table if needed
$db->exec("CREATE TABLE IF NOT EXISTS admin_partners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    logo_url VARCHAR(500),
    website_url VARCHAR(500),
    tag VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Seed if empty
$count = $db->query("SELECT COUNT(*) FROM admin_partners")->fetchColumn();
if ($count == 0) {
    $defaults = [
        ['TATA GROUP',  '', 'https://www.tata.com',       'Conglomerate'],
        ['INFOSYS',     '', 'https://www.infosys.com',     'Technology'],
        ['HDFC BANK',   '', 'https://www.hdfcbank.com',    'Banking'],
        ['GOOGLE',      '', 'https://www.google.com',      'Technology'],
        ['BOOKMYSHOW',  '', 'https://in.bookmyshow.com',   'Entertainment'],
        ['DECATHLON',   '', 'https://www.decathlon.in',    'Sports & Retail'],
        ['KRAFTON',     '', '#',                           'Gaming'],
    ];
    $ins = $db->prepare("INSERT INTO admin_partners (name, logo_url, website_url, tag) VALUES (?,?,?,?)");
    foreach ($defaults as $d) $ins->execute($d);
    echo "Seeded " . count($defaults) . " default partners.\n";
} else {
    echo "Partners already in DB: $count\n";
}

$rows = $db->query("SELECT id, name, tag FROM admin_partners")->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
