<?php
require 'config/Database.php';
$db = Database::getConnection();
$db->exec("DELETE FROM home_event_cards WHERE country_or_state = 'PUNE'");
echo 'Deleted Legacy Pune';
