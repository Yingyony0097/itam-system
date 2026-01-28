<?php
/**
 * Database Base Class
 * PDO Wrapper for secure database operations
 * ITAM System - P-line Company
 */

class Database {
    private $conn;
    private $stmt;
    
    /**
     * Constructor - Establish database connection
     */
    public function __construct() {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            // Log error (REQ-REL-004)
            error_log("Database Connection Error: " . $e->getMessage());
            die("Database connection failed. Please contact the administrator.");
        }
    }
    
    /**
     * Prepare SQL statement (REQ-SEC-003)
     */
    public function query($sql) {
        $this->stmt = $this->conn->prepare($sql);
        return $this;
    }
    
    /**
     * Bind parameter values
     */
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
        return $this;
    }
    
    /**
     * Execute prepared statement
     */
    public function execute() {
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            error_log("Query Execution Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Fetch single record
     */
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }
    
    /**
     * Fetch multiple records
     */
    public function all() {
        $this->execute();
        return $this->stmt->fetchAll();
    }
    
    /**
     * Get row count
     */
    public function rowCount() {
        return $this->stmt->rowCount();
    }
    
    /**
     * Get last inserted ID
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->conn->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->conn->rollBack();
    }
}