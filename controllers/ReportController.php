<?php
/**
 * Report Controller
 * ITAM System - P-line Company
 *
 * Handles report generation, filtering, and export (PDF/Excel)
 * Security: REQ-SEC-003 (PDO Prepared Statements), REQ-SEC-007 (CSRF Protection)
 * Phase 3H: REQ-REPORT-001 to REQ-REPORT-010
 */

class ReportController {
    private $assetModel;
    private $checkLogModel;
    private $userModel;

    public function __construct() {
        // Check if user is logged in
        if (!is_logged_in()) {
            redirect('/index.php');
            exit();
        }

        // Only admins can access reports
        if (!is_admin()) {
            redirect('/views/errors/403.php');
            exit();
        }

        $this->assetModel = new Asset();
        $this->checkLogModel = new CheckLog();
        $this->userModel = new User();
    }

    /**
     * Show report dashboard (GET)
     * REQ-REPORT-001
     */
    public function index() {
        // Get all categories for filter dropdown
        $categories = $this->assetModel->getCategories();

        // Get all users for filter dropdown
        $users = $this->userModel->getAll();

        // Load report index view
        require_once __DIR__ . '/../views/admin/reports/index.php';
    }

    /**
     * Generate report based on filters (GET)
     * REQ-REPORT-002, REQ-REPORT-003, REQ-REPORT-004, REQ-REPORT-005, REQ-REPORT-006
     */
    public function generate() {
        // Get report type from GET parameters
        $report_type = $_GET['report_type'] ?? 'asset_inventory';

        // Get filters from GET parameters
        $filters = $this->getFiltersFromRequest();

        // Initialize report data
        $report_data = [];
        $report_title = '';
        $statistics = [];

        // Generate report based on type
        switch ($report_type) {
            case 'asset_inventory':
                $report_title = 'Asset Inventory Report';
                $report_data = $this->generateAssetInventoryReport($filters);
                $statistics = $this->getAssetInventoryStatistics($report_data);
                break;

            case 'checkin_checkout':
                $report_title = 'Check-In/Check-Out History Report';
                $report_data = $this->generateCheckInOutReport($filters);
                $statistics = $this->getCheckInOutStatistics($report_data);
                break;

            case 'user_assignment':
                $report_title = 'User Assignment Report';
                $report_data = $this->generateUserAssignmentReport($filters);
                $statistics = $this->getUserAssignmentStatistics($report_data);
                break;

            case 'asset_by_category':
                $report_title = 'Asset by Category Report';
                $report_data = $this->generateAssetByCategoryReport($filters);
                $statistics = $this->getAssetByCategoryStatistics($report_data);
                break;

            case 'asset_valuation':
                $report_title = 'Asset Valuation Report';
                $report_data = $this->generateAssetValuationReport($filters);
                $statistics = $this->getAssetValuationStatistics($report_data);
                break;

            default:
                $report_title = 'Asset Inventory Report';
                $report_data = $this->generateAssetInventoryReport($filters);
                $statistics = $this->getAssetInventoryStatistics($report_data);
                break;
        }

        // Get categories and users for filter dropdowns
        $categories = $this->assetModel->getCategories();
        $users = $this->userModel->getAll();

        // Load report index view with data
        require_once __DIR__ . '/../views/admin/reports/index.php';
    }

    /**
     * Export report to PDF (GET)
     * REQ-REPORT-007, REQ-REPORT-008
     */
    public function exportPdf() {
        // Check if TCPDF library exists
        if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
            set_flash_message('error', 'PDF export library not installed. Please run: composer install');
            redirect('/controllers/ReportController.php');
            exit();
        }

        require_once __DIR__ . '/../vendor/autoload.php';

        // Get report type and filters
        $report_type = $_GET['report_type'] ?? 'asset_inventory';
        $filters = $this->getFiltersFromRequest();

        // Generate report data
        $report_data = [];
        $report_title = '';

