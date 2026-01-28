<?php
/**
 * Database Configuration
 * ITAM System - P-line Company
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'itam_system');
define('DB_USER', 'root');  // Change for production
define('DB_PASS', '');      // Change for production
define('DB_CHARSET', 'utf8mb4');

// PDO options for security (REQ-SEC-003)
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
]);