<?php
/**
 * AuthController - Authentication Controller
 * Handles login, logout, and authentication
 * 
 * @package ITAM System
 * @version 1.0
 * @organization P-line Company
 */

class AuthController
{
    private $userModel;
    
    /**
     * Constructor - Initialize User model
     */
    public function __construct()
    {
        $this->userModel = new User();
    }
    
    /**
     * Display login page
     * REQ-AUTH-001: User login with email and password
     */
    public function login()
    {
        // If already logged in, redirect to dashboard
        if (is_logged_in()) {
            $this->redirectToDashboard();
        }
        
        // Display login view
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    /**
     * Handle login authentication
     * REQ-AUTH-001, REQ-AUTH-006: Validate credentials
     * REQ-SEC-001: Password verification with bcrypt
     * REQ-SEC-007: CSRF protection
     */
    public function authenticate()
    {
        // Only allow POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php');
            return;
        }
        
        // Verify CSRF token (REQ-SEC-007)
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash('error', 'Invalid security token. Please try again.');
            redirect('/index.php');
            return;
        }
        
        // Sanitize inputs (REQ-SEC-006)
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validate login form (REQ-VAL-001, REQ-VAL-002)
        $errors = validate_login_form([
            'email' => $email,
            'password' => $password
        ]);
        
        if (!empty($errors)) {
            set_flash('error', implode('<br>', $errors));
            redirect('/index.php');
            return;
        }
        
        // Verify credentials using User model (REQ-AUTH-006, REQ-SEC-001)
        $user = $this->userModel->verifyPassword($email, $password);
        
        if (!$user) {
            set_flash('error', 'Invalid email or password.');
            redirect('/index.php');
            return;
        }
        
        // Check if user account is active
        if (!$user['is_active']) {
            set_flash('error', 'Your account has been deactivated. Please contact administrator.');
            redirect('/index.php');
            return;
        }
        
        // Set user session (REQ-AUTH-003)
        set_user_session(
            $user['user_id'],
            $user['name'],
            $user['email'],
            $user['role']
        );
        
        // Set remember me cookie if checked
        if ($remember) {
            $this->setRememberMeCookie($user['user_id']);
        }
        
        // Success message
        set_flash('success', 'Welcome back, ' . escape($user['name']) . '!');
        
        // Redirect to appropriate dashboard
        $this->redirectToDashboard();
    }
    
    /**
     * Logout user
     * REQ-AUTH-004: Logout functionality
     */
    public function logout()
    {
        // Clear user session
        clear_user_session();
        
        // Clear remember me cookie
        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600, '/', '', true, true);
        }
        
        // Set logout message
        set_flash('success', 'You have been logged out successfully.');
        
        // Redirect to login page
        redirect('/index.php');
    }
    
    /**
     * Redirect to appropriate dashboard based on role
     * REQ-AUTH-002: Role-based access control
     */
    private function redirectToDashboard()
    {
        $role = get_user_role();
        
        if ($role === 'Admin') {
            redirect('/views/admin/dashboard.php');
        } else {
            redirect('/views/user/dashboard.php');
        }
    }
    
    /**
     * Set remember me cookie
     * 
     * @param int $user_id User ID
     */
    private function setRememberMeCookie($user_id)
    {
        // Generate secure token
        $token = bin2hex(random_bytes(32));
        
        // Set cookie for 30 days
        $expiry = time() + (30 * 24 * 60 * 60);
        setcookie(
            'remember_me',
            $user_id . ':' . $token,
            $expiry,
            '/',
            '',
            true, // Secure
            true  // HttpOnly
        );
    }
}