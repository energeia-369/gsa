<?php
require 'config/Database.php';
try {
    Database::getConnection()->exec('ALTER TABLE home_carousel_events ADD delegate_reg_url VARCHAR(255) NULL');
    echo 'Added delegate_reg_url successfully.';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
