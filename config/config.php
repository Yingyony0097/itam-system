<?php
/**
 * Application Configuration
 * ITAM System - P-line Company
 * 
 * Central configuration file for the ITAM system
 * Contains all application-wide settings and constants
 */

// =============================================================================
// APPLICATION SETTINGS
// =============================================================================

// Application name
define('APP_NAME', 'P-line ITAM System');
define('APP_VERSION', '1.0.1');
define('APP_ENV', 'development'); // development, staging, production

// =============================================================================
// TIMEZONE & LOCALIZATION
// =============================================================================

// Set timezone for Laos (REQ from project location)
date_default_timezone_set('Asia/Vientiane');
define('APP_TIMEZONE', 'Asia/Vientiane');

// Default language
define('DEFAULT_LANG', 'lo'); // Lao language

// =============================================================================
// DATABASE CONFIGURATION
// =============================================================================

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'itam_system');
define('DB_USER', 'root');          // Change for production
define('DB_PASS', '');              // Change for production
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');

// PDO options for security (REQ-SEC-003)
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
]);

// =============================================================================
// URL & PATH CONFIGURATION
// =============================================================================

// Base URL (change based on your environment)
// Development: http://localhost/itam-system/
// Production: https://itam.pline.com/
define('BASE_URL', 'http://localhost/itam-system/');

// Root path
define('ROOT_PATH', dirname(__DIR__));

// Directory paths
define('CONFIG_PATH', ROOT_PATH . '/config');
define('MODELS_PATH', ROOT_PATH . '/models');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('INCLUDES_PATH', ROOT_PATH . '/includes');

// Asset paths
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('CSS_PATH', ASSETS_PATH . '/css');
define('JS_PATH', ASSETS_PATH . '/js');
define('IMAGES_PATH', ASSETS_PATH . '/images');

// =============================================================================
// UPLOAD & STORAGE PATHS
// =============================================================================

// Uploads directory for asset photos
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('UPLOADS_URL', BASE_URL . 'uploads/');

// Reports directory for generated reports
define('REPORTS_PATH', ROOT_PATH . '/reports');
define('REPORTS_URL', BASE_URL . 'reports/');

// Create directories if they don't exist
if (!file_exists(UPLOADS_PATH)) {
    mkdir(UPLOADS_PATH, 0755, true);
}
if (!file_exists(REPORTS_PATH)) {
    mkdir(REPORTS_PATH, 0755, true);
}

// =============================================================================
// FILE UPLOAD SETTINGS (REQ-SEC-010)
// =============================================================================

// Allowed file types for asset photos
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Max upload size (in bytes) - 5MB
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);

// =============================================================================
// SESSION CONFIGURATION (REQ-SEC-002)
// =============================================================================

// Session settings
define('SESSION_NAME', 'ITAM_SESSION');
define('SESSION_LIFETIME', 3600 * 8); // 8 hours
define('SESSION_COOKIE_HTTPONLY', true);
define('SESSION_COOKIE_SECURE', false); // Set to true in production with HTTPS
define('SESSION_USE_ONLY_COOKIES', true);

// =============================================================================
// SECURITY SETTINGS
// =============================================================================

// Password requirements (REQ-VAL-006)
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_REQUIRE_UPPERCASE', true);
define('PASSWORD_REQUIRE_LOWERCASE', true);
define('PASSWORD_REQUIRE_NUMBER', true);

// CSRF Token settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_LIFETIME', 3600); // 1 hour

// =============================================================================
// PAGINATION SETTINGS
// =============================================================================

define('ITEMS_PER_PAGE', 10);
define('REPORTS_PER_PAGE', 20);
define('LOGS_PER_PAGE', 15);

// =============================================================================
// DATE & TIME FORMATS
// =============================================================================

define('DATE_FORMAT', 'd/m/Y');           // 28/01/2026
define('DATETIME_FORMAT', 'd/m/Y H:i');   // 28/01/2026 14:30
define('TIME_FORMAT', 'H:i:s');           // 14:30:45

// Database date formats
define('DB_DATE_FORMAT', 'Y-m-d');
define('DB_DATETIME_FORMAT', 'Y-m-d H:i:s');

// =============================================================================
// ASSET SETTINGS
// =============================================================================

// Asset code prefix
define('ASSET_CODE_PREFIX', 'AST-');
define('ASSET_CODE_LENGTH', 6); // AST-000001

// Asset statuses
define('ASSET_STATUS_AVAILABLE', 'Available');
define('ASSET_STATUS_IN_USE', 'In Use');

// =============================================================================
// ERROR HANDLING & LOGGING (REQ-REL-004)
// =============================================================================

// Error reporting based on environment
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    define('ERROR_LOG_PATH', ROOT_PATH . '/logs/error.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// =============================================================================
// EMAIL CONFIGURATION (Future use)
// =============================================================================

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'noreply@pline.com');
define('SMTP_PASSWORD', ''); // Set in production
define('SMTP_FROM_EMAIL', 'noreply@pline.com');
define('SMTP_FROM_NAME', APP_NAME);

// =============================================================================
// CACHE SETTINGS
// =============================================================================

define('CACHE_ENABLED', false);
define('CACHE_LIFETIME', 3600); // 1 hour

// =============================================================================
// API SETTINGS (REQ-TECH-010)
// =============================================================================

define('API_VERSION', 'v1');
define('API_BASE_URL', BASE_URL . 'api/');

// =============================================================================
// HELPER FUNCTIONS
// =============================================================================

/**
 * Get configuration value
 * 
 * @param string $key Configuration key
 * @param mixed $default Default value if not found
 * @return mixed Configuration value
 */
function config($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

/**
 * Generate full URL from path
 * 
 * @param string $path Relative path
 * @return string Full URL
 */
function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}

/**
 * Generate asset URL
 * 
 * @param string $path Asset path
 * @return string Full asset URL
 */
function asset($path = '') {
    return BASE_URL . 'assets/' . ltrim($path, '/');
}

/**
 * Generate upload URL
 * 
 * @param string $filename Upload filename
 * @return string Full upload URL
 */
function upload_url($filename = '') {
    return UPLOADS_URL . ltrim($filename, '/');
}

/**
 * Format date for display
 * 
 * @param string $date Date string
 * @param string $format Date format
 * @return string Formatted date
 */
function format_date($date, $format = DATE_FORMAT) {
    if (empty($date)) return '-';
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

/**
 * Format currency (Lao Kip)
 * 
 * @param float $amount Amount
 * @return string Formatted currency
 */
function format_currency($amount) {
    return number_format($amount, 0, ',', '.') . ' LAK';
}

/**
 * Debug helper (only in development)
 * 
 * @param mixed $data Data to debug
 * @param bool $die Stop execution
 */
function dd($data, $die = true) {
    if (APP_ENV !== 'production') {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        if ($die) die();
    }
}