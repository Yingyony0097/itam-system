<?php
/**
 * Logout Handler
 * ITAM System - P-line Company
 * 
 * REQ-AUTH-004: Logout functionality
 * Clears user session and redirects to login page
 */

require_once 'config/init.php';

// Create auth controller instance
$authController = new AuthController();

// Handle logout
$authController->logout();