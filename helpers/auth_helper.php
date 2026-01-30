<?php
/**
 * Authentication Helper Functions
 * ITAM System - P-line Company
 * Security: REQ-SEC-004, REQ-SEC-006
 */

/**
 * Sanitize input data (REQ-SEC-004, REQ-SEC-006)
 * @param mixed $data
 * @return mixed
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }

    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

    return $data;
}

/**
 * Clean input for database (additional layer)
 * @param string $data
 * @return string
 */
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

/**
 * Escape output for HTML display (REQ-SEC-004)
 * @param string $data
 * @return string
 */
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Check if user is logged in (REQ-AUTH-003)
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user ID
 * @return int|null
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role (REQ-AUTH-002)
 * @return string|null
 */
function get_user_role() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Get current user name
 * @return string|null
 */
function get_user_name() {
    return $_SESSION['user_name'] ?? null;
}

/**
 * Get current user email
 * @return string|null
 */
function get_user_email() {
    return $_SESSION['user_email'] ?? null;
}

/**
 * Check if user is admin (REQ-AUTH-002)
 * @return bool
 */
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin';
}

/**
 * Set user session (REQ-AUTH-003)
 * @param int $user_id
 * @param string $name
 * @param string $email
 * @param string $role
 */
function set_user_session($user_id, $name, $email, $role) {
    // Regenerate session ID to prevent session fixation (REQ-SEC-002)
    session_regenerate_id(true);

    // Set session variables
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role'] = $role;
    $_SESSION['last_activity'] = time();
}

/**
 * Clear user session (REQ-AUTH-004)
 */
function clear_user_session() {
    // Unset all session variables
    $_SESSION = [];

    // Destroy session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destroy session
    session_destroy();
}

/**
 * Get CSRF token (REQ-SEC-007)
 * Alias for generate_csrf_token() for backward compatibility
 * @return string
 */
function csrf_token() {
    return generate_csrf_token();
}

/**
 * Redirect back to previous page
 */
function redirect_back() {
    $url = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
    redirect($url);
}

/**
 * Set flash message
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
function set_flash($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * @return array|null
 */
function get_flash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * Format datetime for display
 * @param string $datetime
 * @param string $format
 * @return string
 */
function format_datetime($datetime, $format = DISPLAY_DATETIME_FORMAT) {
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return '';
    }

    $timestamp = strtotime($datetime);
    return date($format, $timestamp);
}

/**
 * Generate random string
 * @param int $length
 * @return string
 */
function generate_random_string($length = 16) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Get asset status badge HTML
 * @param string $status
 * @return string
 */
function get_status_badge($status) {
    $class = $status === 'Available' ? 'badge-success' : 'badge-warning';
    return '<span class="badge ' . $class . '">' . escape($status) . '</span>';
}

/**
 * Get role badge HTML
 * @param string $role
 * @return string
 */
function get_role_badge($role) {
    $class = $role === 'Admin' ? 'badge-info' : 'badge-success';
    return '<span class="badge ' . $class . '">' . escape($role) . '</span>';
}

/**
 * Pagination helper
 * @param int $total_records
 * @param int $current_page
 * @param int $per_page
 * @return array
 */
function get_pagination($total_records, $current_page = 1, $per_page = RECORDS_PER_PAGE) {
    $total_pages = ceil($total_records / $per_page);
    $offset = ($current_page - 1) * $per_page;

    return [
        'total_records' => $total_records,
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'per_page' => $per_page,
        'offset' => $offset,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages
    ];
}
