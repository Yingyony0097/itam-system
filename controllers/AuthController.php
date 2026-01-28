<?php
/**
 * Authentication Controller
 * Handles user login, logout, and session management
 * ITAM System - P-line Company
 */

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Handle user login (REQ-AUTH-001)
     * 
     * @return void
     */
    public function login() {
        // If already logged in, redirect to appropriate dashboard
        if (isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
            return;
        }
        
        // Show login form
        require_once VIEWS_PATH . '/auth/login.php';
    }
    
    /**
     * Process login form submission
     * 
     * @return void
     */
    private function processLogin() {
        // Verify CSRF token (REQ-SEC-007)
        if (!csrf_verify($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request. Please try again.');
            redirect('index.php');
            return;
        }
        
        // Sanitize inputs (REQ-SEC-006)
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validate inputs
        $errors = [];
        
        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!validateEmail($email)) {
            $errors[] = 'Invalid email format';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required';
        }
        
        if (!empty($errors)) {
            $_SESSION['login_errors'] = $errors;
            $_SESSION['old_email'] = $email;
            redirect('index.php');
            return;
        }
        
        // Attempt login (REQ-AUTH-006)
        $user = $this->userModel->login($email, $password);
        
        if ($user) {
            // Set session data (REQ-AUTH-003)
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in_at'] = time();
            
            // Regenerate session ID for security
            session_regenerate_id(true);
            
            // Set success message
            setFlash('success', 'Welcome back, ' . $user['name'] . '!');
            
            // Redirect to dashboard (REQ-UI-004)
            $this->redirectToDashboard();
        } else {
            // Login failed
            setFlash('error', 'Invalid email or password');
            $_SESSION['old_email'] = $email;
            redirect('index.php');
        }
    }
    
    /**
     * Handle user logout (REQ-AUTH-004)
     * 
     * @return void
     */
    public function logout() {
        // Destroy session
        session_unset();
        session_destroy();
        
        // Start new session for flash message
        session_start();
        setFlash('success', 'You have been logged out successfully');
        
        redirect('index.php');
    }
    
    /**
     * Redirect to appropriate dashboard based on role
     * 
     * @return void
     */
    private function redirectToDashboard() {
        // Check if there's a redirect URL stored
        if (isset($_SESSION['redirect_after_login'])) {
            $redirectUrl = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            redirect($redirectUrl);
            return;
        }
        
        // Redirect based on role (REQ-UI-004)
        if (isAdmin()) {
            redirect('views/admin/dashboard.php');
        } else {
            redirect('views/user/dashboard.php');
        }
    }
}