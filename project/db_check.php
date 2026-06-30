<?php require_once __DIR__ . "/config/Database.php"; $db = Database::getConnection(); 
$db->exec("UPDATE users SET credits=100000 WHERE id=1"); 
