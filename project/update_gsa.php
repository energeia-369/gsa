<?php
require 'config/Database.php';
$db = Database::getConnection();

$national = ['TAMIL NADU', 'PUNE', 'MAHARASHTRA', 'KARNATAKA', 'DELHI', 'GOA', 'KERALA', 'RAJASTHAN', 'GUJARAT'];

$inQuery = implode(',', array_fill(0, count($national), '?'));

$stmt = $db->prepare("UPDATE home_event_cards SET module_type = 'gsa_carousel' WHERE country_or_state IN ($inQuery)");
$stmt->execute($national);

echo "Updated " . $stmt->rowCount() . " rows to gsa_carousel.\n";
