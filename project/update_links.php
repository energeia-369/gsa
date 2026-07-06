<?php
require 'config/Database.php';
$db = Database::getConnection();
$db->exec("UPDATE home_event_cards SET link = '#' WHERE country_or_state = 'PUNE'");
$db->exec("UPDATE custom_destinations SET link = '' WHERE country = 'PUNE'");
echo 'Updated Links';
