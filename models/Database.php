<?php
/**
 * Database PDO Wrapper Class
 * ITAM System - P-line Company
 * Security: REQ-SEC-003 - PDO Prepared Statements
 * Pattern: Singleton
 */

class Database {
    private static $instance = null;
    private $conn = null;
    
    // Database credentials
    private $host = 'localhost';
    private $db_name = 'itam_system';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    private $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false
    ];
    
    /**
     * Private constructor for singleton pattern
     */
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Get singleton instance
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Establish database connection
     * @return PDO
     */
    private function connect() {
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
     * Get PDO connection
     * @return PDO
     */
    public function getConnection() {
        if ($this->conn === null) {
            $this->connect();
        }
        return $this->conn;
    }
    
    /**
     * Execute a query with parameters
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query Error: " . $e->getMessage() . " | SQL: " . $sql);
            throw $e;
        }
    }
    
    /**
     * Fetch all rows
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Fetch single row
     * @param string $sql
     * @param array $params
     * @return array|false
     */
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Fetch single column value
     * @param string $sql
     * @param array $params
     * @return mixed
     */
    public function fetchColumn($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn();
    }
    
    /**
     * Get last insert ID
     * @return string
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    /**
     * Get row count from last statement
     * @param PDOStatement $stmt
     * @return int
     */
    public function rowCount($stmt) {
        return $stmt->rowCount();
    }
    
    /**
     * Begin transaction
     * @return bool
     */
    public function beginTransaction() {
        try {
            return $this->conn->beginTransaction();
        } catch (PDOException $e) {
            error_log("Transaction Begin Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Commit transaction
     * @return bool
     */
    public function commit() {
        try {
            return $this->conn->commit();
        } catch (PDOException $e) {
            error_log("Transaction Commit Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Rollback transaction
     * @return bool
     */
    public function rollback() {
        try {
            return $this->conn->rollBack();
        } catch (PDOException $e) {
            error_log("Transaction Rollback Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if in transaction
     * @return bool
     */
    public function inTransaction() {
        return $this->conn->inTransaction();
    }
    
    /**
     * Prevent cloning of singleton
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization of singleton
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}