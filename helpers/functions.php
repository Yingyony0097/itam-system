<?php
/**
 * Helper Functions
 * ITAM System - P-line Company
 * 
 * Common utility functions used throughout the application
 */

/**
 * Sanitize user input to prevent XSS attacks
 * 
 * @param string $data Raw input data
 * @return string Sanitized data
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Upload file with validation (REQ-SEC-010)
 * 
 * @param array $file $_FILES array element
 * @param string $upload_dir Target directory (relative to public/)
 * @param array $allowed_types Allowed MIME types
 * @param int $max_size Maximum file size in bytes (default: 5MB)
 * @return array ['success' => bool, 'filename' => string|null, 'error' => string|null]
 */
function upload_file($file, $upload_dir = 'uploads/assets/', $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'], $max_size = 5242880) {
    $result = [
        'success' => false,
        'filename' => null,
        'error' => null
    ];
    
    // Check if file was uploaded
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        $result['error'] = 'No file uploaded';
        return $result;
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = 'File upload error: ' . $file['error'];
        return $result;
    }
    
    // Validate file size
    if ($file['size'] > $max_size) {
        $result['error'] = 'File size exceeds maximum allowed size (' . ($max_size / 1048576) . 'MB)';
        return $result;
    }
    
    // Validate file type using finfo (more secure than mime type check)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        $result['error'] = 'Invalid file type. Allowed types: ' . implode(', ', $allowed_types);
        return $result;
    }
    
    // Generate unique filename to prevent conflicts and security issues
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $unique_filename = 'asset_' . time() . '_' . uniqid() . '.' . strtolower($file_extension);
    
    // Create upload directory if it doesn't exist
    $full_upload_dir = __DIR__ . '/../public/' . $upload_dir;
    if (!is_dir($full_upload_dir)) {
        mkdir($full_upload_dir, 0755, true);
    }
    
    // Move uploaded file
    $target_path = $full_upload_dir . $unique_filename;
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $result['success'] = true;
        $result['filename'] = $upload_dir . $unique_filename;
    } else {
        $result['error'] = 'Failed to move uploaded file';
    }
    
    return $result;
}

/**
 * Delete uploaded file
 * 
 * @param string $filename Filename relative to public directory
 * @return bool Success status
 */
function delete_file($filename) {
    if (empty($filename)) {
        return false;
    }
    
    $file_path = __DIR__ . '/../public/' . $filename;
    
    if (file_exists($file_path)) {
        return unlink($file_path);
    }
    
    return false;
}

/**
 * Format currency for display
 * 
 * @param float $amount Amount to format
 * @param string $currency Currency symbol
 * @return string Formatted currency
 */
function format_currency($amount, $currency = '$') {
    return $currency . number_format($amount, 2);
}

/**
 * Format date for display
 * 
 * @param string $date Date string
 * @param string $format Output format
 * @return string Formatted date
 */
function format_date($date, $format = 'M d, Y') {
    if (empty($date) || $date === '0000-00-00') {
        return '-';
    }
    return date($format, strtotime($date));
}

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token (REQ-SEC-007)
 * 
 * @param string $token Token to verify
 * @return bool Verification result
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect to a URL
 * 
 * @param string $url Target URL
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Set flash message in session
 * 
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message text
 */
function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * 
 * @return array|null Flash message array or null
 */
function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Check if value is empty (including zero and false)
 *
 * @param mixed $value Value to check
 * @return bool True if empty
 */
function is_empty($value) {
    return empty($value) && $value !== '0' && $value !== 0;
}

/**
 * Get user initials from name
 *
 * @param string $name Full name
 * @return string Initials (e.g., "John Doe" => "JD")
 */
function get_user_initials($name) {
    if (empty($name)) {
        return '??';
    }

    $words = explode(' ', trim($name));
    if (count($words) === 1) {
        return strtoupper(substr($words[0], 0, 2));
    }

    return strtoupper(substr($words[0], 0, 1) . substr($words[count($words) - 1], 0, 1));
}

/**
 * Convert timestamp to relative time (e.g., "2 hours ago")
 *
 * @param string $datetime Date/time string
 * @return string Relative time
 */
function time_ago($datetime) {
    if (empty($datetime)) {
        return 'Never';
    }

    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 31536000) {
        $months = floor($diff / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    } else {
        $years = floor($diff / 31536000);
        return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
    }
}