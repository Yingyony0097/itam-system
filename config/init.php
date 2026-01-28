<?php
/**
 * Application Initialization
 * ITAM System - P-line Company
 */

// Load main configuration
require_once __DIR__ . '/config.php';

// =============================================================================
// SESSION MANAGEMENT (REQ-SEC-002)
// =============================================================================

if (session_status() === PHP_SESSION_NONE) {
    // Configure session settings
    ini_set('session.cookie_httponly', SESSION_COOKIE_HTTPONLY ? 1 : 0);
    ini_set('session.use_only_cookies', SESSION_USE_ONLY_COOKIES ? 1 : 0);
    ini_set('session.cookie_secure', SESSION_COOKIE_SECURE ? 1 : 0);
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    
    session_name(SESSION_NAME);
    session_start();
}

// =============================================================================
// SECURITY HEADERS (REQ-SEC-009)
// =============================================================================

header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');

// =============================================================================
// AUTOLOADER
// =============================================================================

spl_autoload_register(function ($class) {
    $paths = [
        MODELS_PATH . '/' . $class . '.php',
        CONTROLLERS_PATH . '/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// =============================================================================
// HELPER FUNCTIONS
// =============================================================================

/**
 * Sanitize output for XSS prevention (REQ-SEC-004)
 * 
 * @param string $data Data to escape
 * @return string Escaped data
 */
function escape($data) {
    if (is_array($data)) {
        return array_map('escape', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a URL
 * 
 * @param string $path Path to redirect to
 */
function redirect($path) {
    // If path doesn't start with http, treat as relative
    if (!preg_match('/^https?:\/\//', $path)) {
        $path = url($path);
    }
    header("Location: $path");
    exit;
}

/**
 * Check if user is logged in
 * 
 * @return bool Login status
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * 
 * @return bool Admin status
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

/**
 * Require user to be logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect('index.php');
    }
}

/**
 * Require user to be admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        redirect('views/user/dashboard.php');
    }
}

/**
 * Get current user data
 * 
 * @return array|null User data or null
 */
function currentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'user_id' => $_SESSION['user_id'] ?? null,
        'name' => $_SESSION['name'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'role' => $_SESSION['role'] ?? null
    ];
}

/**
 * Generate CSRF token (REQ-SEC-007)
 * 
 * @return string CSRF token
 */
function csrf_token() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        $_SESSION[CSRF_TOKEN_NAME . '_time'] = time();
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF token (REQ-SEC-007)
 * 
 * @param string $token Token to verify
 * @return bool Verification status
 */
function csrf_verify($token) {
    if (empty($_SESSION[CSRF_TOKEN_NAME]) || empty($token)) {
        return false;
    }
    
    // Check token expiration
    $tokenTime = $_SESSION[CSRF_TOKEN_NAME . '_time'] ?? 0;
    if (time() - $tokenTime > CSRF_TOKEN_LIFETIME) {
        unset($_SESSION[CSRF_TOKEN_NAME]);
        unset($_SESSION[CSRF_TOKEN_NAME . '_time']);
        return false;
    }
    
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Set flash message
 * 
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message text
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * 
 * @return array|null Flash message or null
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Validate password strength (REQ-VAL-006)
 * 
 * @param string $password Password to validate
 * @return array Validation result [valid => bool, errors => array]
 */
function validatePassword($password) {
    $errors = [];
    
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        $errors[] = "Password must be at least " . PASSWORD_MIN_LENGTH . " characters long";
    }
    
    if (PASSWORD_REQUIRE_UPPERCASE && !preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    
    if (PASSWORD_REQUIRE_LOWERCASE && !preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    
    if (PASSWORD_REQUIRE_NUMBER && !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Sanitize input (REQ-SEC-006)
 * 
 * @param string $data Input data
 * @return string Sanitized data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

/**
 * Validate email format (REQ-VAL-002)
 * 
 * @param string $email Email to validate
 * @return bool Validation status
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}