<?php
/**
 * Logout Handler
 * ITAM System - P-line Company
 */

require_once 'config/init.php';

// Create auth controller instance
$authController = new AuthController();

// Handle logout
$authController->logout();