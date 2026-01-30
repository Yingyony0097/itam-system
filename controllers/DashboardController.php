<?php
/**
 * Dashboard Controller
 * ITAM System - P-line Company
 * 
 * Handles dashboard logic for Admin and User roles
 * REQ-AUTH-002: Role-based access control
 * REQ-DASH-001: Admin dashboard statistics
 * REQ-DASH-002: User dashboard with assigned assets
 */

class DashboardController {
    private $db;
    private $user_id;
    private $user_role;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->user_id = $_SESSION['user_id'] ?? null;
        $this->user_role = $_SESSION['user_role'] ?? null;
    }
    
    /**
     * Show appropriate dashboard based on user role
     * REQ-AUTH-002: Role-based access control
     */
    public function index() {
        // Ensure user is logged in
        require_login();
        
        // Route to appropriate dashboard
        if ($this->user_role === 'Admin') {
            $this->adminDashboard();
        } else {
            $this->userDashboard();
        }
    }
    
    /**
     * Admin Dashboard
     * REQ-DASH-001: Display statistics and recent activities
     * Performance: Uses SQL COUNT() for efficiency
     */
    private function adminDashboard() {
        try {
            // 1. Get Total Assets Count
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM assets");
            $stmt->execute();
            $total_assets = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // 2. Get Available Assets Count
            $stmt = $this->db->prepare("SELECT COUNT(*) as available FROM assets WHERE status = 'Available'");
            $stmt->execute();
            $available_assets = $stmt->fetch(PDO::FETCH_ASSOC)['available'];
            
            // 3. Get In Use Assets Count
            $stmt = $this->db->prepare("SELECT COUNT(*) as in_use FROM assets WHERE status = 'In Use'");
            $stmt->execute();
            $in_use_assets = $stmt->fetch(PDO::FETCH_ASSOC)['in_use'];
            
            // 4. Get Total Asset Value
            $stmt = $this->db->prepare("SELECT SUM(purchase_price) as total_value FROM assets");
            $stmt->execute();
            $total_value = $stmt->fetch(PDO::FETCH_ASSOC)['total_value'] ?? 0;
            
            // 5. Get Total Users Count
            $stmt = $this->db->prepare("SELECT COUNT(*) as total_users FROM users WHERE is_active = 1");
            $stmt->execute();
            $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];
            
            // 6. Get Recent Activities (Last 5 check logs)
            $stmt = $this->db->prepare("
                SELECT 
                    cl.log_id,
                    cl.action_type,
                    cl.action_date,
                    a.asset_name,
                    a.asset_code,
                    u.name as user_name,
                    p.name as performed_by_name
                FROM check_logs cl
                INNER JOIN assets a ON cl.asset_id = a.asset_id
                INNER JOIN users u ON cl.user_id = u.user_id
                INNER JOIN users p ON cl.performed_by = p.user_id
                ORDER BY cl.action_date DESC
                LIMIT 5
            ");
            $stmt->execute();
            $recent_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // 7. Get Assets by Category (for chart)
            $stmt = $this->db->prepare("
                SELECT 
                    category, 
                    COUNT(*) as count 
                FROM assets 
                GROUP BY category 
                ORDER BY count DESC
            ");
            $stmt->execute();
            $assets_by_category = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Pass data to view
            $data = [
                'total_assets' => $total_assets,
                'available_assets' => $available_assets,
                'in_use_assets' => $in_use_assets,
                'total_value' => $total_value,
                'total_users' => $total_users,
                'recent_activities' => $recent_activities,
                'assets_by_category' => $assets_by_category
            ];
            
            // Load Admin Dashboard View
            $this->loadView('admin/dashboard', $data);
            
        } catch (PDOException $e) {
            error_log("Dashboard Error: " . $e->getMessage());
            show_error("Error loading dashboard data");
            $this->loadView('admin/dashboard', []);
        }
    }
    
    /**
     * User Dashboard
     * REQ-DASH-002: Display assigned assets and activity history
     */
    private function userDashboard() {
        try {
            // 1. Get My Assigned Assets
            $stmt = $this->db->prepare("
                SELECT 
                    a.asset_id,
                    a.asset_code,
                    a.asset_name,
                    a.category,
                    a.serial_number,
                    a.brand,
                    a.model,
                    a.purchase_price,
                    a.assigned_date,
                    a.photo_url
                FROM assets a
                WHERE a.assigned_to = :user_id
                AND a.status = 'In Use'
                ORDER BY a.assigned_date DESC
            ");
            $stmt->execute(['user_id' => $this->user_id]);
            $my_assets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // 2. Count My Assets
            $assets_count = count($my_assets);
            
            // 3. Calculate Total Value of My Assets
            $total_value = array_sum(array_column($my_assets, 'purchase_price'));
            
            // 4. Get My Recent Activity (Check-out/Check-in history)
            $stmt = $this->db->prepare("
                SELECT 
                    cl.log_id,
                    cl.action_type,
                    cl.action_date,
                    cl.notes,
                    a.asset_name,
                    a.asset_code,
                    p.name as performed_by_name
                FROM check_logs cl
                INNER JOIN assets a ON cl.asset_id = a.asset_id
                INNER JOIN users p ON cl.performed_by = p.user_id
                WHERE cl.user_id = :user_id
                ORDER BY cl.action_date DESC
                LIMIT 10
            ");
            $stmt->execute(['user_id' => $this->user_id]);
            $my_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // 5. Get Last Check Out Date
            $last_checkout = null;
            foreach ($my_activities as $activity) {
                if ($activity['action_type'] === 'Check Out') {
                    $last_checkout = $activity['action_date'];
                    break;
                }
            }
            
            // Pass data to view
            $data = [
                'my_assets' => $my_assets,
                'assets_count' => $assets_count,
                'total_value' => $total_value,
                'my_activities' => $my_activities,
                'last_checkout' => $last_checkout
            ];
            
            // Load User Dashboard View
            $this->loadView('user/dashboard', $data);
            
        } catch (PDOException $e) {
            error_log("User Dashboard Error: " . $e->getMessage());
            show_error("Error loading dashboard data");
            $this->loadView('user/dashboard', []);
        }
    }
    
    /**
     * Helper: Load view file with data
     */
    private function loadView($view, $data = []) {
        // Extract data array to variables
        extract($data);
        
        // Include view file
        require_once __DIR__ . '/../views/' . $view . '.php';
    }
}