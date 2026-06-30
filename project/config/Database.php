<?php
require_once __DIR__ . '/Config.php';

class Database {
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                self::$conn = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $exception) {
                // If database doesn't exist or connection fails, log or print error
                error_log("Connection error: " . $exception->getMessage());
                throw new Exception("Database connection failed. Please verify your config/Config.php file.");
            }
        }
        return self::$conn;
    }
}
