<?php
/**
 * Application Configuration
 * ITAM System - P-line Company
 */

// Application Settings
define('APP_NAME', 'ITAM System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/itam-system');
define('APP_TIMEZONE', 'Asia/Vientiane');

// Path Configuration
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('MODEL_PATH', ROOT_PATH . '/models');
define('CONTROLLER_PATH', ROOT_PATH . '/controllers');
define('VIEW_PATH', ROOT_PATH . '/views');
define('HELPER_PATH', ROOT_PATH . '/helpers');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('ASSET_UPLOAD_PATH', UPLOAD_PATH . '/assets');

// URL Configuration
define('BASE_URL', APP_URL);
define('ASSETS_URL', BASE_URL . '/public/assets');
define('CSS_URL', ASSETS_URL . '/css');
define('JS_URL', ASSETS_URL . '/js');
define('IMG_URL', ASSETS_URL . '/images');
define('UPLOAD_URL', BASE_URL . '/public/uploads');

// Session Configuration (REQ-SEC-002)
define('SESSION_NAME', 'ITAM_SESSION');
define('SESSION_LIFETIME', 7200); // 2 hours in seconds
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', '');
define('SESSION_SECURE', false); // Set to true in production with HTTPS
define('SESSION_HTTPONLY', true);
define('SESSION_SAMESITE', 'Strict');

// Security Settings (REQ-SEC-007)
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_EXPIRE', 3600); // 1 hour

// File Upload Settings (REQ-SEC-010)
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// Pagination
define('RECORDS_PER_PAGE', 10);

// Date/Time Format
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd/m/Y');
define('DISPLAY_DATETIME_FORMAT', 'd/m/Y H:i');

// Error Reporting
if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', ROOT_PATH . '/logs/error.log');
}

// Set timezone
date_default_timezone_set(APP_TIMEZONE);