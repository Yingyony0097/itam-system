<?php
/**
 * User Model
 * Handles user authentication and data operations
 * ITAM System - P-line Company
 */

class User extends Database {
    
    /**
     * Authenticate user login (REQ-AUTH-001, REQ-AUTH-006)
     * 
     * @param string $email
     * @param string $password
     * @return array|false User data or false
     */
    public function login($email, $password) {
        $sql = "SELECT user_id, name, email, password, role, is_active 
                FROM users 
                WHERE email = :email AND is_active = 1 
                LIMIT 1";
        
        $this->query($sql)->bind(':email', $email);
        $user = $this->single();
        
        // Verify password using bcrypt (REQ-SEC-001)
        if ($user && password_verify($password, $user['password'])) {
            // Remove password from return data
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Get user by ID
     * 
     * @param int $userId
     * @return array|false User data or false
     */
    public function getUserById($userId) {
        $sql = "SELECT user_id, name, email, role, is_active, created_at, updated_at 
                FROM users 
                WHERE user_id = :user_id 
                LIMIT 1";
        
        $this->query($sql)->bind(':user_id', $userId);
        return $this->single();
    }
    
    /**
     * Get all users
     * 
     * @return array List of users
     */
    public function getAllUsers() {
        $sql = "SELECT user_id, name, email, role, is_active, created_at 
                FROM users 
                ORDER BY created_at DESC";
        
        $this->query($sql);
        return $this->all();
    }
    
    /**
     * Create new user (REQ-USER-001)
     * 
     * @param array $data User data
     * @return int|false New user ID or false
     */
    public function createUser($data) {
        $sql = "INSERT INTO users (name, email, password, role) 
                VALUES (:name, :email, :password, :role)";
        
        // Hash password with bcrypt (REQ-SEC-001)
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $this->query($sql)
            ->bind(':name', $data['name'])
            ->bind(':email', $data['email'])
            ->bind(':password', $hashedPassword)
            ->bind(':role', $data['role']);
        
        if ($this->execute()) {
            return $this->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update user password (REQ-PROFILE-002)
     * 
     * @param int $userId
     * @param string $newPassword
     * @return bool Success status
     */
    public function updatePassword($userId, $newPassword) {
        $sql = "UPDATE users 
                SET password = :password, updated_at = CURRENT_TIMESTAMP 
                WHERE user_id = :user_id";
        
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        
        $this->query($sql)
            ->bind(':password', $hashedPassword)
            ->bind(':user_id', $userId);
        
        return $this->execute();
    }
    
    /**
     * Check if email exists (REQ-USER-006)
     * 
     * @param string $email
     * @param int|null $excludeUserId Exclude this user ID from check
     * @return bool Email exists
     */
    public function emailExists($email, $excludeUserId = null) {
        $sql = "SELECT user_id FROM users WHERE email = :email";
        
        if ($excludeUserId) {
            $sql .= " AND user_id != :user_id";
        }
        $sql .= " LIMIT 1";
        
        $this->query($sql)->bind(':email', $email);
        
        if ($excludeUserId) {
            $this->bind(':user_id', $excludeUserId);
        }
        
        return $this->single() !== false;
    }
}
