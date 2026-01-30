<?php
/**
 * Reports Dashboard View
 * ITAM System - P-line Company
 *
 * Report generation interface with filters and export capabilities
 * Phase 3H: REQ-REPORT-001 to REQ-REPORT-010
 */

$page_title = "Reports & Analytics";
require_once __DIR__ . '/../../layouts/header.php';

// Get current report type and filters
$current_report_type = $_GET['report_type'] ?? '';
$current_filters = [
    'status' => $_GET['status'] ?? '',
    'category' => $_GET['category'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? '',
    'user_id' => $_GET['user_id'] ?? '',
    'action_type' => $_GET['action_type'] ?? ''
];

// Check if we're showing report results
$show_results = isset($report_data);
?>

<!-- Main Content -->
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 font-bold text-gray-800">
                <i data-lucide="file-text" class="me-2"></i>Reports & Analytics
            </h1>
            <p class="text-gray-600 mb-0">Generate comprehensive reports with custom filters</p>
        </div>
    </div>

    <?php if ($flash = get_flash_message()): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Report Type Selection -->
    <div class="glass-card p-4 mb-4">
        <div class="d-flex align-items-center mb-3">
            <i data-lucide="layout-grid" class="me-2"></i>
            <h3 class="h5 mb-0 font-bold">Select Report Type</h3>
        </div>

        <div class="row g-3">
            <!-- Asset Inventory Report -->
            <div class="col-md-6 col-lg-4">
                <div class="report-card <?= $current_report_type === 'asset_inventory' ? 'active' : '' ?>"
                     onclick="selectReportType('asset_inventory')">
                    <div class="report-icon" style="background: linear-gradient(135deg, #2563EB, #7C3AED);">
                        <i data-lucide="package"></i>
                    </div>
                    <div class="report-info">
                        <h4 class="report-title">Asset Inventory</h4>
                        <p class="report-description">Complete list of all assets with details</p>
                    </div>
                </div>
            </div>

            <!-- Check-In/Check-Out History Report -->
            <div class="col-md-6 col-lg-4">
                <div class="report-card <?= $current_report_type === 'checkin_checkout' ? 'active' : '' ?>"
                     onclick="selectReportType('checkin_checkout')">
                    <div class="report-icon bg-success">
                        <i data-lucide="activity"></i>
                    </div>
                    <div class="report-info">
                        <h4 class="report-title">Check-In/Out History</h4>
                        <p class="report-description">All check-in and check-out transactions</p>
                    </div>
                </div>
            </div>

            <!-- User Assignment Report -->
            <div class="col-md-6 col-lg-4">
                <div class="report-card <?= $current_report_type === 'user_assignment' ? 'active' : '' ?>"
                     onclick="selectReportType('user_assignment')">
                    <div class="report-icon bg-info">
                        <i data-lucide="users"></i>
                    </div>
                    <div class="report-info">
                        <h4 class="report-title">User Assignments</h4>
                        <p class="report-description">Assets assigned to each user</p>
                    </div>
                </div>
            </div>

            <!-- Asset by Category Report -->
            <div class="col-md-6 col-lg-4">
                <div class="report-card <?= $current_report_type === 'asset_by_category' ? 'active' : '' ?>"
                     onclick="selectReportType('asset_by_category')">
                    <div class="report-icon bg-warning">
                        <i data-lucide="grid"></i>
                    </div>
                    <div class="report-info">
                        <h4 class="report-title">Assets by Category</h4>
                        <p class="report-description">Grouped by category with statistics</p>
                    </div>
                </div>
            </div>

            <!-- Asset Valuation Report -->
            <div class="col-md-6 col-lg-4">
                <div class="report-card <?= $current_report_type === 'asset_valuation' ? 'active' : '' ?>"
                     onclick="selectReportType('asset_valuation')">
                    <div class="report-icon" style="background: linear-gradient(135deg, #10B981, #059669);">
                        <i data-lucide="dollar-sign"></i>
                    </div>
                    <div class="report-info">
                        <h4 class="report-title">Asset Valuation</h4>
                        <p class="report-description">Total value calculations and breakdown</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="glass-card p-4 mb-4" id="filtersSection">
        <div class="d-flex align-items-center mb-3">
            <i data-lucide="filter" class="me-2"></i>
            <h3 class="h5 mb-0 font-bold">Filters</h3>
        </div>

        <form method="GET" action="/controllers/ReportController.php" id="reportForm">
            <input type="hidden" name="action" value="generate">
            <input type="hidden" name="report_type" id="report_type" value="<?= htmlspecialchars($current_report_type) ?>">

            <div class="row g-3">
                <!-- Status Filter (for asset reports) -->
                <div class="col-md-3 filter-group asset-filter">
                    <label class="input-label">Status</label>
                    <select name="status" class="input-field">
                        <option value="">All Status</option>
                        <option value="Available" <?= $current_filters['status'] === 'Available' ? 'selected' : '' ?>>Available</option>
                        <option value="In Use" <?= $current_filters['status'] === 'In Use' ? 'selected' : '' ?>>In Use</option>
                        <option value="Maintenance" <?= $current_filters['status'] === 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        <option value="Retired" <?= $current_filters['status'] === 'Retired' ? 'selected' : '' ?>>Retired</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div class="col-md-3 filter-group asset-filter">
                    <label class="input-label">Category</label>
                    <select name="category" class="input-field">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category) ?>"
                                    <?= $current_filters['category'] === $category ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- User Filter (for assignment reports) -->
                <div class="col-md-3 filter-group user-filter">
                    <label class="input-label">User</label>
                    <select name="user_id" class="input-field">
                        <option value="">All Users</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['user_id']) ?>"
                                    <?= $current_filters['user_id'] == $user['user_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Action Type Filter (for check logs) -->
                <div class="col-md-3 filter-group checkin-filter">
                    <label class="input-label">Action Type</label>
                    <select name="action_type" class="input-field">
                        <option value="">All Actions</option>
                        <option value="Check Out" <?= $current_filters['action_type'] === 'Check Out' ? 'selected' : '' ?>>Check Out</option>
                        <option value="Check In" <?= $current_filters['action_type'] === 'Check In' ? 'selected' : '' ?>>Check In</option>
                    </select>
                </div>

                <!-- Date From -->
                <div class="col-md-3 filter-group date-filter">
                    <label class="input-label">Date From</label>
                    <input type="date" name="date_from" class="input-field"
                           value="<?= htmlspecialchars($current_filters['date_from']) ?>">
                </div>

                <!-- Date To -->
                <div class="col-md-3 filter-group date-filter">
                    <label class="input-label">Date To</label>
                    <input type="date" name="date_to" class="input-field"
                           value="<?= htmlspecialchars($current_filters['date_to']) ?>">
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary" id="generateBtn" disabled>
                    <i data-lucide="play" class="me-2"></i>Generate Report
                </button>
                <a href="/controllers/ReportController.php" class="btn btn-secondary">
                    <i data-lucide="x" class="me-2"></i>Clear All
                </a>
            </div>
        </form>
    </div>

    <?php if ($show_results): ?>
        <!-- Statistics Cards -->
        <?php if (!empty($statistics)): ?>
            <div class="row g-3 mb-4">
                <?php if (isset($statistics['total_records'])): ?>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));">
                                <i data-lucide="hash"></i>
                            </div>
                            <div>
                                <div class="stat-label">Total Records</div>
                                <div class="stat-value"><?= number_format($statistics['total_records']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($statistics['total_value'])): ?>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-success">
                                <i data-lucide="dollar-sign"></i>
                            </div>
                            <div>
                                <div class="stat-label">Total Value</div>
                                <div class="stat-value"><?= format_currency($statistics['total_value']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($statistics['available_count'])): ?>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-info">
                                <i data-lucide="check-circle"></i>
                            </div>
                            <div>
                                <div class="stat-label">Available</div>
                                <div class="stat-value"><?= number_format($statistics['available_count']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($statistics['in_use_count'])): ?>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning">
                                <i data-lucide="users"></i>
                            </div>
                            <div>
                                <div class="stat-label">In Use</div>
                                <div class="stat-value"><?= number_format($statistics['in_use_count']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($statistics['total_users'])): ?>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-info">
                                <i data-lucide="users"></i>
                            </div>
                            <div>
                                <div class="stat-label">Total Users</div>
                                <div class="stat-value"><?= number_format($statistics['total_users']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($statistics['total_assets'])): ?>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary">
                                <i data-lucide="package"></i>
                            </div>
                            <div>
                                <div class="stat-label">Total Assets</div>
                                <div class="stat-value"><?= number_format($statistics['total_assets']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($statistics['total_categories'])): ?>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning">
                                <i data-lucide="grid"></i>
                            </div>
                            <div>
                                <div class="stat-label">Categories</div>
                                <div class="stat-value"><?= number_format($statistics['total_categories']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Export Buttons -->
        <div class="glass-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="h5 mb-1 font-bold">
                        <i data-lucide="download" class="me-2"></i><?= htmlspecialchars($report_title) ?>
                    </h3>
                    <p class="text-gray-600 mb-0">Export your report in your preferred format</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="/controllers/ReportController.php?action=exportPdf&<?= http_build_query(array_merge(['report_type' => $current_report_type], $current_filters)) ?>"
                       class="btn btn-danger">
                        <i data-lucide="file-text" class="me-2"></i>Export PDF
                    </a>
                    <a href="/controllers/ReportController.php?action=exportExcel&<?= http_build_query(array_merge(['report_type' => $current_report_type], $current_filters)) ?>"
                       class="btn btn-success">
                        <i data-lucide="file-spreadsheet" class="me-2"></i>Export Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Report Preview -->
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="h5 mb-0 font-bold">
                    <i data-lucide="eye" class="me-2"></i>Report Preview
                </h3>
                <span class="badge badge-info">
                    <?= !empty($report_data) ? count($report_data) : 0 ?> records found
                </span>
            </div>

            <?php if (empty($report_data)): ?>
                <div class="text-center py-5">
                    <i data-lucide="inbox" style="width: 64px; height: 64px;" class="text-gray-400 mb-3"></i>
                    <p class="text-gray-600 mb-0">No data found for the selected filters</p>
                    <small class="text-gray-500">Try adjusting your filter criteria</small>
                </div>
            <?php else: ?>
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <?php
                    // Render appropriate table based on report type
                    switch ($current_report_type) {
                        case 'asset_inventory':
                            include __DIR__ . '/preview_asset_inventory.php';
                            break;
                        case 'checkin_checkout':
                            include __DIR__ . '/preview_checkin_checkout.php';
                            break;
                        case 'user_assignment':
                            include __DIR__ . '/preview_user_assignment.php';
                            break;
                        case 'asset_by_category':
                            include __DIR__ . '/preview_asset_by_category.php';
                            break;
                        case 'asset_valuation':
                            include __DIR__ . '/preview_asset_valuation.php';
                            break;
                        default:
                            echo '<p class="text-gray-600">Please select a report type to view data</p>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Report Card Styling -->
<style>
.report-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(16px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.report-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    border-color: var(--color-primary);
}

.report-card.active {
    border-color: var(--color-primary);
    background: rgba(37, 99, 235, 0.1);
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.2);
}

.report-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.report-icon i {
    width: 28px;
    height: 28px;
}

.report-info {
    flex: 1;
}

.report-title {
    font-size: 16px;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 4px;
}

.report-description {
    font-size: 13px;
    color: #6B7280;
    margin: 0;
}

.filter-group {
    display: none;
}

.filter-group.active {
    display: block;
}
</style>

<!-- JavaScript -->
<script>
// Report type selection
let selectedReportType = '<?= htmlspecialchars($current_report_type) ?>';

function selectReportType(type) {
    selectedReportType = type;
    document.getElementById('report_type').value = type;

    // Update UI
    document.querySelectorAll('.report-card').forEach(card => {
        card.classList.remove('active');
    });
    event.currentTarget.classList.add('active');

    // Show/hide relevant filters
    updateFilters();

    // Enable generate button
    document.getElementById('generateBtn').disabled = false;
}

function updateFilters() {
    // Hide all filters first
    document.querySelectorAll('.filter-group').forEach(group => {
        group.classList.remove('active');
    });

    // Show relevant filters based on report type
    switch (selectedReportType) {
        case 'asset_inventory':
        case 'asset_by_category':
        case 'asset_valuation':
            document.querySelectorAll('.asset-filter').forEach(el => el.classList.add('active'));
            break;

        case 'checkin_checkout':
            document.querySelectorAll('.checkin-filter, .date-filter, .user-filter').forEach(el => el.classList.add('active'));
            break;

        case 'user_assignment':
            document.querySelectorAll('.user-filter, .asset-filter').forEach(el => el.classList.add('active'));
            break;
    }

    // Date filters are useful for most reports
    if (selectedReportType !== 'asset_inventory' && selectedReportType !== 'asset_valuation') {
        document.querySelectorAll('.date-filter').forEach(el => el.classList.add('active'));
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // If report type is already selected, show appropriate filters
    if (selectedReportType) {
        updateFilters();
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
