<?php
/**
 * Profile Controller
 * ITAM System - P-line Company
 *
 * Handles user profile and password management (All Users)
 * Security: REQ-AUTH-005, REQ-SEC-008 (Current Password Validation)
 */

class ProfileController {
    private $userModel;
    private $assetModel;
    private $checkLogModel;

    public function __construct() {
        // Check if user is logged in
        if (!is_logged_in()) {
            redirect('/index.php');
            exit();
        }

        $this->userModel = new User();
        $this->assetModel = new Asset();
        $this->checkLogModel = new CheckLog();
    }

    /**
     * Display user profile
     * REQ-PROFILE-001
     */
    public function index() {
        $user_id = $_SESSION['user_id'];

        // Get user details
        $user = $this->userModel->findById($user_id);

        if (!$user) {
            set_flash_message('error', 'User not found');
            redirect('/dashboard.php');
            exit();
        }

        // Get assigned assets
        $assigned_assets = $this->userModel->getAssignedAssets($user_id);

        // Get check log history
        $check_history = $this->checkLogModel->getByUser($user_id, ['limit' => 10, 'offset' => 0]);

        // Get statistics
        $total_assigned = count($assigned_assets);
        $current_in_use = count(array_filter($assigned_assets, function($asset) {
            return $asset['status'] === 'In Use';
        }));
        $history_count = $this->checkLogModel->count(['user_id' => $user_id]);

        // Generate CSRF token for password change
        $csrf_token = generate_csrf_token();

        // Load view
        require_once __DIR__ . '/../views/user/profile.php';
    }

    /**
     * Change password
     * REQ-AUTH-005, REQ-SEC-008
     * IMPORTANT: Must validate current password before allowing change
     */
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/controllers/ProfileController.php?action=index');
            exit();
        }

        // Verify CSRF token (REQ-SEC-007)
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash_message('error', 'Invalid CSRF token');
            redirect('/controllers/ProfileController.php?action=index');
            exit();
        }

        $user_id = $_SESSION['user_id'];

        // Sanitize input (don't sanitize passwords)
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate inputs
        $errors = [];

        // Validate current password (REQ-SEC-008)
        if (empty($current_password)) {
            $errors['current_password'] = 'Current password is required';
        } else {
            // Verify current password
            $user = $this->userModel->findById($user_id);
            if (!$user || !password_verify($current_password, $user['password'])) {
                $errors['current_password'] = 'Current password is incorrect';
            }
        }

        // Validate new password (REQ-SEC-001)
        if (empty($new_password)) {
            $errors['new_password'] = 'New password is required';
        } elseif (strlen($new_password) < 6) {
            $errors['new_password'] = 'New password must be at least 6 characters';
        } elseif ($new_password === $current_password) {
            $errors['new_password'] = 'New password must be different from current password';
        }

        // Validate password confirmation
        if (empty($confirm_password)) {
            $errors['confirm_password'] = 'Please confirm your new password';
        } elseif ($new_password !== $confirm_password) {
            $errors['confirm_password'] = 'Passwords do not match';
        }

        // If there are errors, redirect back with errors
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            set_flash_message('error', 'Please fix the errors below');
            redirect('/controllers/ProfileController.php?action=index');
            exit();
        }

        // Change password
        $success = $this->userModel->changePassword($user_id, $new_password);

        if ($success) {
            set_flash_message('success', 'Password changed successfully');
        } else {
            set_flash_message('error', 'Failed to change password');
        }

        redirect('/controllers/ProfileController.php?action=index');
    }

    /**
     * Update profile information (name, email)
     * REQ-PROFILE-003
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/controllers/ProfileController.php?action=index');
            exit();
        }

        // Verify CSRF token
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash_message('error', 'Invalid CSRF token');
            redirect('/controllers/ProfileController.php?action=index');
            exit();
        }

        $user_id = $_SESSION['user_id'];

        // Sanitize input data
        $data = [
            'name' => sanitize_input($_POST['name'] ?? ''),
            'email' => sanitize_input($_POST['email'] ?? '')
        ];

        // Validate data
        $errors = [];

        // Validate name
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        } elseif (strlen($data['name']) < 3) {
            $errors['name'] = 'Name must be at least 3 characters';
        }

        // Validate email
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } else {
            // Check if email already exists for different user
            $existing = $this->userModel->findByEmail($data['email']);
            if ($existing && $existing['user_id'] != $user_id) {
                $errors['email'] = 'Email already exists';
            }
        }

        // If there are errors, redirect back with errors
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $data;
            set_flash_message('error', 'Please fix the errors below');
            redirect('/controllers/ProfileController.php?action=index');
            exit();
        }

        // Update profile
        $success = $this->userModel->update($user_id, $data);

        if ($success) {
            // Update session data
            $_SESSION['user_name'] = $data['name'];
            $_SESSION['user_email'] = $data['email'];

            set_flash_message('success', 'Profile updated successfully');
        } else {
            set_flash_message('error', 'Failed to update profile');
        }

        redirect('/controllers/ProfileController.php?action=index');
    }
}

// ====================================
// Routing Logic
// ====================================

// Initialize config and autoload
require_once __DIR__ . '/../config/init.php';

// Get action from URL
$action = $_GET['action'] ?? 'index';

// Create controller instance
$controller = new ProfileController();

// Route to appropriate method
switch ($action) {
    case 'index':
        $controller->index();
        break;

    case 'changePassword':
        $controller->changePassword();
        break;

    case 'updateProfile':
        $controller->updateProfile();
        break;

    default:
        $controller->index();
        break;
}
