<?php
/**
 * Dashboard Entry Point
 * ITAM System - P-line Company
 * 
 * Routes to appropriate dashboard based on user role
 */

// Initialize application
require_once 'config/init.php';

// Instantiate Dashboard Controller
$dashboardController = new DashboardController();

// Show dashboard
$dashboardController->index();