        switch ($report_type) {
            case 'asset_inventory':
                $report_title = 'Asset Inventory Report';
                $report_data = $this->generateAssetInventoryReport($filters);
                break;
            case 'checkin_checkout':
                $report_title = 'Check-In/Check-Out History Report';
                $report_data = $this->generateCheckInOutReport($filters);
                break;
            case 'user_assignment':
                $report_title = 'User Assignment Report';
                $report_data = $this->generateUserAssignmentReport($filters);
                break;
            case 'asset_by_category':
                $report_title = 'Asset by Category Report';
                $report_data = $this->generateAssetByCategoryReport($filters);
                break;
            case 'asset_valuation':
                $report_title = 'Asset Valuation Report';
                $report_data = $this->generateAssetValuationReport($filters);
                break;
        }

        // Create PDF
        $this->generatePDF($report_title, $report_data, $report_type, $filters);
    }

    /**
     * Export report to Excel (GET)
     * REQ-REPORT-009, REQ-REPORT-010
     */
    public function exportExcel() {
        // Check if PhpSpreadsheet library exists
        if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
            set_flash_message('error', 'Excel export library not installed. Please run: composer install');
            redirect('/controllers/ReportController.php');
            exit();
        }

        require_once __DIR__ . '/../vendor/autoload.php';

        // Get report type and filters
        $report_type = $_GET['report_type'] ?? 'asset_inventory';
        $filters = $this->getFiltersFromRequest();

        // Generate report data
        $report_data = [];
        $report_title = '';

        switch ($report_type) {
            case 'asset_inventory':
                $report_title = 'Asset Inventory Report';
                $report_data = $this->generateAssetInventoryReport($filters);
                break;
            case 'checkin_checkout':
                $report_title = 'Check-In/Check-Out History Report';
                $report_data = $this->generateCheckInOutReport($filters);
                break;
            case 'user_assignment':
                $report_title = 'User Assignment Report';
                $report_data = $this->generateUserAssignmentReport($filters);
                break;
            case 'asset_by_category':
                $report_title = 'Asset by Category Report';
                $report_data = $this->generateAssetByCategoryReport($filters);
                break;
            case 'asset_valuation':
                $report_title = 'Asset Valuation Report';
                $report_data = $this->generateAssetValuationReport($filters);
                break;
        }

        // Create Excel
        $this->generateExcel($report_title, $report_data, $report_type, $filters);
    }

    // ====================================
    // Report Generation Methods
    // ====================================

    /**
     * Generate Asset Inventory Report
     * REQ-REPORT-002
     */
    private function generateAssetInventoryReport($filters) {
        return $this->assetModel->getAll($filters);
    }

    /**
     * Generate Check-In/Check-Out History Report
     * REQ-REPORT-003
     */
    private function generateCheckInOutReport($filters) {
        return $this->checkLogModel->getAll($filters);
    }

    /**
     * Generate User Assignment Report
     * REQ-REPORT-004
     */
    private function generateUserAssignmentReport($filters) {
        // Get assets with assigned users
        $assets_filters = $filters;
        $assets_filters['status'] = 'In Use'; // Only show assigned assets

        $assets = $this->assetModel->getAll($assets_filters);

        // Group by user
        $user_assignments = [];
        foreach ($assets as $asset) {
            if (!empty($asset['assigned_to'])) {
                $user_id = $asset['assigned_to'];
                if (!isset($user_assignments[$user_id])) {
                    $user_assignments[$user_id] = [
                        'user_id' => $user_id,
                        'user_name' => $asset['assigned_user_name'],
                        'assets' => [],
                        'total_value' => 0
                    ];
                }
                $user_assignments[$user_id]['assets'][] = $asset;
                $user_assignments[$user_id]['total_value'] += floatval($asset['purchase_price'] ?? 0);
            }
        }

        return array_values($user_assignments);
    }

    /**
     * Generate Asset by Category Report
     * REQ-REPORT-005
     */
    private function generateAssetByCategoryReport($filters) {
        $assets = $this->assetModel->getAll($filters);

        // Group by category
        $category_groups = [];
        foreach ($assets as $asset) {
            $category = $asset['category'] ?? 'Uncategorized';
            if (!isset($category_groups[$category])) {
                $category_groups[$category] = [
                    'category' => $category,
                    'assets' => [],
                    'total_count' => 0,
                    'total_value' => 0,
                    'available_count' => 0,
                    'in_use_count' => 0
                ];
            }
            $category_groups[$category]['assets'][] = $asset;
            $category_groups[$category]['total_count']++;
            $category_groups[$category]['total_value'] += floatval($asset['purchase_price'] ?? 0);

            if ($asset['status'] === 'Available') {
                $category_groups[$category]['available_count']++;
            } elseif ($asset['status'] === 'In Use') {
                $category_groups[$category]['in_use_count']++;
            }
        }

        return array_values($category_groups);
    }

    /**
     * Generate Asset Valuation Report
     * REQ-REPORT-006
     */
    private function generateAssetValuationReport($filters) {
        $assets = $this->assetModel->getAll($filters);

        // Calculate total valuations
        $valuation_data = [
            'assets' => $assets,
            'total_assets' => count($assets),
            'total_value' => 0,
            'by_status' => [
                'Available' => ['count' => 0, 'value' => 0],
                'In Use' => ['count' => 0, 'value' => 0],
                'Maintenance' => ['count' => 0, 'value' => 0],
                'Retired' => ['count' => 0, 'value' => 0]
            ],
            'by_category' => []
        ];

        foreach ($assets as $asset) {
            $price = floatval($asset['purchase_price'] ?? 0);
            $status = $asset['status'] ?? 'Available';
            $category = $asset['category'] ?? 'Uncategorized';

            $valuation_data['total_value'] += $price;

            // By status
            if (!isset($valuation_data['by_status'][$status])) {
                $valuation_data['by_status'][$status] = ['count' => 0, 'value' => 0];
            }
            $valuation_data['by_status'][$status]['count']++;
            $valuation_data['by_status'][$status]['value'] += $price;

            // By category
            if (!isset($valuation_data['by_category'][$category])) {
                $valuation_data['by_category'][$category] = ['count' => 0, 'value' => 0];
            }
            $valuation_data['by_category'][$category]['count']++;
            $valuation_data['by_category'][$category]['value'] += $price;
        }

        return $valuation_data;
    }

    // ====================================
    // Statistics Methods
    // ====================================

    private function getAssetInventoryStatistics($data) {
        return [
            'total_records' => count($data),
            'total_value' => array_sum(array_column($data, 'purchase_price')),
            'available_count' => count(array_filter($data, fn($a) => $a['status'] === 'Available')),
            'in_use_count' => count(array_filter($data, fn($a) => $a['status'] === 'In Use'))
        ];
    }

    private function getCheckInOutStatistics($data) {
        return [
            'total_records' => count($data),
            'check_out_count' => count(array_filter($data, fn($l) => $l['action_type'] === 'Check Out')),
            'check_in_count' => count(array_filter($data, fn($l) => $l['action_type'] === 'Check In')),
            'unique_assets' => count(array_unique(array_column($data, 'asset_id'))),
            'unique_users' => count(array_unique(array_column($data, 'user_id')))
        ];
    }

    private function getUserAssignmentStatistics($data) {
        $total_assets = 0;
        $total_value = 0;
        foreach ($data as $user) {
            $total_assets += count($user['assets']);
            $total_value += $user['total_value'];
        }
        return [
            'total_users' => count($data),
            'total_assets' => $total_assets,
            'total_value' => $total_value
        ];
    }

    private function getAssetByCategoryStatistics($data) {
        $total_assets = 0;
        $total_value = 0;
        foreach ($data as $category) {
            $total_assets += $category['total_count'];
            $total_value += $category['total_value'];
        }
        return [
            'total_categories' => count($data),
            'total_assets' => $total_assets,
            'total_value' => $total_value
        ];
    }

    private function getAssetValuationStatistics($data) {
        return [
            'total_assets' => $data['total_assets'],
            'total_value' => $data['total_value'],
            'total_categories' => count($data['by_category'])
        ];
    }

    // ====================================
    // Helper Methods
    // ====================================

    /**
     * Get filters from GET request
     */
    private function getFiltersFromRequest() {
        $filters = [];

        // Status filter
        if (!empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }

        // Category filter
        if (!empty($_GET['category'])) {
            $filters['category'] = $_GET['category'];
        }

        // Date range filter
        if (!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
        }

        if (!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
        }

        // User filter
        if (!empty($_GET['user_id'])) {
            $filters['user_id'] = $_GET['user_id'];
        }

        // Action type filter (for check logs)
        if (!empty($_GET['action_type'])) {
            $filters['action_type'] = $_GET['action_type'];
        }

        return $filters;
    }

    /**
     * Generate PDF using TCPDF
     * REQ-REPORT-007, REQ-REPORT-008
     */
    private function generatePDF($report_title, $report_data, $report_type, $filters) {
        // Create new PDF document
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('ITAM System - P-line Company');
        $pdf->SetAuthor('P-line Company');
        $pdf->SetTitle($report_title);
        $pdf->SetSubject($report_title);

        // Set header data
        $pdf->SetHeaderData('', 0, 'P-line Company', $report_title . "\n" . date('F d, Y'));

        // Set header and footer fonts
        $pdf->setHeaderFont(['helvetica', '', 12]);
        $pdf->setFooterFont(['helvetica', '', 8]);

        // Set margins
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(true, 15);

        // Add page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 10);

        // Generate content based on report type
        $html = $this->generatePDFContent($report_title, $report_data, $report_type, $filters);

        // Output HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Close and output PDF
        $filename = str_replace(' ', '_', strtolower($report_title)) . '_' . date('Y-m-d_His') . '.pdf';
        $pdf->Output($filename, 'D'); // D = Download
        exit();
    }

    /**
     * Generate PDF HTML content
     */
    private function generatePDFContent($report_title, $report_data, $report_type, $filters) {
        $html = '<h1 style="color: #2563EB; text-align: center;">' . htmlspecialchars($report_title) . '</h1>';
        $html .= '<p style="text-align: center; color: #666;">Generated on ' . date('F d, Y h:i A') . '</p>';

        // Applied Filters
        if (!empty($filters)) {
            $html .= '<div style="background-color: #f3f4f6; padding: 10px; margin-bottom: 15px; border-left: 4px solid #2563EB;">';
            $html .= '<h3 style="margin-top: 0; color: #2563EB;">Applied Filters:</h3><ul>';

            if (!empty($filters['status'])) {
                $html .= '<li><strong>Status:</strong> ' . htmlspecialchars($filters['status']) . '</li>';
            }
            if (!empty($filters['category'])) {
                $html .= '<li><strong>Category:</strong> ' . htmlspecialchars($filters['category']) . '</li>';
            }
            if (!empty($filters['date_from'])) {
                $html .= '<li><strong>Date From:</strong> ' . htmlspecialchars($filters['date_from']) . '</li>';
            }
            if (!empty($filters['date_to'])) {
                $html .= '<li><strong>Date To:</strong> ' . htmlspecialchars($filters['date_to']) . '</li>';
            }
            if (!empty($filters['user_id'])) {
                $user = $this->userModel->findById($filters['user_id']);
                $html .= '<li><strong>User:</strong> ' . htmlspecialchars($user['name'] ?? 'N/A') . '</li>';
            }
            if (!empty($filters['action_type'])) {
                $html .= '<li><strong>Action Type:</strong> ' . htmlspecialchars($filters['action_type']) . '</li>';
            }

            $html .= '</ul></div>';
        }

        // Report content based on type
        switch ($report_type) {
            case 'asset_inventory':
                $html .= $this->generateAssetInventoryPDFTable($report_data);
                break;
            case 'checkin_checkout':
                $html .= $this->generateCheckInOutPDFTable($report_data);
                break;
            case 'user_assignment':
                $html .= $this->generateUserAssignmentPDFTable($report_data);
                break;
            case 'asset_by_category':
                $html .= $this->generateAssetByCategoryPDFTable($report_data);
                break;
            case 'asset_valuation':
                $html .= $this->generateAssetValuationPDFTable($report_data);
                break;
        }

        return $html;
    }

    private function generateAssetInventoryPDFTable($data) {
        $html = '<table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%;">';
        $html .= '<thead><tr style="background-color: #2563EB; color: white;">';
        $html .= '<th>Asset Code</th><th>Name</th><th>Category</th><th>Status</th><th>Price</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($data as $asset) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($asset['asset_code']) . '</td>';
            $html .= '<td>' . htmlspecialchars($asset['asset_name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($asset['category'] ?? 'N/A') . '</td>';
            $html .= '<td>' . htmlspecialchars($asset['status']) . '</td>';
            $html .= '<td>' . format_currency($asset['purchase_price'] ?? 0) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        return $html;
    }

    private function generateCheckInOutPDFTable($data) {
        $html = '<table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%;">';
        $html .= '<thead><tr style="background-color: #2563EB; color: white;">';
        $html .= '<th>Date</th><th>Asset</th><th>Action</th><th>User</th><th>Performed By</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($data as $log) {
            $html .= '<tr>';
            $html .= '<td>' . format_date($log['action_date'], 'M d, Y H:i') . '</td>';
            $html .= '<td>' . htmlspecialchars($log['asset_name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($log['action_type']) . '</td>';
            $html .= '<td>' . htmlspecialchars($log['user_name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($log['performed_by_name']) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        return $html;
    }

    private function generateUserAssignmentPDFTable($data) {
        $html = '<table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%;">';
        $html .= '<thead><tr style="background-color: #2563EB; color: white;">';
        $html .= '<th>User</th><th>Asset Count</th><th>Total Value</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($data as $user_data) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($user_data['user_name']) . '</td>';
            $html .= '<td>' . count($user_data['assets']) . '</td>';
            $html .= '<td>' . format_currency($user_data['total_value']) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        return $html;
    }

    private function generateAssetByCategoryPDFTable($data) {
        $html = '<table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%;">';
        $html .= '<thead><tr style="background-color: #2563EB; color: white;">';
        $html .= '<th>Category</th><th>Total Assets</th><th>Available</th><th>In Use</th><th>Total Value</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($data as $category) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($category['category']) . '</td>';
            $html .= '<td>' . $category['total_count'] . '</td>';
            $html .= '<td>' . $category['available_count'] . '</td>';
            $html .= '<td>' . $category['in_use_count'] . '</td>';
            $html .= '<td>' . format_currency($category['total_value']) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        return $html;
    }

    private function generateAssetValuationPDFTable($data) {
        $html = '<h3 style="color: #2563EB;">Summary</h3>';
        $html .= '<p><strong>Total Assets:</strong> ' . $data['total_assets'] . '</p>';
        $html .= '<p><strong>Total Value:</strong> ' . format_currency($data['total_value']) . '</p>';

        $html .= '<h3 style="color: #2563EB; margin-top: 20px;">By Status</h3>';
        $html .= '<table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%;">';
        $html .= '<thead><tr style="background-color: #2563EB; color: white;">';
        $html .= '<th>Status</th><th>Count</th><th>Value</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($data['by_status'] as $status => $stats) {
            if ($stats['count'] > 0) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($status) . '</td>';
                $html .= '<td>' . $stats['count'] . '</td>';
                $html .= '<td>' . format_currency($stats['value']) . '</td>';
                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * Generate Excel using PhpSpreadsheet
     * REQ-REPORT-009, REQ-REPORT-010
     */
    private function generateExcel($report_title, $report_data, $report_type, $filters) {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('ITAM System - P-line Company')
            ->setTitle($report_title)
            ->setSubject($report_title)
            ->setDescription($report_title . ' - Generated on ' . date('Y-m-d H:i:s'));

        // Set sheet title
        $sheet->setTitle(substr($report_title, 0, 31)); // Excel limit

        // Add title
        $sheet->setCellValue('A1', 'P-line Company');
        $sheet->setCellValue('A2', $report_title);
        $sheet->setCellValue('A3', 'Generated on: ' . date('F d, Y h:i A'));

        // Style title
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setSize(10)->getColor()->setRGB('666666');

        $currentRow = 5;

        // Add filters if any
        if (!empty($filters)) {
            $sheet->setCellValue('A' . $currentRow, 'Applied Filters:');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
            $currentRow++;

            foreach ($filters as $key => $value) {
                $label = ucwords(str_replace('_', ' ', $key));
                if ($key === 'user_id') {
                    $user = $this->userModel->findById($value);
                    $value = $user['name'] ?? 'N/A';
                }
                $sheet->setCellValue('A' . $currentRow, $label . ': ' . $value);
                $currentRow++;
            }
            $currentRow++;
        }

        // Generate content based on report type
        switch ($report_type) {
            case 'asset_inventory':
                $this->addAssetInventoryToSheet($sheet, $report_data, $currentRow);
                break;
            case 'checkin_checkout':
                $this->addCheckInOutToSheet($sheet, $report_data, $currentRow);
                break;
            case 'user_assignment':
                $this->addUserAssignmentToSheet($sheet, $report_data, $currentRow);
                break;
            case 'asset_by_category':
                $this->addAssetByCategoryToSheet($sheet, $report_data, $currentRow);
                break;
            case 'asset_valuation':
                $this->addAssetValuationToSheet($sheet, $report_data, $currentRow);
                break;
        }

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output Excel file
        $filename = str_replace(' ', '_', strtolower($report_title)) . '_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    private function addAssetInventoryToSheet($sheet, $data, $startRow) {
        // Headers
        $headers = ['Asset Code', 'Name', 'Category', 'Serial Number', 'Brand', 'Model', 'Status', 'Purchase Price', 'Assigned To'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $startRow, $header);
            $col++;
        }

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A' . $startRow . ':I' . $startRow)->applyFromArray($headerStyle);

        // Freeze header row
        $sheet->freezePane('A' . ($startRow + 1));

        // Add data
        $row = $startRow + 1;
        foreach ($data as $asset) {
            $sheet->setCellValue('A' . $row, $asset['asset_code']);
            $sheet->setCellValue('B' . $row, $asset['asset_name']);
            $sheet->setCellValue('C' . $row, $asset['category'] ?? 'N/A');
            $sheet->setCellValue('D' . $row, $asset['serial_number'] ?? 'N/A');
            $sheet->setCellValue('E' . $row, $asset['brand'] ?? 'N/A');
            $sheet->setCellValue('F' . $row, $asset['model'] ?? 'N/A');
            $sheet->setCellValue('G' . $row, $asset['status']);
            $sheet->setCellValue('H' . $row, $asset['purchase_price'] ?? 0);
            $sheet->setCellValue('I' . $row, $asset['assigned_user_name'] ?? 'N/A');

            // Format currency
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');

            $row++;
        }

        // Add auto-filter
        $sheet->setAutoFilter('A' . $startRow . ':I' . ($row - 1));
    }

    private function addCheckInOutToSheet($sheet, $data, $startRow) {
        // Headers
        $headers = ['Date', 'Asset Code', 'Asset Name', 'Action', 'User', 'Performed By', 'Notes'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $startRow, $header);
            $col++;
        }

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A' . $startRow . ':G' . $startRow)->applyFromArray($headerStyle);

        // Freeze header row
        $sheet->freezePane('A' . ($startRow + 1));

        // Add data
        $row = $startRow + 1;
        foreach ($data as $log) {
            $sheet->setCellValue('A' . $row, $log['action_date']);
            $sheet->setCellValue('B' . $row, $log['asset_code']);
            $sheet->setCellValue('C' . $row, $log['asset_name']);
            $sheet->setCellValue('D' . $row, $log['action_type']);
            $sheet->setCellValue('E' . $row, $log['user_name']);
            $sheet->setCellValue('F' . $row, $log['performed_by_name']);
            $sheet->setCellValue('G' . $row, $log['notes'] ?? '');
            $row++;
        }

        // Add auto-filter
        $sheet->setAutoFilter('A' . $startRow . ':G' . ($row - 1));
    }

    private function addUserAssignmentToSheet($sheet, $data, $startRow) {
        // Headers
        $headers = ['User Name', 'Asset Count', 'Total Value', 'Assets'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $startRow, $header);
            $col++;
        }

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A' . $startRow . ':D' . $startRow)->applyFromArray($headerStyle);

        // Freeze header row
        $sheet->freezePane('A' . ($startRow + 1));

        // Add data
        $row = $startRow + 1;
        foreach ($data as $user_data) {
            $assetNames = array_map(fn($a) => $a['asset_code'] . ' - ' . $a['asset_name'], $user_data['assets']);

            $sheet->setCellValue('A' . $row, $user_data['user_name']);
            $sheet->setCellValue('B' . $row, count($user_data['assets']));
            $sheet->setCellValue('C' . $row, $user_data['total_value']);
            $sheet->setCellValue('D' . $row, implode(', ', $assetNames));

            // Format currency
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');

            $row++;
        }

        // Add auto-filter
        $sheet->setAutoFilter('A' . $startRow . ':D' . ($row - 1));
    }

    private function addAssetByCategoryToSheet($sheet, $data, $startRow) {
        // Headers
        $headers = ['Category', 'Total Assets', 'Available', 'In Use', 'Total Value'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $startRow, $header);
            $col++;
        }

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A' . $startRow . ':E' . $startRow)->applyFromArray($headerStyle);

        // Freeze header row
        $sheet->freezePane('A' . ($startRow + 1));

        // Add data
        $row = $startRow + 1;
        foreach ($data as $category) {
            $sheet->setCellValue('A' . $row, $category['category']);
            $sheet->setCellValue('B' . $row, $category['total_count']);
            $sheet->setCellValue('C' . $row, $category['available_count']);
            $sheet->setCellValue('D' . $row, $category['in_use_count']);
            $sheet->setCellValue('E' . $row, $category['total_value']);

            // Format currency
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');

            $row++;
        }

        // Add auto-filter
        $sheet->setAutoFilter('A' . $startRow . ':E' . ($row - 1));
    }

    private function addAssetValuationToSheet($sheet, $data, $startRow) {
        // Summary
        $sheet->setCellValue('A' . $startRow, 'Summary');
        $sheet->getStyle('A' . $startRow)->getFont()->setBold(true)->setSize(12);
        $startRow++;

        $sheet->setCellValue('A' . $startRow, 'Total Assets:');
        $sheet->setCellValue('B' . $startRow, $data['total_assets']);
        $startRow++;

        $sheet->setCellValue('A' . $startRow, 'Total Value:');
        $sheet->setCellValue('B' . $startRow, $data['total_value']);
        $sheet->getStyle('B' . $startRow)->getNumberFormat()->setFormatCode('$#,##0.00');
        $startRow += 2;

        // By Status
        $sheet->setCellValue('A' . $startRow, 'Valuation by Status');
        $sheet->getStyle('A' . $startRow)->getFont()->setBold(true)->setSize(12);
        $startRow++;

        $headers = ['Status', 'Count', 'Value'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $startRow, $header);
            $col++;
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']]
        ];
        $sheet->getStyle('A' . $startRow . ':C' . $startRow)->applyFromArray($headerStyle);

        $startRow++;
        foreach ($data['by_status'] as $status => $stats) {
            if ($stats['count'] > 0) {
                $sheet->setCellValue('A' . $startRow, $status);
                $sheet->setCellValue('B' . $startRow, $stats['count']);
                $sheet->setCellValue('C' . $startRow, $stats['value']);
                $sheet->getStyle('C' . $startRow)->getNumberFormat()->setFormatCode('$#,##0.00');
                $startRow++;
            }
        }
    }
}

// ====================================
// Routing Logic
// ====================================

// Initialize config and autoload
require_once __DIR__ . '/../config/init.php';

// Get action from URL
$action = $_GET['action'] ?? 'index';

// Create controller instance
$controller = new ReportController();

// Route to appropriate method
switch ($action) {
    case 'index':
        $controller->index();
        break;

    case 'generate':
        $controller->generate();
        break;

    case 'exportPdf':
        $controller->exportPdf();
        break;

    case 'exportExcel':
        $controller->exportExcel();
        break;

    default:
        $controller->index();
        break;
}
