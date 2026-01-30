<?php
/**
 * User Controller
 * ITAM System - P-line Company
 *
 * Handles user management operations (Admin Only)
 * Security: REQ-SEC-007 (CSRF Protection), REQ-SEC-001 (Password Hashing)
 */

class UserController {
    private $userModel;

    public function __construct() {
        // Check if user is logged in
        if (!is_logged_in()) {
            redirect('/index.php');
            exit();
        }

        // Only admins can access user management
        if (!is_admin()) {
            redirect('/views/errors/403.php');
            exit();
        }

        $this->userModel = new User();
    }

    /**
     * Display users list
     * REQ-USER-002
     */
    public function index() {
        // Get filters from GET parameters
        $filters = [
            'search' => $_GET['search'] ?? '',
            'role' => $_GET['role'] ?? '',
            'is_active' => isset($_GET['is_active']) && $_GET['is_active'] !== '' ? (int)$_GET['is_active'] : null
        ];

        // Clean up empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== '' && $value !== null;
        });

        // Get all users with filters
        $users = $this->userModel->getAll($filters);

        // Get total count for stats
        $total_users = $this->userModel->count();
        $active_users = $this->userModel->count(['is_active' => 1]);
        $admin_count = $this->userModel->count(['role' => 'Admin']);

        // Load view
        require_once __DIR__ . '/../views/admin/users/index.php';
    }

    /**
     * Show create user form
     * REQ-USER-001
     */
    public function create() {
        // Generate CSRF token
        $csrf_token = generate_csrf_token();

        // Load view
        require_once __DIR__ . '/../views/admin/users/create.php';
    }

    /**
     * Store new user
     * REQ-USER-001, REQ-SEC-001
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        // Verify CSRF token (REQ-SEC-007)
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash_message('error', 'Invalid CSRF token');
            redirect('/controllers/UserController.php?action=create');
            exit();
        }

        // Sanitize input data (REQ-SEC-004, REQ-SEC-006)
        $data = [
            'name' => sanitize_input($_POST['name'] ?? ''),
            'email' => sanitize_input($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '', // Don't sanitize password
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'role' => sanitize_input($_POST['role'] ?? 'User'),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        // Validate data
        $errors = [];

        // Validate name
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        } elseif (strlen($data['name']) < 3) {
            $errors['name'] = 'Name must be at least 3 characters';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'Name must not exceed 100 characters';
        }

        // Validate email
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } else {
            // Check if email already exists
            $existing = $this->userModel->findByEmail($data['email']);
            if ($existing) {
                $errors['email'] = 'Email already exists';
            }
        }

        // Validate password (REQ-SEC-001)
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        } elseif ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Passwords do not match';
        }

        // Validate role
        if (!in_array($data['role'], ['Admin', 'User'])) {
            $errors['role'] = 'Invalid role selected';
        }

        // If there are errors, redirect back with errors
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $data;
            redirect('/controllers/UserController.php?action=create');
            exit();
        }

        // Create user
        $user_id = $this->userModel->create($data);

        if ($user_id) {
            set_flash_message('success', 'User "' . htmlspecialchars($data['name']) . '" created successfully');
            redirect('/controllers/UserController.php?action=index');
        } else {
            set_flash_message('error', 'Failed to create user');
            redirect('/controllers/UserController.php?action=create');
        }
    }

    /**
     * Show edit user form
     * REQ-USER-003
     */
    public function edit() {
        $user_id = $_GET['id'] ?? null;

        if (!$user_id) {
            set_flash_message('error', 'User ID is required');
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        // Get user details
        $user = $this->userModel->findById($user_id);

        if (!$user) {
            set_flash_message('error', 'User not found');
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        // Generate CSRF token
        $csrf_token = generate_csrf_token();

        // Load view
        require_once __DIR__ . '/../views/admin/users/edit.php';
    }

    /**
     * Update user
     * REQ-USER-003
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        // Verify CSRF token (REQ-SEC-007)
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash_message('error', 'Invalid CSRF token');
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        $user_id = intval($_POST['user_id'] ?? 0);

        if (!$user_id) {
            set_flash_message('error', 'User ID is required');
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        // Get existing user
        $existing_user = $this->userModel->findById($user_id);

        if (!$existing_user) {
            set_flash_message('error', 'User not found');
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        // Sanitize input data (REQ-SEC-004, REQ-SEC-006)
        $data = [
            'name' => sanitize_input($_POST['name'] ?? ''),
            'email' => sanitize_input($_POST['email'] ?? ''),
            'role' => sanitize_input($_POST['role'] ?? 'User'),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        // Handle password change (optional)
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
            $password_confirm = $_POST['password_confirm'] ?? '';
        }

        // Validate data
        $errors = [];

        // Validate name
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        } elseif (strlen($data['name']) < 3) {
            $errors['name'] = 'Name must be at least 3 characters';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'Name must not exceed 100 characters';
        }

        // Validate email
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } else {
            // Check if email already exists for different user
            $email_user = $this->userModel->findByEmail($data['email']);
            if ($email_user && $email_user['user_id'] != $user_id) {
                $errors['email'] = 'Email already exists';
            }
        }

        // Validate password if provided (REQ-SEC-001)
        if (isset($data['password'])) {
            if (strlen($data['password']) < 6) {
                $errors['password'] = 'Password must be at least 6 characters';
            } elseif ($data['password'] !== $password_confirm) {
                $errors['password_confirm'] = 'Passwords do not match';
            }
        }

        // Validate role
        if (!in_array($data['role'], ['Admin', 'User'])) {
            $errors['role'] = 'Invalid role selected';
        }

        // Prevent deactivating yourself
        if ($user_id == $_SESSION['user_id'] && $data['is_active'] == 0) {
            $errors['is_active'] = 'You cannot deactivate your own account';
        }

        // If there are errors, redirect back with errors
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $data;
            redirect('/controllers/UserController.php?action=edit&id=' . $user_id);
            exit();
        }

        // Update user
        $success = $this->userModel->update($user_id, $data);

        if ($success) {
            set_flash_message('success', 'User "' . htmlspecialchars($data['name']) . '" updated successfully');
            redirect('/controllers/UserController.php?action=index');
        } else {
            set_flash_message('error', 'Failed to update user');
            redirect('/controllers/UserController.php?action=edit&id=' . $user_id);
        }
    }

    /**
     * Delete user
     * REQ-USER-004, REQ-DB-008
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        // Verify CSRF token (REQ-SEC-007)
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash_message('error', 'Invalid CSRF token');
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        $user_id = intval($_POST['user_id'] ?? 0);

        if (!$user_id) {
            set_flash_message('error', 'User ID is required');
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        // Get user details
        $user = $this->userModel->findById($user_id);

        if (!$user) {
            set_flash_message('error', 'User not found');
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        // Prevent self-deletion
        if ($user_id == $_SESSION['user_id']) {
            set_flash_message('error', 'You cannot delete your own account');
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        // Check if user has assigned assets (REQ-DB-008)
        if ($this->userModel->hasAssignedAssets($user_id)) {
            set_flash_message('error', 'Cannot delete user "' . htmlspecialchars($user['name']) . '" because they have assets assigned. Please check-in all assets first.');
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        // Deactivate user instead of deleting (safer approach)
        $success = $this->userModel->deactivate($user_id);

        if ($success) {
            set_flash_message('success', 'User "' . htmlspecialchars($user['name']) . '" has been deactivated successfully');
        } else {
            set_flash_message('error', 'Failed to deactivate user');
        }

        redirect('/controllers/UserController.php?action=index');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        // Verify CSRF token (REQ-SEC-007)
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash_message('error', 'Invalid CSRF token');
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        $user_id = intval($_POST['user_id'] ?? 0);

        if (!$user_id) {
            set_flash_message('error', 'User ID is required');
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        // Get user details
        $user = $this->userModel->findById($user_id);

        if (!$user) {
            set_flash_message('error', 'User not found');
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        // Prevent deactivating yourself
        if ($user_id == $_SESSION['user_id']) {
            set_flash_message('error', 'You cannot change your own account status');
            redirect('/controllers/UserController.php?action=index');
            exit();
        }

        // Toggle status
        if ($user['is_active'] == 1) {
            $success = $this->userModel->deactivate($user_id);
            $action = 'deactivated';
        } else {
            $success = $this->userModel->activate($user_id);
            $action = 'activated';
        }

        if ($success) {
            set_flash_message('success', 'User "' . htmlspecialchars($user['name']) . '" has been ' . $action . ' successfully');
        } else {
            set_flash_message('error', 'Failed to change user status');
        }

        redirect('/controllers/UserController.php?action=index');
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
$controller = new UserController();

// Route to appropriate method
switch ($action) {
    case 'index':
        $controller->index();
        break;

    case 'create':
        $controller->create();
        break;

    case 'store':
        $controller->store();
        break;

    case 'edit':
        $controller->edit();
        break;

    case 'update':
        $controller->update();
        break;

    case 'delete':
        $controller->delete();
        break;

    case 'toggleStatus':
        $controller->toggleStatus();
        break;

    default:
        $controller->index();
        break;
}
