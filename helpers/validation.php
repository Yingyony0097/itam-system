<?php
/**
 * Validation Helper Functions
 * ITAM System - P-line Company
 * 
 * Server-side validation functions for forms
 */

/**
 * Validate required field
 * 
 * @param mixed $value Field value
 * @param string $field_name Field name for error message
 * @return array ['valid' => bool, 'error' => string|null]
 */
function validate_required($value, $field_name = 'Field') {
    if (is_empty($value)) {
        return [
            'valid' => false,
            'error' => "$field_name is required"
        ];
    }
    return ['valid' => true, 'error' => null];
}

/**
 * Validate email format (REQ-VAL-002)
 * 
 * @param string $email Email address
 * @return array ['valid' => bool, 'error' => string|null]
 */
function validate_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'valid' => false,
            'error' => 'Invalid email format'
        ];
    }
    return ['valid' => true, 'error' => null];
}

/**
 * Validate string length
 * 
 * @param string $value String to validate
 * @param int $min Minimum length
 * @param int $max Maximum length
 * @param string $field_name Field name for error message
 * @return array ['valid' => bool, 'error' => string|null]
 */
function validate_length($value, $min = 0, $max = 255, $field_name = 'Field') {
    $length = strlen($value);
    
    if ($length < $min) {
        return [
            'valid' => false,
            'error' => "$field_name must be at least $min characters"
        ];
    }
    
    if ($length > $max) {
        return [
            'valid' => false,
            'error' => "$field_name must not exceed $max characters"
        ];
    }
    
    return ['valid' => true, 'error' => null];
}

/**
 * Validate numeric value
 * 
 * @param mixed $value Value to validate
 * @param float $min Minimum value
 * @param float $max Maximum value (optional)
 * @param string $field_name Field name for error message
 * @return array ['valid' => bool, 'error' => string|null]
 */
function validate_numeric($value, $min = 0, $max = null, $field_name = 'Field') {
    if (!is_numeric($value)) {
        return [
            'valid' => false,
            'error' => "$field_name must be a number"
        ];
    }
    
    $num_value = floatval($value);
    
    if ($num_value < $min) {
        return [
            'valid' => false,
            'error' => "$field_name must be at least $min"
        ];
    }
    
    if ($max !== null && $num_value > $max) {
        return [
            'valid' => false,
            'error' => "$field_name must not exceed $max"
        ];
    }
    
    return ['valid' => true, 'error' => null];
}

/**
 * Validate date format (REQ-VAL-004)
 * 
 * @param string $date Date string
 * @param string $format Expected format (default: Y-m-d)
 * @return array ['valid' => bool, 'error' => string|null]
 */
function validate_date($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    
    if (!$d || $d->format($format) !== $date) {
        return [
            'valid' => false,
            'error' => 'Invalid date format. Expected: ' . $format
        ];
    }
    
    return ['valid' => true, 'error' => null];
}

/**
 * Validate asset data
 * 
 * @param array $data Asset data to validate
 * @param bool $is_update Whether this is an update operation
 * @return array ['valid' => bool, 'errors' => array]
 */
function validate_asset_data($data, $is_update = false) {
    $errors = [];
    
    // Asset Name - Required (REQ-VAL-001)
    $name_validation = validate_required($data['asset_name'] ?? '', 'Asset Name');
    if (!$name_validation['valid']) {
        $errors['asset_name'] = $name_validation['error'];
    } else {
        $length_validation = validate_length($data['asset_name'], 3, 200, 'Asset Name');
        if (!$length_validation['valid']) {
            $errors['asset_name'] = $length_validation['error'];
        }
    }
    
    // Category - Required
    $category_validation = validate_required($data['category'] ?? '', 'Category');
    if (!$category_validation['valid']) {
        $errors['category'] = $category_validation['error'];
    }
    
    // Serial Number - Optional, but validate length if provided
    if (!is_empty($data['serial_number'] ?? '')) {
        $serial_validation = validate_length($data['serial_number'], 1, 100, 'Serial Number');
        if (!$serial_validation['valid']) {
            $errors['serial_number'] = $serial_validation['error'];
        }
    }
    
    // Brand - Optional
    if (!is_empty($data['brand'] ?? '')) {
        $brand_validation = validate_length($data['brand'], 1, 100, 'Brand');
        if (!$brand_validation['valid']) {
            $errors['brand'] = $brand_validation['error'];
        }
    }
    
    // Model - Optional
    if (!is_empty($data['model'] ?? '')) {
        $model_validation = validate_length($data['model'], 1, 100, 'Model');
        if (!$model_validation['valid']) {
            $errors['model'] = $model_validation['error'];
        }
    }
    
    // Purchase Date - Optional, but validate format if provided (REQ-VAL-004)
    if (!is_empty($data['purchase_date'] ?? '')) {
        $date_validation = validate_date($data['purchase_date']);
        if (!$date_validation['valid']) {
            $errors['purchase_date'] = $date_validation['error'];
        }
    }
    
    // Purchase Price - Optional, but validate if provided (REQ-VAL-005)
    if (!is_empty($data['purchase_price'] ?? '')) {
        $price_validation = validate_numeric($data['purchase_price'], 0, null, 'Purchase Price');
        if (!$price_validation['valid']) {
            $errors['purchase_price'] = $price_validation['error'];
        }
    }
    
    // Status - Required for updates, default for creates
    if ($is_update) {
        $status_validation = validate_required($data['status'] ?? '', 'Status');
        if (!$status_validation['valid']) {
            $errors['status'] = $status_validation['error'];
        } else {
            // Validate enum values
            $valid_statuses = ['Available', 'In Use'];
            if (!in_array($data['status'], $valid_statuses)) {
                $errors['status'] = 'Invalid status value';
            }
        }
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Validate user data
 * 
 * @param array $data User data to validate
 * @param bool $is_update Whether this is an update operation
 * @return array ['valid' => bool, 'errors' => array]
 */
function validate_user_data($data, $is_update = false) {
    $errors = [];
    
    // Name - Required
    $name_validation = validate_required($data['name'] ?? '', 'Name');
    if (!$name_validation['valid']) {
        $errors['name'] = $name_validation['error'];
    }
    
    // Email - Required and valid format (REQ-VAL-002)
    $email_validation = validate_required($data['email'] ?? '', 'Email');
    if (!$email_validation['valid']) {
        $errors['email'] = $email_validation['error'];
    } else {
        $format_validation = validate_email($data['email']);
        if (!$format_validation['valid']) {
            $errors['email'] = $format_validation['error'];
        }
    }
    
    // Password - Required for new users, optional for updates (REQ-VAL-006)
    if (!$is_update) {
        $password_validation = validate_required($data['password'] ?? '', 'Password');
        if (!$password_validation['valid']) {
            $errors['password'] = $password_validation['error'];
        } else {
            // Password strength validation
            $password = $data['password'];
            if (strlen($password) < 8) {
                $errors['password'] = 'Password must be at least 8 characters';
            } elseif (!preg_match('/[A-Z]/', $password)) {
                $errors['password'] = 'Password must contain at least one uppercase letter';
            } elseif (!preg_match('/[a-z]/', $password)) {
                $errors['password'] = 'Password must contain at least one lowercase letter';
            } elseif (!preg_match('/[0-9]/', $password)) {
                $errors['password'] = 'Password must contain at least one number';
            }
        }
    }
    
    // Role - Required
    if (isset($data['role'])) {
        $valid_roles = ['Admin', 'User'];
        if (!in_array($data['role'], $valid_roles)) {
            $errors['role'] = 'Invalid role value';
        }
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}