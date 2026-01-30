<?php
/**
 * Helper Functions for ITAM System
 * P-line Company - Vientiane, Laos
 */

/**
 * Sanitize output for HTML display
 * Prevents XSS attacks (REQ-SEC-004)
 */
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize input data
 * Prevents injection attacks (REQ-SEC-006)
 */
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Redirect to a specific URL
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Format currency for display
 */
function format_currency($amount) {
    return '$' . number_format($amount, 2);
}

/**
 * Format date for display
 */
function format_date($date) {
    if (!$date) return 'N/A';
    return date('M d, Y', strtotime($date));
}

/**
 * Format datetime for display
 */
function format_datetime($datetime) {
    if (!$datetime) return 'N/A';
    return date('M d, Y h:i A', strtotime($datetime));
}

/**
 * Get user initials from name
 * Example: "John Doe" -> "JD"
 */
function get_user_initials($name) {
    $words = explode(' ', trim($name));
    
    if (count($words) >= 2) {
        // First and last name
        return strtoupper(substr($words[0], 0, 1) . substr($words[count($words) - 1], 0, 1));
    } else {
        // Single name - take first 2 letters
        return strtoupper(substr($name, 0, 2));
    }
}

/**
 * Get time ago string
 * Example: "2 hours ago", "1 day ago"
 */
function time_ago($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    $periods = [
        'year' => 31536000,
        'month' => 2592000,
        'week' => 604800,
        'day' => 86400,
        'hour' => 3600,
        'minute' => 60,
        'second' => 1
    ];
    
    foreach ($periods as $key => $value) {
        if ($difference >= $value) {
            $time = floor($difference / $value);
            return $time . ' ' . $key . ($time > 1 ? 's' : '') . ' ago';
        }
    }
    
    return 'Just now';
}

/**
 * Generate random asset code
 * Format: AST-XXX (e.g., AST-001)
 */
function generate_asset_code($last_code = null) {
    if ($last_code) {
        $number = intval(substr($last_code, 4)) + 1;
    } else {
        $number = 1;
    }
    return 'AST-' . str_pad($number, 3, '0', STR_PAD_LEFT);
}

/**
 * Check if string is a valid email
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Display flash message
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Show success toast notification
 */
function show_success($message) {
    set_flash('success', $message);
}

/**
 * Show error toast notification
 */
function show_error($message) {
    set_flash('error', $message);
}