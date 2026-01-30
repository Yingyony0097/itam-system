<?php
/**
 * Check Controller
 * ITAM System - P-line Company
 *
 * Handles check-in/check-out operations with database transactions
 * Security: REQ-SEC-003 (PDO Prepared Statements), REQ-SEC-007 (CSRF Protection)
 */

class CheckController {
    private $assetModel;
    private $checkLogModel;
    private $userModel;
    private $db;

    public function __construct() {
        // Check if user is logged in
        if (!is_logged_in()) {
            redirect('/index.php');
            exit();
        }

        // Only admins can access check-in/check-out
        if (!is_admin()) {
            redirect('/views/errors/403.php');
            exit();
        }

        $this->assetModel = new Asset();
        $this->checkLogModel = new CheckLog();
        $this->userModel = new User();
        $this->db = Database::getInstance();
    }

    /**
     * Show checkout form (GET)
     * REQ-CHECK-001, REQ-CHECK-002
     */
    public function checkout() {
        $asset_id = $_GET['id'] ?? null;

        if (!$asset_id) {
            set_flash_message('error', 'Asset ID is required');
            redirect('/views/admin/assets/index.php');
            exit();
        }

        // Get asset details
        $asset = $this->assetModel->getById($asset_id);

        if (!$asset) {
            set_flash_message('error', 'Asset not found');
            redirect('/views/admin/assets/index.php');
            exit();
        }

        // Check if asset is available
        if ($asset['status'] !== 'Available') {
            set_flash_message('error', 'Asset is not available for checkout. Current status: ' . htmlspecialchars($asset['status']));
            redirect('/views/admin/assets/view.php?id=' . $asset_id);
            exit();
        }

        // Get all active users for dropdown
        $users = $this->userModel->getAll(['is_active' => 1]);

        // Generate CSRF token
        $csrf_token = generate_csrf_token();

        // Load checkout form view
        require_once __DIR__ . '/../views/admin/checkin-checkout/checkout.php';
    }

