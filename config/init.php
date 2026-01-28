<?php
/**
 * Application Initialization
 * ITAM System - P-line Company
 */

// Start session with secure settings (REQ-SEC-002)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0);  // Set to 1 in production with HTTPS
    session_start();
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);  // Set to 0 in production

// Timezone
date_default_timezone_set('Asia/Vientiane');

// Security headers (REQ-SEC-009)
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');

// Include database config
require_once __DIR__ . '/database.php';

// Autoloader for models and controllers
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../models/' . $class . '.php',
        __DIR__ . '/../controllers/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Helper function for secure output (REQ-SEC-004)
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Helper function for redirect
function redirect($path) {
    header("Location: $path");
    exit;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/index.php');
    }
}

// Require admin role
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        redirect('/views/user/dashboard.php');
    }
}