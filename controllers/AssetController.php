<?php
/**
 * Asset Controller
 * ITAM System - P-line Company
 * 
 * Handles all asset-related HTTP requests and responses
 */

class AssetController {
    private $assetModel;
    
    public function __construct() {
        // Check if user is logged in
        if (!is_logged_in()) {
            redirect('/index.php');
            exit();
        }
        
        $this->assetModel = new Asset();
    }
    
    /**
     * Display assets list with search and filters (REQ-ASSET-002)
     */
    public function index() {
        // Only admins can access asset management
        if (!is_admin()) {
            redirect('/views/errors/403.php');
            exit();
        }
        
        // Get filters from GET parameters
        $filters = [
            'search' => $_GET['search'] ?? '',
            'category' => $_GET['category'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];
        
        // Get all assets with filters
        $assets = $this->assetModel->getAll($filters);
        
        // Get categories for filter dropdown
        $categories = $this->assetModel->getCategories();
        
        // Load view
        require_once __DIR__ . '/../views/admin/assets/index.php';
    }
    
    /**
     * Show create asset form
     */
    public function create() {
        // Only admins can create assets
        if (!is_admin()) {
            redirect('/views/errors/403.php');
            exit();
        }
        
        // Get categories for dropdown
        $categories = $this->assetModel->getCategories();
        
        // Generate CSRF token
        $csrf_token = generate_csrf_token();
        
        // Load view
        require_once __DIR__ . '/../views/admin/assets/create.php';
    }
    
    /**
     * Store new asset (REQ-ASSET-001)
     */
    public function store() {
        // Only admins can create assets
        if (!is_admin()) {
            redirect('/views/errors/403.php');
            exit();
        }
        
        // Verify CSRF token (REQ-SEC-007)
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash_message('error', 'Invalid CSRF token');
            redirect('/views/admin/assets/create.php');
            exit();
        }
        
        // Sanitize input data (REQ-SEC-004, REQ-SEC-006)
        $data = [
            'asset_name' => sanitize_input($_POST['asset_name'] ?? ''),
            'category' => sanitize_input($_POST['category'] ?? ''),
            'serial_number' => sanitize_input($_POST['serial_number'] ?? ''),
            'brand' => sanitize_input($_POST['brand'] ?? ''),
            'model' => sanitize_input($_POST['model'] ?? ''),
            'purchase_date' => sanitize_input($_POST['purchase_date'] ?? ''),
            'purchase_price' => sanitize_input($_POST['purchase_price'] ?? ''),
            'status' => 'Available' // New assets are always available
        ];
        
        // Validate data
        $validation = validate_asset_data($data, false);
        
        if (!$validation['valid']) {
            $_SESSION['form_errors'] = $validation['errors'];
            $_SESSION['form_data'] = $data;
            set_flash_message('error', 'Please correct the errors below');
            redirect('/views/admin/assets/create.php');
            exit();
        }
        
        // Check if serial number already exists (REQ-VAL-003)
        if (!empty($data['serial_number']) && $this->assetModel->serialNumberExists($data['serial_number'])) {
            $_SESSION['form_errors'] = ['serial_number' => 'Serial number already exists'];
            $_SESSION['form_data'] = $data;
            set_flash_message('error', 'Serial number already exists');
            redirect('/views/admin/assets/create.php');
            exit();
        }
        
        // Handle file upload (REQ-SEC-010)
        $photo_url = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_file($_FILES['photo']);
            
            if ($upload_result['success']) {
                $photo_url = $upload_result['filename'];
            } else {
                $_SESSION['form_errors'] = ['photo' => $upload_result['error']];
                $_SESSION['form_data'] = $data;
                set_flash_message('error', 'File upload error: ' . $upload_result['error']);
                redirect('/views/admin/assets/create.php');
                exit();
            }
        }
        
        $data['photo_url'] = $photo_url;
        
        // Create asset
        $asset_id = $this->assetModel->create($data);
        
