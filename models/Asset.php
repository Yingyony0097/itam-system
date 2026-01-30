<?php
/**
 * Asset Model
 * ITAM System - P-line Company
 * 
 * Handles all asset-related database operations
 */

class Asset {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all assets with optional filters (REQ-ASSET-007, REQ-ASSET-008)
     * 
     * @param array $filters Optional filters (search, category, status)
     * @return array Array of assets
     */
    public function getAll($filters = []) {
        $query = "SELECT a.*, u.name as assigned_user_name 
                  FROM assets a 
                  LEFT JOIN users u ON a.assigned_to = u.user_id 
                  WHERE 1=1";
        
        $params = [];
        
        // Search filter (REQ-ASSET-007)
        if (!empty($filters['search'])) {
            $query .= " AND (a.asset_name LIKE :search 
                        OR a.asset_code LIKE :search 
                        OR a.serial_number LIKE :search
                        OR a.brand LIKE :search
                        OR a.model LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Category filter (REQ-ASSET-008)
        if (!empty($filters['category'])) {
            $query .= " AND a.category = :category";
            $params[':category'] = $filters['category'];
        }
        
        // Status filter (REQ-ASSET-008)
        if (!empty($filters['status'])) {
            $query .= " AND a.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        // Order by most recent first
        $query .= " ORDER BY a.created_at DESC";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Asset::getAll() Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get asset by ID (REQ-ASSET-009)
     * 
     * @param int $id Asset ID
     * @return array|null Asset data or null if not found
     */
    public function getById($id) {
        $query = "SELECT a.*, u.name as assigned_user_name 
                  FROM assets a 
                  LEFT JOIN users u ON a.assigned_to = u.user_id 
                  WHERE a.asset_id = :id";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Asset::getById() Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get asset by code
     * 
     * @param string $code Asset code
     * @return array|null Asset data or null if not found
     */
    public function getByCode($code) {
        $query = "SELECT * FROM assets WHERE asset_code = :code";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':code' => $code]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Asset::getByCode() Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Generate unique asset code (Format: AST-001, AST-002, etc.)
     * 
     * @return string Generated asset code
     */
    public function generateAssetCode() {
        $query = "SELECT asset_code FROM assets ORDER BY asset_id DESC LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $last_asset = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($last_asset) {
                // Extract number from last code (e.g., "AST-001" -> 001)
                $last_number = intval(substr($last_asset['asset_code'], 4));
                $new_number = $last_number + 1;
            } else {
                // First asset
                $new_number = 1;
            }
            
            // Format: AST-001, AST-002, etc.
            return 'AST-' . str_pad($new_number, 3, '0', STR_PAD_LEFT);
            
        } catch (PDOException $e) {
            error_log("Asset::generateAssetCode() Error: " . $e->getMessage());
            // Fallback to timestamp-based code
            return 'AST-' . time();
        }
    }
    
    /**
     * Create new asset (REQ-ASSET-001)
     * 
     * @param array $data Asset data
     * @return int|bool New asset ID or false on failure
     */
    public function create($data) {
        // Generate asset code
        $asset_code = $this->generateAssetCode();
        
        $query = "INSERT INTO assets (
                    asset_code, asset_name, category, serial_number, 
                    brand, model, purchase_date, purchase_price, 
                    status, photo_url
                  ) VALUES (
                    :asset_code, :asset_name, :category, :serial_number,
                    :brand, :model, :purchase_date, :purchase_price,
                    :status, :photo_url
                  )";
        
        try {
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
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
            ]);
            
            return $result ? $this->db->lastInsertId() : false;
            
        } catch (PDOException $e) {
            error_log("Asset::create() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update asset (REQ-ASSET-003)
     * 
     * @param int $id Asset ID
     * @param array $data Asset data
     * @return bool Success status
     */
    public function update($id, $data) {
        $query = "UPDATE assets SET 
                    asset_name = :asset_name,
                    category = :category,
                    serial_number = :serial_number,
                    brand = :brand,
                    model = :model,
                    purchase_date = :purchase_date,
                    purchase_price = :purchase_price,
                    status = :status";
        
        // Add photo_url to update only if provided
        if (isset($data['photo_url'])) {
            $query .= ", photo_url = :photo_url";
        }
        
        $query .= " WHERE asset_id = :id";
        
        try {
            $stmt = $this->db->prepare($query);
            $params = [
                ':asset_name' => $data['asset_name'],
                ':category' => $data['category'],
                ':serial_number' => $data['serial_number'] ?? null,
                ':brand' => $data['brand'] ?? null,
                ':model' => $data['model'] ?? null,
                ':purchase_date' => $data['purchase_date'] ?? null,
                ':purchase_price' => $data['purchase_price'] ?? null,
                ':status' => $data['status'],
                ':id' => $id
            ];
            
            if (isset($data['photo_url'])) {
                $params[':photo_url'] = $data['photo_url'];
            }
            
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            error_log("Asset::update() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete asset (REQ-ASSET-004, REQ-ASSET-005)
     * Note: Check logs will be cascade deleted due to FK constraint
     * 
     * @param int $id Asset ID
     * @return bool Success status
     */
    public function delete($id) {
        $query = "DELETE FROM assets WHERE asset_id = :id";
        
        try {
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Asset::delete() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Assign asset to user (REQ-ASSET-006)
     * 
     * @param int $asset_id Asset ID
     * @param int $user_id User ID
     * @param string $assigned_date Assignment date
     * @return bool Success status
     */
    public function assignToUser($asset_id, $user_id, $assigned_date = null) {
        if ($assigned_date === null) {
            $assigned_date = date('Y-m-d');
        }
        
        $query = "UPDATE assets SET 
                    status = 'In Use',
                    assigned_to = :user_id,
                    assigned_date = :assigned_date
                  WHERE asset_id = :asset_id";
        
        try {
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ':user_id' => $user_id,
                ':assigned_date' => $assigned_date,
                ':asset_id' => $asset_id
            ]);
        } catch (PDOException $e) {
            error_log("Asset::assignToUser() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Unassign asset from user
     * 
     * @param int $asset_id Asset ID
     * @return bool Success status
     */
    public function unassignFromUser($asset_id) {
        $query = "UPDATE assets SET 
                    status = 'Available',
                    assigned_to = NULL,
                    assigned_date = NULL
                  WHERE asset_id = :asset_id";
        
        try {
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':asset_id' => $asset_id]);
        } catch (PDOException $e) {
            error_log("Asset::unassignFromUser() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get assets assigned to a specific user
     * 
     * @param int $user_id User ID
     * @return array Array of assets
     */
    public function getByUserId($user_id) {
        $query = "SELECT * FROM assets WHERE assigned_to = :user_id ORDER BY assigned_date DESC";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':user_id' => $user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Asset::getByUserId() Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get asset statistics for dashboard (REQ-DASH-001)
     * 
     * @return array Statistics data
     */
    public function getStatistics() {
        try {
            // Total assets
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM assets");
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Available assets
            $stmt = $this->db->query("SELECT COUNT(*) as available FROM assets WHERE status = 'Available'");
            $available = $stmt->fetch(PDO::FETCH_ASSOC)['available'];
            
            // In Use assets
            $stmt = $this->db->query("SELECT COUNT(*) as in_use FROM assets WHERE status = 'In Use'");
            $in_use = $stmt->fetch(PDO::FETCH_ASSOC)['in_use'];
            
            // Total value
            $stmt = $this->db->query("SELECT COALESCE(SUM(purchase_price), 0) as total_value FROM assets");
            $total_value = $stmt->fetch(PDO::FETCH_ASSOC)['total_value'];
            
            // Assets by category
            $stmt = $this->db->query("SELECT category, COUNT(*) as count FROM assets GROUP BY category ORDER BY count DESC");
            $by_category = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'total' => $total,
                'available' => $available,
                'in_use' => $in_use,
                'total_value' => $total_value,
                'by_category' => $by_category,
                'available_percentage' => $total > 0 ? round(($available / $total) * 100, 1) : 0,
                'in_use_percentage' => $total > 0 ? round(($in_use / $total) * 100, 1) : 0
            ];
            
        } catch (PDOException $e) {
            error_log("Asset::getStatistics() Error: " . $e->getMessage());
            return [
                'total' => 0,
                'available' => 0,
                'in_use' => 0,
                'total_value' => 0,
                'by_category' => [],
                'available_percentage' => 0,
                'in_use_percentage' => 0
            ];
        }
    }
    
    /**
     * Get unique categories for filter dropdown
     * 
     * @return array Array of categories
     */
    public function getCategories() {
        $query = "SELECT DISTINCT category FROM assets WHERE category IS NOT NULL ORDER BY category ASC";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Asset::getCategories() Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check if serial number exists (for validation)
     * 
     * @param string $serial_number Serial number
     * @param int $exclude_id Asset ID to exclude (for updates)
     * @return bool True if exists
     */
    public function serialNumberExists($serial_number, $exclude_id = null) {
        $query = "SELECT COUNT(*) as count FROM assets WHERE serial_number = :serial_number";
        
        if ($exclude_id) {
            $query .= " AND asset_id != :exclude_id";
        }
        
        try {
            $stmt = $this->db->prepare($query);
            $params = [':serial_number' => $serial_number];
            
            if ($exclude_id) {
                $params[':exclude_id'] = $exclude_id;
            }
            
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("Asset::serialNumberExists() Error: " . $e->getMessage());
            return false;
        }
    }
}