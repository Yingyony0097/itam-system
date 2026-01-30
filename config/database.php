<?php
/**
 * Database Configuration and Connection
 * ITAM System - P-line Company
 * Security: REQ-SEC-003 - PDO Prepared Statements
 */

class Database {
    // Database credentials
    private $host = 'localhost';
    private $db_name = 'itam_system';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    private $conn = null;
    private $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false
    ];
    
    /**
     * Get database connection
     * @return PDO|null
     */
    public function connect() {
        try {
            if ($this->conn === null) {
                $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
                $this->conn = new PDO($dsn, $this->username, $this->password, $this->options);
            }
            return $this->conn;
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            die("Database connection failed. Please contact administrator.");
        }
    }
    
    /**
     * Close database connection
     */
    public function disconnect() {
        $this->conn = null;
    }
    
    /**
     * Get last insert ID
     * @return string
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}