        if ($asset_id) {
            set_flash_message('success', 'Asset created successfully');
            redirect('/views/admin/assets/index.php');
        } else {
            // Delete uploaded photo if asset creation failed
            if ($photo_url) {
                delete_file($photo_url);
            }
            
            $_SESSION['form_data'] = $data;
            set_flash_message('error', 'Failed to create asset');
            redirect('/views/admin/assets/create.php');
        }
    }
    
    /**
     * Show edit asset form (REQ-ASSET-003)
     */
    public function edit($id) {
        // Only admins can edit assets
        if (!is_admin()) {
            redirect('/views/errors/403.php');
            exit();
        }
        
        // Get asset data
        $asset = $this->assetModel->getById($id);
        
        if (!$asset) {
            set_flash_message('error', 'Asset not found');
            redirect('/views/admin/assets/index.php');
            exit();
        }
        
        // Get categories for dropdown
        $categories = $this->assetModel->getCategories();
        
        // Generate CSRF token
        $csrf_token = generate_csrf_token();
        
        // Load view
        require_once __DIR__ . '/../views/admin/assets/edit.php';
    }
    
    /**
     * Update asset (REQ-ASSET-003)
     */
    public function update($id) {
        // Only admins can update assets
        if (!is_admin()) {
            redirect('/views/errors/403.php');
            exit();
        }
        
        // Verify CSRF token (REQ-SEC-007)
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash_message('error', 'Invalid CSRF token');
            redirect('/views/admin/assets/edit.php?id=' . $id);
            exit();
        }
        
        // Get existing asset
        $existing_asset = $this->assetModel->getById($id);
        
        if (!$existing_asset) {
            set_flash_message('error', 'Asset not found');
            redirect('/views/admin/assets/index.php');
            exit();
        }
        
        // Sanitize input data
        $data = [
            'asset_name' => sanitize_input($_POST['asset_name'] ?? ''),
            'category' => sanitize_input($_POST['category'] ?? ''),
            'serial_number' => sanitize_input($_POST['serial_number'] ?? ''),
            'brand' => sanitize_input($_POST['brand'] ?? ''),
            'model' => sanitize_input($_POST['model'] ?? ''),
            'purchase_date' => sanitize_input($_POST['purchase_date'] ?? ''),
            'purchase_price' => sanitize_input($_POST['purchase_price'] ?? ''),
            'status' => sanitize_input($_POST['status'] ?? '')
        ];
        
        // Validate data
        $validation = validate_asset_data($data, true);
        
        if (!$validation['valid']) {
            $_SESSION['form_errors'] = $validation['errors'];
            set_flash_message('error', 'Please correct the errors below');
            redirect('/views/admin/assets/edit.php?id=' . $id);
            exit();
        }
        
        // Check if serial number already exists (exclude current asset)
        if (!empty($data['serial_number']) && $this->assetModel->serialNumberExists($data['serial_number'], $id)) {
            $_SESSION['form_errors'] = ['serial_number' => 'Serial number already exists'];
            set_flash_message('error', 'Serial number already exists');
            redirect('/views/admin/assets/edit.php?id=' . $id);
            exit();
        }
        
        // Handle file upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_file($_FILES['photo']);
            
            if ($upload_result['success']) {
                // Delete old photo if exists
                if (!empty($existing_asset['photo_url'])) {
                    delete_file($existing_asset['photo_url']);
                }
                
                $data['photo_url'] = $upload_result['filename'];
            } else {
                $_SESSION['form_errors'] = ['photo' => $upload_result['error']];
                set_flash_message('error', 'File upload error: ' . $upload_result['error']);
                redirect('/views/admin/assets/edit.php?id=' . $id);
                exit();
            }
        }
        
        // Update asset
        $success = $this->assetModel->update($id, $data);
        
        if ($success) {
            set_flash_message('success', 'Asset updated successfully');
            redirect('/views/admin/assets/index.php');
        } else {
            set_flash_message('error', 'Failed to update asset');
            redirect('/views/admin/assets/edit.php?id=' . $id);
        }
    }
    
    /**
     * Show asset details (REQ-ASSET-009)
     */
    public function view($id) {
        // Both admins and users can view asset details
        if (!is_logged_in()) {
            redirect('/index.php');
            exit();
        }
        
        // Get asset data
        $asset = $this->assetModel->getById($id);
        
        if (!$asset) {
            set_flash_message('error', 'Asset not found');
            redirect('/views/admin/assets/index.php');
            exit();
        }
        
        // Load view
        require_once __DIR__ . '/../views/admin/assets/view.php';
    }
    
    /**
     * Delete asset (REQ-ASSET-004)
     */
    public function delete($id) {
        // Only admins can delete assets
        if (!is_admin()) {
            redirect('/views/errors/403.php');
            exit();
        }
        
        // Verify CSRF token
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash_message('error', 'Invalid CSRF token');
            redirect('/views/admin/assets/index.php');
            exit();
        }
        
        // Get asset data to delete photo
        $asset = $this->assetModel->getById($id);
        
        if (!$asset) {
            set_flash_message('error', 'Asset not found');
            redirect('/views/admin/assets/index.php');
            exit();
        }
        
        // Delete asset (REQ-ASSET-005: Check logs will be cascade deleted)
        $success = $this->assetModel->delete($id);
        
        if ($success) {
            // Delete photo file if exists
            if (!empty($asset['photo_url'])) {
                delete_file($asset['photo_url']);
            }
            
            set_flash_message('success', 'Asset deleted successfully');
        } else {
            set_flash_message('error', 'Failed to delete asset');
        }
        
        redirect('/views/admin/assets/index.php');
    }
}