    /**
     * Process checkout (POST)
     * REQ-CHECK-003, REQ-CHECK-004
     * Uses Database Transaction for data integrity
     */
    public function processCheckout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/views/admin/assets/index.php');
            exit();
        }

        // Verify CSRF token (REQ-SEC-007)
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash_message('error', 'Invalid CSRF token');
            redirect('/views/admin/assets/index.php');
            exit();
        }

        // Sanitize input data (REQ-SEC-004, REQ-SEC-006)
        $asset_id = intval($_POST['asset_id'] ?? 0);
        $user_id = intval($_POST['user_id'] ?? 0);
        $notes = sanitize_input($_POST['notes'] ?? '');

        // Validate inputs
        if (!$asset_id || !$user_id) {
            set_flash_message('error', 'Asset ID and User ID are required');
            redirect('/views/admin/assets/index.php');
            exit();
        }

        // Get asset details
        $asset = $this->assetModel->getById($asset_id);

        if (!$asset) {
            set_flash_message('error', 'Asset not found');
            redirect('/views/admin/assets/index.php');
            exit();
        }

        // Verify asset is available
        if ($asset['status'] !== 'Available') {
            set_flash_message('error', 'Asset is not available for checkout. Current status: ' . htmlspecialchars($asset['status']));
            redirect('/views/admin/assets/view.php?id=' . $asset_id);
            exit();
        }

        // Get user details
        $user = $this->userModel->findById($user_id);

        if (!$user) {
            set_flash_message('error', 'User not found');
            redirect('/views/admin/checkin-checkout/checkout.php?id=' . $asset_id);
            exit();
        }

        // *** BEGIN DATABASE TRANSACTION ***
        // This ensures both asset status update and check log creation happen together
        // or both fail (data integrity)
        try {
            $this->db->beginTransaction();

            // 1. Update asset status to 'In Use' and assign to user
            $updateResult = $this->assetModel->assignToUser($asset_id, $user_id);

            if (!$updateResult) {
                throw new Exception('Failed to update asset status');
            }

            // 2. Create check log entry
            $logData = [
                'asset_id' => $asset_id,
                'user_id' => $user_id,
                'action_type' => 'Check Out',
                'action_date' => date('Y-m-d H:i:s'),
                'notes' => $notes,
                'performed_by' => $_SESSION['user_id'] // Admin who performed the action
            ];

            $logResult = $this->checkLogModel->create($logData);

            if (!$logResult) {
                throw new Exception('Failed to create check log entry');
            }

            // *** COMMIT TRANSACTION ***
            // Both operations succeeded, make changes permanent
            $this->db->commit();

            // Success message
            set_flash_message('success', 'Asset "' . htmlspecialchars($asset['asset_name']) . '" (' . htmlspecialchars($asset['asset_code']) . ') successfully checked out to ' . htmlspecialchars($user['name']));
            redirect('/views/admin/assets/view.php?id=' . $asset_id);

        } catch (Exception $e) {
            // *** ROLLBACK TRANSACTION ***
            // Something went wrong, undo all changes
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }

            // Log error
            error_log("Checkout Transaction Error: " . $e->getMessage());

            // Error message
            set_flash_message('error', 'Failed to checkout asset: ' . $e->getMessage());
            redirect('/views/admin/checkin-checkout/checkout.php?id=' . $asset_id);
        }
    }

    /**
     * Show checkin confirmation (GET)
     * REQ-CHECK-005, REQ-CHECK-006
     */
    public function checkin() {
        $asset_id = $_GET['id'] ?? null;

        if (!$asset_id) {
            set_flash_message('error', 'Asset ID is required');
            redirect('/views/admin/assets/index.php');
            exit();
        }

        // Get asset details
        $asset = $this->assetModel->getById($asset_id);

        if (!$asset) {
            set_flash_message('error', 'Asset not found');
            redirect('/views/admin/assets/index.php');
            exit();
        }

        // Check if asset is in use
        if ($asset['status'] !== 'In Use') {
            set_flash_message('error', 'Asset is not checked out. Current status: ' . htmlspecialchars($asset['status']));
            redirect('/views/admin/assets/view.php?id=' . $asset_id);
            exit();
        }

        // Generate CSRF token
        $csrf_token = generate_csrf_token();

        // Load checkin confirmation view
        require_once __DIR__ . '/../views/admin/checkin-checkout/checkin.php';
    }

    /**
     * Process checkin (POST)
     * REQ-CHECK-007, REQ-CHECK-008
     * Uses Database Transaction for data integrity
     */
    public function processCheckin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/views/admin/assets/index.php');
            exit();
        }

        // Verify CSRF token (REQ-SEC-007)
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash_message('error', 'Invalid CSRF token');
            redirect('/views/admin/assets/index.php');
            exit();
        }

        // Sanitize input data (REQ-SEC-004, REQ-SEC-006)
        $asset_id = intval($_POST['asset_id'] ?? 0);
        $notes = sanitize_input($_POST['notes'] ?? '');

        // Validate input
        if (!$asset_id) {
            set_flash_message('error', 'Asset ID is required');
            redirect('/views/admin/assets/index.php');
            exit();
        }

        // Get asset details
        $asset = $this->assetModel->getById($asset_id);

        if (!$asset) {
            set_flash_message('error', 'Asset not found');
            redirect('/views/admin/assets/index.php');
            exit();
        }

        // Verify asset is in use
        if ($asset['status'] !== 'In Use') {
            set_flash_message('error', 'Asset is not checked out. Current status: ' . htmlspecialchars($asset['status']));
            redirect('/views/admin/assets/view.php?id=' . $asset_id);
            exit();
        }

        // Store user_id before unassigning
        $previous_user_id = $asset['assigned_to'];

        // *** BEGIN DATABASE TRANSACTION ***
        // This ensures both asset status update and check log creation happen together
        try {
            $this->db->beginTransaction();

            // 1. Update asset status to 'Available' and clear assignment
            $updateResult = $this->assetModel->unassignFromUser($asset_id);

            if (!$updateResult) {
                throw new Exception('Failed to update asset status');
            }

            // 2. Create check log entry
            $logData = [
                'asset_id' => $asset_id,
                'user_id' => $previous_user_id, // User who returned the asset
                'action_type' => 'Check In',
                'action_date' => date('Y-m-d H:i:s'),
                'notes' => $notes,
                'performed_by' => $_SESSION['user_id'] // Admin who performed the action
            ];

            $logResult = $this->checkLogModel->create($logData);

            if (!$logResult) {
                throw new Exception('Failed to create check log entry');
            }

            // *** COMMIT TRANSACTION ***
            // Both operations succeeded, make changes permanent
            $this->db->commit();

            // Success message
            set_flash_message('success', 'Asset "' . htmlspecialchars($asset['asset_name']) . '" (' . htmlspecialchars($asset['asset_code']) . ') successfully checked in and is now available');
            redirect('/views/admin/assets/view.php?id=' . $asset_id);

        } catch (Exception $e) {
            // *** ROLLBACK TRANSACTION ***
            // Something went wrong, undo all changes
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }

            // Log error
            error_log("Checkin Transaction Error: " . $e->getMessage());

            // Error message
            set_flash_message('error', 'Failed to checkin asset: ' . $e->getMessage());
            redirect('/views/admin/checkin-checkout/checkin.php?id=' . $asset_id);
        }
    }

    /**
     * Show check-in/check-out history
     * REQ-CHECK-011, REQ-CHECK-012
     */
    public function history() {
        // Get filters from GET parameters
        $filters = [
            'asset_id' => $_GET['asset_id'] ?? '',
            'user_id' => $_GET['user_id'] ?? '',
            'action_type' => $_GET['action_type'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        // Clean up empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== '';
        });

        // Get check logs with filters
        $logs = $this->checkLogModel->getAll($filters);

        // Get all assets for filter dropdown
        $assets = $this->assetModel->getAll();

        // Get all users for filter dropdown
        $users = $this->userModel->getAll();

        // Load history view
        require_once __DIR__ . '/../views/admin/checkin-checkout/history.php';
    }

    /**
     * Get asset history (AJAX endpoint for asset detail view)
     * REQ-CHECK-011
     */
    public function getAssetHistory() {
        $asset_id = $_GET['id'] ?? null;

        if (!$asset_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Asset ID is required']);
            exit();
        }

        // Get asset history
        $history = $this->checkLogModel->getByAsset($asset_id);

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $history]);
    }
}

// ====================================
// Routing Logic
// ====================================

// Initialize config and autoload
require_once __DIR__ . '/../config/init.php';

// Get action from URL
$action = $_GET['action'] ?? 'history';

// Create controller instance
$controller = new CheckController();

// Route to appropriate method
switch ($action) {
    case 'checkout':
        $controller->checkout();
        break;

    case 'processCheckout':
        $controller->processCheckout();
        break;

    case 'checkin':
        $controller->checkin();
        break;

    case 'processCheckin':
        $controller->processCheckin();
        break;

    case 'history':
        $controller->history();
        break;

    case 'getAssetHistory':
        $controller->getAssetHistory();
        break;

    default:
        // Default to history page
        $controller->history();
        break;
}
