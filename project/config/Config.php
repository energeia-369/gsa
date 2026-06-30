<?php
// Global configuration constants

// Enable error reporting for debugging, disable in production
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'playarena_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Authentication / JWT Key
define('JWT_SECRET', 'SuperSecretJWTKeyForPlayArena2026!@#$');
define('JWT_EXPIRY', 86400); // 24 hours in seconds

// Gmail SMTP Credentials (extracted from Spring Boot application.properties)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'sanketshendage0750@gmail.com');
define('SMTP_PASS', 'oxpfdkbpsnxovjxw'); 
// <-------pass here
define('SMTP_FROM_EMAIL', 'sanketshendage0750@gmail.com');
define('SMTP_FROM_NAME', 'GLOBAL SPORTS ARENA');

// Fast2SMS API Gateway Credentials
define('FAST2SMS_API_KEY', 'l80GkpFyidISaogQhLR1DVJ4AX7rmCwfHbNZqE9YUjc62tKuP59LhD1iFv4ASVtJOsykdxwmrPYQ7uM6');
// <---------key here 
// Razorpay Credentials
define('RAZORPAY_KEY_ID', 'rzp_test_T2EBiML37yYUXT');
define('RAZORPAY_KEY_SECRET', '7C003XzZ1cHln1fD8mamEQLc');

// Admin Setup Passkey
define('ADMIN_PASSKEY', 'PLAYADMIN2026');
