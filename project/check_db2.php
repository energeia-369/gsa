<?php require_once 'config/Database.php'; $db = Database::getConnection(); $q = $db->query('SELECT * FROM home_event_cards'); print_r($q->fetchAll(PDO::FETCH_ASSOC));
