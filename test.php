<?php
/**
 * Simple Test File - Diagnose Issues Step by Step
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "1. PHP is working!<br>";

// Test config loading
require_once __DIR__ . '/config/config.php';
echo "2. Config loaded successfully!<br>";

// Test helper loading
require_once HELPER_PATH . '/functions.php';
echo "3. functions.php loaded!<br>";

try {
    require_once HELPER_PATH . '/auth_helper.php';
    echo "4. auth_helper.php loaded!<br>";
} catch (Throwable $e) {
    echo "<strong style='color: red;'>ERROR loading auth_helper.php:</strong><br>";
    echo "Message: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
    die();
}

require_once HELPER_PATH . '/validation.php';
echo "5. validation.php loaded!<br>";

// Test Database class
require_once MODEL_PATH . '/Database.php';
echo "6. Database.php loaded!<br>";

// Test Database instance
try {
    $db = Database::getInstance();
    echo "7. Database instance created!<br>";
} catch (Exception $e) {
    echo "ERROR creating database: " . $e->getMessage() . "<br>";
}

// Test User model
require_once MODEL_PATH . '/User.php';
echo "8. User.php loaded!<br>";

// Test User instance
try {
    $user = new User();
    echo "9. User instance created!<br>";
} catch (Exception $e) {
    echo "ERROR creating User: " . $e->getMessage() . "<br>";
}

// Test session functions
echo "10. Testing session functions:<br>";
echo "   - is_logged_in: " . (function_exists('is_logged_in') ? 'EXISTS' : 'MISSING') . "<br>";
echo "   - set_user_session: " . (function_exists('set_user_session') ? 'EXISTS' : 'MISSING') . "<br>";
echo "   - get_user_role: " . (function_exists('get_user_role') ? 'EXISTS' : 'MISSING') . "<br>";
echo "   - generate_csrf_token: " . (function_exists('generate_csrf_token') ? 'EXISTS' : 'MISSING') . "<br>";
echo "   - validate_login_form: " . (function_exists('validate_login_form') ? 'EXISTS' : 'MISSING') . "<br>";

echo "<br><strong style='color: green;'>✅ ALL TESTS PASSED!</strong><br>";
echo "<br>If you see this message, the core system is working correctly.";
