<?php
require 'config/Database.php';
$cols = Database::getConnection()->query('DESCRIBE home_carousel_events')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($cols);
