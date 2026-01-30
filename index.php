<?php
/**
 * ITAM System - Main Entry Point
 * Simple Router with Authentication Check
 *
 * @package ITAM System
 * @version 1.0
 * @organization P-line Company
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Initialize application
require_once __DIR__ . '/config/init.php';

// Get action from query string
$action = $_GET['action'] ?? 'index';

// Initialize AuthController
require_once __DIR__ . '/controllers/AuthController.php';
$authController = new AuthController();

// Handle authentication action
if ($action === 'authenticate') {
    $authController->authenticate();
    exit;
}

// Check if user is logged in
if (!is_logged_in()) {
    // Not logged in - show login page
    $authController->login();
    exit;
}

// User is logged in - route to appropriate dashboard
$role = get_user_role();

if ($role === 'Admin') {
    // Redirect to admin dashboard
    redirect('/views/admin/dashboard.php');
} else {
    // Redirect to user dashboard
    redirect('/views/user/dashboard.php');
}