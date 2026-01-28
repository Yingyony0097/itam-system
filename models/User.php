<?php
/**
 * User Model
 * ITAM System - P-line Company
 * Security: REQ-SEC-001 (password_hash), REQ-SEC-003 (PDO)
 */

class User {
    private $db;
    private $table = 'users';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Find user by email (REQ-AUTH-001)
     * @param string $email
     * @return array|false
     */
    public function findByEmail($email) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
            return $this->db->fetch($sql, [':email' => $email]);
        } catch (PDOException $e) {
            error_log("User findByEmail Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find user by ID
     * @param int $user_id
     * @return array|false
     */
    public function findById($user_id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id LIMIT 1";
            return $this->db->fetch($sql, [':user_id' => $user_id]);
        } catch (PDOException $e) {
            error_log("User findById Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verify user password (REQ-AUTH-006, REQ-SEC-001)
     * @param string $email
     * @param string $password
     * @return array|false Returns user data if valid, false otherwise
     */
    public function verifyPassword($email, $password) {
        try {
            $user = $this->findByEmail($email);
            
            if (!$user) {
                return false;
            }
            
            // Verify password using password_verify (REQ-SEC-001)
            if (password_verify($password, $user['password'])) {
                // Check if user is active
                if ($user['is_active'] == 1) {
                    return $user;
                }
            }
            
            return false;
        } catch (Exception $e) {
            error_log("User verifyPassword Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all users (REQ-USER-002)
     * @param array $filters Optional filters
     * @return array
     */
    public function getAll($filters = []) {
        try {
            $sql = "SELECT user_id, name, email, role, is_active, created_at, updated_at 
                    FROM {$this->table}";
            
            $where = [];
            $params = [];
            
            // Filter by role
            if (!empty($filters['role'])) {
                $where[] = "role = :role";
                $params[':role'] = $filters['role'];
            }
            
            // Filter by status
            if (isset($filters['is_active'])) {
                $where[] = "is_active = :is_active";
                $params[':is_active'] = $filters['is_active'];
            }
            
            // Search by name or email
            if (!empty($filters['search'])) {
                $where[] = "(name LIKE :search OR email LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            // Pagination
            if (isset($filters['limit']) && isset($filters['offset'])) {
                $sql .= " LIMIT :limit OFFSET :offset";
                $stmt = $this->db->getConnection()->prepare($sql);
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
                $stmt->bindValue(':limit', (int)$filters['limit'], PDO::PARAM_INT);
                $stmt->bindValue(':offset', (int)$filters['offset'], PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll();
            }
            
            return $this->db->fetchAll($sql, $params);
        } catch (PDOException $e) {
            error_log("User getAll Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total users
     * @param array $filters
     * @return int
     */
    public function count($filters = []) {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table}";
            
            $where = [];
            $params = [];
            
            if (!empty($filters['role'])) {
                $where[] = "role = :role";
                $params[':role'] = $filters['role'];
            }
            
            if (isset($filters['is_active'])) {
                $where[] = "is_active = :is_active";
                $params[':is_active'] = $filters['is_active'];
            }
            
            if (!empty($filters['search'])) {
                $where[] = "(name LIKE :search OR email LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            
            return (int)$this->db->fetchColumn($sql, $params);
        } catch (PDOException $e) {
            error_log("User count Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Create new user (REQ-USER-001, REQ-SEC-001)
     * @param array $data
     * @return int|false Returns user_id if successful
     */
    public function create($data) {
        try {
            // Hash password using password_hash (REQ-SEC-001)
            $hashed_password = password_hash($data['password'], PASSWORD_BCRYPT);
            
            $sql = "INSERT INTO {$this->table} 
                    (name, email, password, role, is_active) 
                    VALUES (:name, :email, :password, :role, :is_active)";
            
            $params = [
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':password' => $hashed_password,
                ':role' => $data['role'] ?? 'User',
                ':is_active' => $data['is_active'] ?? 1
            ];
            
            $this->db->query($sql, $params);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("User create Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update user (REQ-USER-003)
     * @param int $user_id
     * @param array $data
     * @return bool
     */
    public function update($user_id, $data) {
        try {
            $fields = [];
            $params = [':user_id' => $user_id];
            
            // Update name
            if (isset($data['name'])) {
                $fields[] = "name = :name";
                $params[':name'] = $data['name'];
            }
            
            // Update email
            if (isset($data['email'])) {
                $fields[] = "email = :email";
                $params[':email'] = $data['email'];
            }
            
            // Update password if provided (REQ-SEC-001)
            if (!empty($data['password'])) {
                $fields[] = "password = :password";
                $params[':password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            }
            
            // Update role
            if (isset($data['role'])) {
                $fields[] = "role = :role";
                $params[':role'] = $data['role'];
            }
            
            // Update active status
            if (isset($data['is_active'])) {
                $fields[] = "is_active = :is_active";
                $params[':is_active'] = $data['is_active'];
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $sql = "UPDATE {$this->table} SET " . implode(", ", $fields) . 
                   " WHERE user_id = :user_id";
            
            $stmt = $this->db->query($sql, $params);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("User update Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Change user password (REQ-AUTH-005, REQ-PROFILE-002)
     * @param int $user_id
     * @param string $new_password
     * @return bool
     */
    public function changePassword($user_id, $new_password) {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            
            $sql = "UPDATE {$this->table} SET password = :password WHERE user_id = :user_id";
            
            $params = [
                ':password' => $hashed_password,
                ':user_id' => $user_id
            ];
            
            $stmt = $this->db->query($sql, $params);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("User changePassword Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Deactivate user (REQ-USER-004)
     * @param int $user_id
     * @return bool
     */
    public function deactivate($user_id) {
        try {
            $sql = "UPDATE {$this->table} SET is_active = 0 WHERE user_id = :user_id";
            $stmt = $this->db->query($sql, [':user_id' => $user_id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("User deactivate Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Activate user
     * @param int $user_id
     * @return bool
     */
    public function activate($user_id) {
        try {
            $sql = "UPDATE {$this->table} SET is_active = 1 WHERE user_id = :user_id";
            $stmt = $this->db->query($sql, [':user_id' => $user_id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("User activate Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if user has assigned assets (REQ-DB-008)
     * @param int $user_id
     * @return bool
     */
    public function hasAssignedAssets($user_id) {
        try {
            $sql = "SELECT COUNT(*) FROM assets WHERE assigned_to = :user_id";
            $count = $this->db->fetchColumn($sql, [':user_id' => $user_id]);
            return $count > 0;
        } catch (PDOException $e) {
            error_log("User hasAssignedAssets Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user's assigned assets (REQ-USER-005, REQ-DASH-002)
     * @param int $user_id
     * @return array
     */
    public function getAssignedAssets($user_id) {
        try {
            $sql = "SELECT a.*, 
                           (SELECT COUNT(*) FROM check_logs WHERE asset_id = a.asset_id) as check_count
                    FROM assets a
                    WHERE a.assigned_to = :user_id
                    ORDER BY a.assigned_date DESC";
            
            return $this->db->fetchAll($sql, [':user_id' => $user_id]);
        } catch (PDOException $e) {
            error_log("User getAssignedAssets Error: " . $e->getMessage());
            return [];
        }
    }
}