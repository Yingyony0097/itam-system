<?php
/**
 * Database Configuration
 * ITAM System - P-line Company
 * 
 * Note: Database constants are now defined in config.php
 * This file is kept for backward compatibility
 */

// Include main config if not already loaded
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/config.php';
}

// All database constants are now defined in config.php:
// - DB_HOST
// - DB_NAME
// - DB_USER
// - DB_PASS
// - DB_CHARSET
// - DB_OPTIONS