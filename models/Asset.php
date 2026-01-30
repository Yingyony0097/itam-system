<?php
/**
 * Asset Model
 * ITAM System - P-line Company
 * Security: REQ-SEC-003 (PDO Prepared Statements)
 */

class Asset {
    private $db;
    private $table = 'assets';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Generate auto-incremented asset code (REQ-ASSET-001)
     * Format: AST-001, AST-002, etc.
     * @return string
     */
    public function generateAssetCode() {
        try {
            // Get the highest asset_id
            $sql = "SELECT MAX(asset_id) as max_id FROM {$this->table}";
            $result = $this->db->fetch($sql);
            
            $next_id = ($result && $result['max_id']) ? $result['max_id'] + 1 : 1;
            
            // Format: AST-001, AST-002, etc. (3 digits, zero-padded)
            return 'AST-' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
        } catch (PDOException $e) {
            error_log("Asset generateAssetCode Error: " . $e->getMessage());
            // Fallback to timestamp-based code
            return 'AST-' . time();
        }
    }
    
    /**
     * Get all assets with filters (REQ-ASSET-002, REQ-ASSET-007, REQ-ASSET-008)
     * @param array $filters
     * @return array
     */
    public function getAll($filters = []) {
        try {
            $sql = "SELECT a.*, 
                           u.name as assigned_user_name,
                           u.email as assigned_user_email
                    FROM {$this->table} a
                    LEFT JOIN users u ON a.assigned_to = u.user_id";
            
            $where = [];
            $params = [];
            
            // Filter by category (REQ-ASSET-008)
            if (!empty($filters['category'])) {
                $where[] = "a.category = :category";
                $params[':category'] = $filters['category'];
            }
            
            // Filter by status (REQ-ASSET-008)
            if (!empty($filters['status'])) {
                $where[] = "a.status = :status";
                $params[':status'] = $filters['status'];
            }
            
            // Search by name, serial, code (REQ-ASSET-007)
            if (!empty($filters['search'])) {
                $where[] = "(a.asset_name LIKE :search OR a.serial_number LIKE :search OR a.asset_code LIKE :search OR a.brand LIKE :search OR a.model LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }
            
            // Filter by assigned user
            if (isset($filters['assigned_to'])) {
                if ($filters['assigned_to'] === 'unassigned') {
                    $where[] = "a.assigned_to IS NULL";
                } else if (is_numeric($filters['assigned_to'])) {
                    $where[] = "a.assigned_to = :assigned_to";
                    $params[':assigned_to'] = $filters['assigned_to'];
                }
            }
            
            // Filter by date range
            if (!empty($filters['date_from'])) {
                $where[] = "a.purchase_date >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $where[] = "a.purchase_date <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            
            // Order by
            $order_by = $filters['order_by'] ?? 'created_at';
            $order_dir = $filters['order_dir'] ?? 'DESC';
            $sql .= " ORDER BY a.{$order_by} {$order_dir}";
            
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
            error_log("Asset getAll Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total assets
     * @param array $filters
     * @return int
     */
    public function count($filters = []) {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} a";
            
            $where = [];
            $params = [];
            
            if (!empty($filters['category'])) {
                $where[] = "a.category = :category";
                $params[':category'] = $filters['category'];
            }
            
            if (!empty($filters['status'])) {
                $where[] = "a.status = :status";
                $params[':status'] = $filters['status'];
            }
            
            if (!empty($filters['search'])) {
                $where[] = "(a.asset_name LIKE :search OR a.serial_number LIKE :search OR a.asset_code LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }
            
            if (isset($filters['assigned_to'])) {
                if ($filters['assigned_to'] === 'unassigned') {
                    $where[] = "a.assigned_to IS NULL";
                } else if (is_numeric($filters['assigned_to'])) {
                    $where[] = "a.assigned_to = :assigned_to";
                    $params[':assigned_to'] = $filters['assigned_to'];
                }
            }
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            
            return (int)$this->db->fetchColumn($sql, $params);
        } catch (PDOException $e) {
            error_log("Asset count Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get asset statistics for dashboard (REQ-DASH-001)
     * @return array
     */
    public function getStatistics() {
        try {
            $stats = [];
            
            // Total assets
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            $result = $this->db->fetch($sql);
            $stats['total'] = $result['total'] ?? 0;
            
            // Available assets
            $sql = "SELECT COUNT(*) as available FROM {$this->table} WHERE status = 'Available'";
            $result = $this->db->fetch($sql);
            $stats['available'] = $result['available'] ?? 0;
            
            // In Use assets
            $sql = "SELECT COUNT(*) as in_use FROM {$this->table} WHERE status = 'In Use'";
            $result = $this->db->fetch($sql);
            $stats['in_use'] = $result['in_use'] ?? 0;
            
            // Total value
            $sql = "SELECT SUM(purchase_price) as total_value FROM {$this->table}";
            $result = $this->db->fetch($sql);
            $stats['total_value'] = $result['total_value'] ?? 0;
            
            // Assets by category
            $sql = "SELECT category, COUNT(*) as count 
                    FROM {$this->table} 
                    GROUP BY category 
                    ORDER BY count DESC";
            $stats['by_category'] = $this->db->fetchAll($sql);
            
            // Assets by status
            $sql = "SELECT status, COUNT(*) as count 
                    FROM {$this->table} 
                    GROUP BY status";
            $stats['by_status'] = $this->db->fetchAll($sql);
            
            // Recent assets (last 30 days)
            $sql = "SELECT COUNT(*) as recent 
                    FROM {$this->table} 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $result = $this->db->fetch($sql);
            $stats['recent_count'] = $result['recent'] ?? 0;
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Asset getStatistics Error: " . $e->getMessage());
            return [
                'total' => 0,
                'available' => 0,
                'in_use' => 0,
                'total_value' => 0,
                'by_category' => [],
                'by_status' => [],
                'recent_count' => 0
            ];
        }
    }
    
    /**
     * Find asset by ID (REQ-ASSET-009)
     * @param int $asset_id
     * @return array|false
     */
    public function findById($asset_id) {
        try {
            $sql = "SELECT a.*, 
                           u.name as assigned_user_name,
                           u.email as assigned_user_email,
                           u.role as assigned_user_role
                    FROM {$this->table} a
                    LEFT JOIN users u ON a.assigned_to = u.user_id
                    WHERE a.asset_id = :asset_id 
                    LIMIT 1";
            
            return $this->db->fetch($sql, [':asset_id' => $asset_id]);
        } catch (PDOException $e) {
            error_log("Asset findById Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find asset by code
     * @param string $asset_code
     * @return array|false
     */
    public function findByCode($asset_code) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE asset_code = :asset_code LIMIT 1";
            return $this->db->fetch($sql, [':asset_code' => $asset_code]);
        } catch (PDOException $e) {
            error_log("Asset findByCode Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create new asset (REQ-ASSET-001)
     * @param array $data
     * @return int|false Returns asset_id if successful
     */
    public function create($data) {
        try {
            // Generate asset code if not provided
            $asset_code = $data['asset_code'] ?? $this->generateAssetCode();
            
            $sql = "INSERT INTO {$this->table} 
                    (asset_code, asset_name, category, serial_number, brand, model, 
                     purchase_date, purchase_price, status, photo_url) 
                    VALUES (:asset_code, :asset_name, :category, :serial_number, :brand, :model,
                            :purchase_date, :purchase_price, :status, :photo_url)";
            
            $params = [
                ':asset_code' => $asset_code,
                ':asset_name' => $data['asset_name'],
                ':category' => $data['category'],
                ':serial_number' => $data['serial_number'] ?? null,
                ':brand' => $data['brand'] ?? null,
                ':model' => $data['model'] ?? null,
                ':purchase_date' => $data['purchase_date'] ?? null,
                ':purchase_price' => $data['purchase_price'] ?? null,
                ':status' => $data['status'] ?? 'Available',
                ':photo_url' => $data['photo_url'] ?? null
            ];
            
            $this->db->query($sql, $params);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Asset create Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update asset (REQ-ASSET-003)
     * @param int $asset_id
     * @param array $data
     * @return bool
     */
    public function update($asset_id, $data) {
        try {
            $fields = [];
            $params = [':asset_id' => $asset_id];
            
            // Fields that can be updated
            $allowed_fields = [
                'asset_name', 'category', 'serial_number', 'brand', 'model',
                'purchase_date', 'purchase_price', 'status', 'photo_url'
            ];
            
            foreach ($allowed_fields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "{$field} = :{$field}";
                    $params[":{$field}"] = $data[$field];
                }
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $sql = "UPDATE {$this->table} SET " . implode(", ", $fields) . 
                   " WHERE asset_id = :asset_id";
            
            $stmt = $this->db->query($sql, $params);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Asset update Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete asset (REQ-ASSET-004, REQ-ASSET-005)
     * Note: Check logs will be cascade deleted by DB foreign key constraint
     * @param int $asset_id
     * @return bool
     */
    public function delete($asset_id) {
        try {
            // The database CASCADE DELETE constraint will automatically delete related check_logs
            // See: ALTER TABLE check_logs ADD CONSTRAINT fk_logs_asset_id 
            //      FOREIGN KEY (asset_id) REFERENCES assets(asset_id) ON DELETE CASCADE
            
            $sql = "DELETE FROM {$this->table} WHERE asset_id = :asset_id";
            $stmt = $this->db->query($sql, [':asset_id' => $asset_id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Asset delete Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Assign asset to user (REQ-ASSET-006, REQ-CHECK-003)
     * @param int $asset_id
     * @param int $user_id
     * @param string $assigned_date
     * @return bool
     */
    public function assignToUser($asset_id, $user_id, $assigned_date = null) {
        try {
            if ($assigned_date === null) {
                $assigned_date = date('Y-m-d');
            }
            
            $sql = "UPDATE {$this->table} 
                    SET status = 'In Use', 
                        assigned_to = :user_id, 
                        assigned_date = :assigned_date 
                    WHERE asset_id = :asset_id AND status = 'Available'";
            
            $params = [
                ':asset_id' => $asset_id,
                ':user_id' => $user_id,
                ':assigned_date' => $assigned_date
            ];
            
            $stmt = $this->db->query($sql, $params);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Asset assignToUser Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Unassign asset from user (REQ-CHECK-006, REQ-CHECK-007)
     * @param int $asset_id
     * @return bool
     */
    public function unassignFromUser($asset_id) {
        try {
            $sql = "UPDATE {$this->table} 
                    SET status = 'Available', 
                        assigned_to = NULL, 
                        assigned_date = NULL 
                    WHERE asset_id = :asset_id";
            
            $stmt = $this->db->query($sql, [':asset_id' => $asset_id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Asset unassignFromUser Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get assets by user (assigned to specific user)
     * @param int $user_id
     * @return array
     */
    public function getByUser($user_id) {
        try {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE assigned_to = :user_id 
                    ORDER BY assigned_date DESC";
            
            return $this->db->fetchAll($sql, [':user_id' => $user_id]);
        } catch (PDOException $e) {
            error_log("Asset getByUser Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get available assets (status = Available)
     * @return array
     */
    public function getAvailable() {
        try {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE status = 'Available' 
                    ORDER BY asset_name ASC";
            
            return $this->db->fetchAll($sql);
        } catch (PDOException $e) {
            error_log("Asset getAvailable Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get assets in use (status = In Use)
     * @return array
     */
    public function getInUse() {
        try {
            $sql = "SELECT a.*, u.name as assigned_user_name 
                    FROM {$this->table} a
                    LEFT JOIN users u ON a.assigned_to = u.user_id
                    WHERE a.status = 'In Use' 
                    ORDER BY a.assigned_date DESC";
            
            return $this->db->fetchAll($sql);
        } catch (PDOException $e) {
            error_log("Asset getInUse Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all unique categories
     * @return array
     */
    public function getCategories() {
        try {
            $sql = "SELECT DISTINCT category FROM {$this->table} ORDER BY category ASC";
            return $this->db->fetchAll($sql);
        } catch (PDOException $e) {
            error_log("Asset getCategories Error: " . $e->getMessage());
            return [];
        }
    }
}