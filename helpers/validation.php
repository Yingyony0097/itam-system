<?php
/**
 * Form Validation Helper Functions
 * ITAM System - P-line Company
 * Security: REQ-VAL-001 to REQ-VAL-010
 */

/**
 * Validate required field (REQ-VAL-001)
 * @param string $value
 * @return bool
 */
function validate_required($value) {
    return !empty(trim($value));
}

/**
 * Validate email format (REQ-VAL-002)
 * @param string $email
 * @return bool
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength (REQ-VAL-006)
 * Minimum 8 characters, at least one uppercase, one lowercase, one number
 * @param string $password
 * @return array ['valid' => bool, 'message' => string]
 */
function validate_password($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    
    return [
        'valid' => empty($errors),
        'message' => implode('. ', $errors)
    ];
}

/**
 * Validate date format (REQ-VAL-004)
 * @param string $date
 * @param string $format
 * @return bool
 */
function validate_date($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Validate positive number (REQ-VAL-005)
 * @param mixed $value
 * @return bool
 */
function validate_positive_number($value) {
    return is_numeric($value) && $value > 0;
}

/**
 * Validate integer
 * @param mixed $value
 * @return bool
 */
function validate_integer($value) {
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}

/**
 * Validate decimal/float
 * @param mixed $value
 * @return bool
 */
function validate_decimal($value) {
    return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
}

/**
 * Validate string length
 * @param string $value
 * @param int $min
 * @param int $max
 * @return bool
 */
function validate_length($value, $min, $max = null) {
    $length = strlen($value);
    
    if ($length < $min) {
        return false;
    }
    
    if ($max !== null && $length > $max) {
        return false;
    }
    
    return true;
}

/**
 * Validate unique email in database
 * @param string $email
 * @param int|null $exclude_user_id
 * @return bool
 */
function validate_unique_email($email, $exclude_user_id = null) {
    try {
        $db = new Database();
        $conn = $db->connect();
        
        $sql = "SELECT user_id FROM users WHERE email = :email";
        
        if ($exclude_user_id) {
            $sql .= " AND user_id != :user_id";
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        
        if ($exclude_user_id) {
            $stmt->bindParam(':user_id', $exclude_user_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->rowCount() === 0;
        
    } catch (PDOException $e) {
        error_log("Validation Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Validate unique serial number
 * @param string $serial_number
 * @param int|null $exclude_asset_id
 * @return bool
 */
function validate_unique_serial($serial_number, $exclude_asset_id = null) {
    if (empty($serial_number)) {
        return true; // Serial number is optional
    }
    
    try {
        $db = new Database();
        $conn = $db->connect();
        
        $sql = "SELECT asset_id FROM assets WHERE serial_number = :serial_number";
        
        if ($exclude_asset_id) {
            $sql .= " AND asset_id != :asset_id";
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':serial_number', $serial_number);
        
        if ($exclude_asset_id) {
            $stmt->bindParam(':asset_id', $exclude_asset_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->rowCount() === 0;
        
    } catch (PDOException $e) {
        error_log("Validation Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Validate asset form data
 * @param array $data
 * @param int|null $asset_id For edit mode
 * @return array ['valid' => bool, 'errors' => array]
 */
function validate_asset_form($data, $asset_id = null) {
    $errors = [];
    
    // Asset name (required)
    if (!validate_required($data['asset_name'] ?? '')) {
        $errors['asset_name'] = "Asset name is required";
    } elseif (!validate_length($data['asset_name'], 1, 200)) {
        $errors['asset_name'] = "Asset name must be between 1 and 200 characters";
    }
    
    // Category (required)
    if (!validate_required($data['category'] ?? '')) {
        $errors['category'] = "Category is required";
    }
    
    // Serial number (optional but must be unique if provided)
    if (!empty($data['serial_number'])) {
        if (!validate_unique_serial($data['serial_number'], $asset_id)) {
            $errors['serial_number'] = "Serial number already exists";
        }
    }
    
    // Purchase price (optional but must be positive if provided)
    if (!empty($data['purchase_price'])) {
        if (!validate_positive_number($data['purchase_price'])) {
            $errors['purchase_price'] = "Purchase price must be a positive number";
        }
    }
    
    // Purchase date (optional but must be valid if provided)
    if (!empty($data['purchase_date'])) {
        if (!validate_date($data['purchase_date'])) {
            $errors['purchase_date'] = "Invalid date format";
        }
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Validate user form data
 * @param array $data
 * @param int|null $user_id For edit mode
 * @return array ['valid' => bool, 'errors' => array]
 */
function validate_user_form($data, $user_id = null) {
    $errors = [];
    
    // Name (required)
    if (!validate_required($data['name'] ?? '')) {
        $errors['name'] = "Name is required";
    } elseif (!validate_length($data['name'], 1, 100)) {
        $errors['name'] = "Name must be between 1 and 100 characters";
    }
    
    // Email (required, valid format, unique)
    if (!validate_required($data['email'] ?? '')) {
        $errors['email'] = "Email is required";
    } elseif (!validate_email($data['email'])) {
        $errors['email'] = "Invalid email format";
    } elseif (!validate_unique_email($data['email'], $user_id)) {
        $errors['email'] = "Email already exists";
    }
    
    // Password (required for new user, optional for edit)
    if ($user_id === null) { // New user
        if (!validate_required($data['password'] ?? '')) {
            $errors['password'] = "Password is required";
        } else {
            $password_validation = validate_password($data['password']);
            if (!$password_validation['valid']) {
                $errors['password'] = $password_validation['message'];
            }
        }
    } else { // Edit user - password optional
        if (!empty($data['password'])) {
            $password_validation = validate_password($data['password']);
            if (!$password_validation['valid']) {
                $errors['password'] = $password_validation['message'];
            }
        }
    }
    
    // Role (required)
    if (!validate_required($data['role'] ?? '')) {
        $errors['role'] = "Role is required";
    } elseif (!in_array($data['role'], ['Admin', 'User'])) {
        $errors['role'] = "Invalid role";
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Validate login form data
 * @param array $data
 * @return array ['valid' => bool, 'errors' => array]
 */
function validate_login_form($data) {
    $errors = [];
    
    if (!validate_required($data['email'] ?? '')) {
        $errors['email'] = "Email is required";
    } elseif (!validate_email($data['email'])) {
        $errors['email'] = "Invalid email format";
    }
    
    if (!validate_required($data['password'] ?? '')) {
        $errors['password'] = "Password is required";
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Validate change password form
 * @param array $data
 * @return array ['valid' => bool, 'errors' => array]
 */
function validate_change_password_form($data) {
    $errors = [];
    
    if (!validate_required($data['current_password'] ?? '')) {
        $errors['current_password'] = "Current password is required";
    }
    
    if (!validate_required($data['new_password'] ?? '')) {
        $errors['new_password'] = "New password is required";
    } else {
        $password_validation = validate_password($data['new_password']);
        if (!$password_validation['valid']) {
            $errors['new_password'] = $password_validation['message'];
        }
    }
    
    if (!validate_required($data['confirm_password'] ?? '')) {
        $errors['confirm_password'] = "Please confirm your new password";
    } elseif ($data['new_password'] !== $data['confirm_password']) {
        $errors['confirm_password'] = "Passwords do not match";
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}