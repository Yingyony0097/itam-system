<?php
/**
 * Application Initialization
 * ITAM System - P-line Company
 * Security: REQ-SEC-002, REQ-SEC-009
 */

// Start output buffering
ob_start();

// Load configuration
require_once __DIR__ . '/config.php';

// Load database connection
require_once __DIR__ . '/database.php';

// Load helper functions
require_once HELPER_PATH . '/functions.php';
require_once HELPER_PATH . '/auth_helper.php';
require_once HELPER_PATH . '/validation.php';

// Set secure session configuration (REQ-SEC-002)
ini_set('session.cookie_httponly', SESSION_HTTPONLY);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', SESSION_SAMESITE);
ini_set('session.cookie_secure', SESSION_SECURE);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_lifetime', SESSION_LIFETIME);

// Start session with custom name
session_name(SESSION_NAME);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID periodically (REQ-SEC-002)
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) { // 30 minutes
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Set security headers (REQ-SEC-009)
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Autoload models
spl_autoload_register(function($class) {
    $model_file = MODEL_PATH . '/' . $class . '.php';
    if (file_exists($model_file)) {
        require_once $model_file;
    }
});

// Autoload controllers
spl_autoload_register(function($class) {
    $controller_file = CONTROLLER_PATH . '/' . $class . '.php';
    if (file_exists($controller_file)) {
        require_once $controller_file;
    }
});

// Initialize CSRF token if not exists (REQ-SEC-007)
if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    $_SESSION[CSRF_TOKEN_NAME . '_time'] = time();
}

// Create upload directories if not exist
if (!file_exists(ASSET_UPLOAD_PATH)) {
    mkdir(ASSET_UPLOAD_PATH, 0755, true);
}