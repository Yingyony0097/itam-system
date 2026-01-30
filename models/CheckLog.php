<?php
/**
 * CheckLog Model
 * ITAM System - P-line Company
 * Security: REQ-SEC-003 (PDO Prepared Statements)
 */

class CheckLog {
    private $db;
    private $table = 'check_logs';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create check log entry (REQ-CHECK-004, REQ-CHECK-008)
     * @param array $data
     * @return int|false Returns log_id if successful
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (asset_id, user_id, action_type, action_date, notes, performed_by) 
                    VALUES (:asset_id, :user_id, :action_type, :action_date, :notes, :performed_by)";
            
            $params = [
                ':asset_id' => $data['asset_id'],
                ':user_id' => $data['user_id'],
                ':action_type' => $data['action_type'], // 'Check Out' or 'Check In'
                ':action_date' => $data['action_date'] ?? date('Y-m-d H:i:s'),
                ':notes' => $data['notes'] ?? null,
                ':performed_by' => $data['performed_by'] // Admin who performed the action
            ];
            
            $this->db->query($sql, $params);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("CheckLog create Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get recent check activities for dashboard (REQ-DASH-001)
     * @param int $limit
     * @return array
     */
    public function getRecent($limit = 10) {
        try {
            $sql = "SELECT cl.*, 
                           a.asset_name, a.asset_code, a.category,
                           u.name as user_name, u.email as user_email,
                           admin.name as performed_by_name
                    FROM {$this->table} cl
                    INNER JOIN assets a ON cl.asset_id = a.asset_id
                    INNER JOIN users u ON cl.user_id = u.user_id
                    INNER JOIN users admin ON cl.performed_by = admin.user_id
                    ORDER BY cl.action_date DESC, cl.created_at DESC
                    LIMIT :limit";
            
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("CheckLog getRecent Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get check log history by asset (REQ-CHECK-011, REQ-CHECK-012)
     * @param int $asset_id
     * @param array $filters
     * @return array
     */
    public function getByAsset($asset_id, $filters = []) {
        try {
            $sql = "SELECT cl.*, 
                           u.name as user_name, u.email as user_email,
                           admin.name as performed_by_name
                    FROM {$this->table} cl
                    INNER JOIN users u ON cl.user_id = u.user_id
                    INNER JOIN users admin ON cl.performed_by = admin.user_id
                    WHERE cl.asset_id = :asset_id";
            
            $params = [':asset_id' => $asset_id];
            
            // Filter by action type
            if (!empty($filters['action_type'])) {
                $sql .= " AND cl.action_type = :action_type";
                $params[':action_type'] = $filters['action_type'];
            }
            
            // Filter by date range
            if (!empty($filters['date_from'])) {
                $sql .= " AND cl.action_date >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $sql .= " AND cl.action_date <= :date_to";
                $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
            }
            
            $sql .= " ORDER BY cl.action_date DESC, cl.created_at DESC";
            
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
            error_log("CheckLog getByAsset Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get check log history by user (REQ-DASH-002)
     * @param int $user_id
     * @param array $filters
     * @return array
     */
    public function getByUser($user_id, $filters = []) {
        try {
            $sql = "SELECT cl.*, 
                           a.asset_name, a.asset_code, a.category, a.serial_number,
                           admin.name as performed_by_name
                    FROM {$this->table} cl
                    INNER JOIN assets a ON cl.asset_id = a.asset_id
                    INNER JOIN users admin ON cl.performed_by = admin.user_id
                    WHERE cl.user_id = :user_id";
            
            $params = [':user_id' => $user_id];
            
            // Filter by action type
            if (!empty($filters['action_type'])) {
                $sql .= " AND cl.action_type = :action_type";
                $params[':action_type'] = $filters['action_type'];
            }
            
            // Filter by date range
            if (!empty($filters['date_from'])) {
                $sql .= " AND cl.action_date >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $sql .= " AND cl.action_date <= :date_to";
                $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
            }
            
            $sql .= " ORDER BY cl.action_date DESC, cl.created_at DESC";
            
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
            error_log("CheckLog getByUser Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all check logs with filters (REQ-CHECK-011, REQ-CHECK-012)
     * @param array $filters
     * @return array
     */
    public function getAll($filters = []) {
        try {
            $sql = "SELECT cl.*, 
                           a.asset_name, a.asset_code, a.category,
                           u.name as user_name, u.email as user_email,
                           admin.name as performed_by_name
                    FROM {$this->table} cl
                    INNER JOIN assets a ON cl.asset_id = a.asset_id
                    INNER JOIN users u ON cl.user_id = u.user_id
                    INNER JOIN users admin ON cl.performed_by = admin.user_id";
            
            $where = [];
            $params = [];
            
            // Filter by asset
            if (!empty($filters['asset_id'])) {
                $where[] = "cl.asset_id = :asset_id";
                $params[':asset_id'] = $filters['asset_id'];
            }
            
            // Filter by user
            if (!empty($filters['user_id'])) {
                $where[] = "cl.user_id = :user_id";
                $params[':user_id'] = $filters['user_id'];
            }
            
            // Filter by action type
            if (!empty($filters['action_type'])) {
                $where[] = "cl.action_type = :action_type";
                $params[':action_type'] = $filters['action_type'];
            }
            
            // Filter by date range
            if (!empty($filters['date_from'])) {
                $where[] = "cl.action_date >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $where[] = "cl.action_date <= :date_to";
                $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
            }
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            
            $sql .= " ORDER BY cl.action_date DESC, cl.created_at DESC";
            
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
            error_log("CheckLog getAll Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total check logs
     * @param array $filters
     * @return int
     */
    public function count($filters = []) {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} cl";
            
            $where = [];
            $params = [];
            
            if (!empty($filters['asset_id'])) {
                $where[] = "cl.asset_id = :asset_id";
                $params[':asset_id'] = $filters['asset_id'];
            }
            
            if (!empty($filters['user_id'])) {
                $where[] = "cl.user_id = :user_id";
                $params[':user_id'] = $filters['user_id'];
            }
            
            if (!empty($filters['action_type'])) {
                $where[] = "cl.action_type = :action_type";
                $params[':action_type'] = $filters['action_type'];
            }
            
            if (!empty($filters['date_from'])) {
                $where[] = "cl.action_date >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $where[] = "cl.action_date <= :date_to";
                $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
            }
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            
            return (int)$this->db->fetchColumn($sql, $params);
        } catch (PDOException $e) {
            error_log("CheckLog count Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get last check log for an asset
     * @param int $asset_id
     * @return array|false
     */
    public function getLastByAsset($asset_id) {
        try {
            $sql = "SELECT cl.*, 
                           u.name as user_name,
                           admin.name as performed_by_name
                    FROM {$this->table} cl
                    INNER JOIN users u ON cl.user_id = u.user_id
                    INNER JOIN users admin ON cl.performed_by = admin.user_id
                    WHERE cl.asset_id = :asset_id
                    ORDER BY cl.action_date DESC, cl.created_at DESC
                    LIMIT 1";
            
            return $this->db->fetch($sql, [':asset_id' => $asset_id]);
        } catch (PDOException $e) {
            error_log("CheckLog getLastByAsset Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get check statistics for reports
     * @param array $filters
     * @return array
     */
    public function getStatistics($filters = []) {
        try {
            $stats = [];
            
            $where = [];
            $params = [];
            
            // Build WHERE clause for filters
            if (!empty($filters['date_from'])) {
                $where[] = "action_date >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $where[] = "action_date <= :date_to";
                $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
            }
            
            $where_clause = !empty($where) ? " WHERE " . implode(" AND ", $where) : "";
            
            // Total check-outs
            $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                    WHERE action_type = 'Check Out'" . $where_clause;
            $result = $this->db->fetch($sql, $params);
            $stats['total_checkouts'] = $result['total'] ?? 0;
            
            // Total check-ins
            $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                    WHERE action_type = 'Check In'" . $where_clause;
            $result = $this->db->fetch($sql, $params);
            $stats['total_checkins'] = $result['total'] ?? 0;
            
            // Most active users
            $sql = "SELECT user_id, COUNT(*) as activity_count 
                    FROM {$this->table}" . $where_clause . "
                    GROUP BY user_id 
                    ORDER BY activity_count DESC 
                    LIMIT 5";
            $stats['most_active_users'] = $this->db->fetchAll($sql, $params);
            
            // Most used assets
            $sql = "SELECT asset_id, COUNT(*) as usage_count 
                    FROM {$this->table}" . $where_clause . "
                    GROUP BY asset_id 
                    ORDER BY usage_count DESC 
                    LIMIT 5";
            $stats['most_used_assets'] = $this->db->fetchAll($sql, $params);
            
            return $stats;
        } catch (PDOException $e) {
            error_log("CheckLog getStatistics Error: " . $e->getMessage());
            return [];
        }
    }
}