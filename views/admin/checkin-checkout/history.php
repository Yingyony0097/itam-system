<?php
/**
 * Check-In/Check-Out History View
 * ITAM System - P-line Company
 *
 * Display all check-in/check-out logs with filters
 */

$page_title = "Check-In/Check-Out History";
require_once __DIR__ . '/../../layouts/header.php';
?>

<!-- Main Content -->
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 font-bold text-gray-800">
                <i data-lucide="history" class="me-2"></i>Check-In/Check-Out History
            </h1>
            <p class="text-gray-600 mb-0">View all asset check-in and check-out transactions</p>
        </div>
        <a href="/views/admin/assets/index.php" class="btn btn-secondary">
            <i data-lucide="arrow-left" class="me-2"></i>Back to Assets
        </a>
    </div>

    <?php if ($flash = get_flash_message()): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filters Card -->
    <div class="glass-card p-4 mb-4">
        <div class="d-flex align-items-center mb-3">
            <i data-lucide="filter" class="me-2"></i>
            <h3 class="h5 mb-0 font-bold">Filters</h3>
        </div>

        <form method="GET" action="/controllers/CheckController.php" id="filterForm">
            <input type="hidden" name="action" value="history">

            <div class="row g-3">
                <!-- Asset Filter -->
                <div class="col-md-3">
                    <label class="input-label">Asset</label>
                    <select name="asset_id" class="input-field">
                        <option value="">All Assets</option>
                        <?php foreach ($assets as $asset): ?>
                            <option value="<?= htmlspecialchars($asset['asset_id']) ?>"
                                    <?= (isset($_GET['asset_id']) && $_GET['asset_id'] == $asset['asset_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($asset['asset_code']) ?> - <?= htmlspecialchars($asset['asset_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- User Filter -->
                <div class="col-md-3">
                    <label class="input-label">User</label>
                    <select name="user_id" class="input-field">
                        <option value="">All Users</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['user_id']) ?>"
                                    <?= (isset($_GET['user_id']) && $_GET['user_id'] == $user['user_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Action Type Filter -->
                <div class="col-md-2">
                    <label class="input-label">Action Type</label>
                    <select name="action_type" class="input-field">
                        <option value="">All Actions</option>
                        <option value="Check Out" <?= (isset($_GET['action_type']) && $_GET['action_type'] === 'Check Out') ? 'selected' : '' ?>>
                            Check Out
                        </option>
                        <option value="Check In" <?= (isset($_GET['action_type']) && $_GET['action_type'] === 'Check In') ? 'selected' : '' ?>>
                            Check In
                        </option>
                    </select>
                </div>

                <!-- Date From -->
                <div class="col-md-2">
                    <label class="input-label">Date From</label>
                    <input type="date" name="date_from" class="input-field"
                           value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
                </div>

                <!-- Date To -->
                <div class="col-md-2">
                    <label class="input-label">Date To</label>
                    <input type="date" name="date_to" class="input-field"
                           value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="search" class="me-2"></i>Apply Filters
                </button>
                <a href="/controllers/CheckController.php?action=history" class="btn btn-secondary">
                    <i data-lucide="x" class="me-2"></i>Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i data-lucide="activity"></i>
                </div>
                <div>
                    <div class="stat-label">Total Transactions</div>
                    <div class="stat-value"><?= count($logs) ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i data-lucide="log-out"></i>
                </div>
                <div>
                    <div class="stat-label">Check Outs</div>
                    <div class="stat-value">
                        <?= count(array_filter($logs, function($log) { return $log['action_type'] === 'Check Out'; })) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i data-lucide="log-in"></i>
                </div>
                <div>
                    <div class="stat-label">Check Ins</div>
                    <div class="stat-value">
                        <?= count(array_filter($logs, function($log) { return $log['action_type'] === 'Check In'; })) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i data-lucide="users"></i>
                </div>
                <div>
                    <div class="stat-label">Active Users</div>
                    <div class="stat-value">
                        <?= count(array_unique(array_column($logs, 'user_id'))) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Timeline/Table -->
    <div class="glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="h5 mb-0 font-bold">
                <i data-lucide="list" class="me-2"></i>Transaction History
            </h3>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-primary active" id="timelineViewBtn">
                    <i data-lucide="git-branch" style="width: 16px; height: 16px;"></i> Timeline
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary" id="tableViewBtn">
                    <i data-lucide="table" style="width: 16px; height: 16px;"></i> Table
                </button>
            </div>
        </div>

        <?php if (empty($logs)): ?>
            <div class="text-center py-5">
                <i data-lucide="inbox" style="width: 64px; height: 64px;" class="text-gray-400 mb-3"></i>
                <p class="text-gray-600 mb-0">No transaction history found</p>
                <small class="text-gray-500">Transactions will appear here once assets are checked in or out</small>
            </div>
        <?php else: ?>
            <!-- Timeline View -->
            <div id="timelineView" class="timeline-container">
                <?php foreach ($logs as $index => $log): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker <?= $log['action_type'] === 'Check Out' ? 'bg-success' : 'bg-info' ?>">
                            <i data-lucide="<?= $log['action_type'] === 'Check Out' ? 'log-out' : 'log-in' ?>" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="timeline-content glass-card-sm p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h4 class="h6 mb-1 font-bold">
                                        <span class="badge <?= $log['action_type'] === 'Check Out' ? 'badge-success' : 'badge-info' ?>">
                                            <?= htmlspecialchars($log['action_type']) ?>
                                        </span>
                                        <?= htmlspecialchars($log['asset_name']) ?>
                                    </h4>
                                    <div class="text-gray-600 small">
                                        <i data-lucide="package" style="width: 14px; height: 14px;"></i>
                                        <strong><?= htmlspecialchars($log['asset_code']) ?></strong>
                                        <?php if (!empty($log['category'])): ?>
                                            | <span class="badge badge-info"><?= htmlspecialchars($log['category']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <small class="text-gray-500">
                                    <?= time_ago($log['action_date']) ?>
                                </small>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center text-sm">
                                        <i data-lucide="user" class="me-2" style="width: 14px; height: 14px;"></i>
                                        <strong>User:</strong>
                                        <span class="ms-2"><?= htmlspecialchars($log['user_name']) ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center text-sm">
                                        <i data-lucide="user-check" class="me-2" style="width: 14px; height: 14px;"></i>
                                        <strong>By:</strong>
                                        <span class="ms-2"><?= htmlspecialchars($log['performed_by_name']) ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center text-sm mb-2">
                                <i data-lucide="calendar" class="me-2" style="width: 14px; height: 14px;"></i>
                                <strong>Date:</strong>
                                <span class="ms-2"><?= format_date($log['action_date']) ?></span>
                            </div>

                            <?php if (!empty($log['notes'])): ?>
                                <div class="alert alert-light mb-0 mt-2 p-2">
                                    <i data-lucide="message-square" style="width: 14px; height: 14px;"></i>
                                    <strong>Notes:</strong>
                                    <div class="mt-1 text-sm"><?= nl2br(htmlspecialchars($log['notes'])) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Table View -->
            <div id="tableView" class="table-responsive" style="display: none;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Asset</th>
                            <th>Action</th>
                            <th>User</th>
                            <th>Performed By</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?= date('M d, Y', strtotime($log['action_date'])) ?></div>
                                    <small class="text-gray-600"><?= date('h:i A', strtotime($log['action_date'])) ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($log['asset_name']) ?></div>
                                    <small class="text-gray-600"><?= htmlspecialchars($log['asset_code']) ?></small>
                                </td>
                                <td>
                                    <span class="badge <?= $log['action_type'] === 'Check Out' ? 'badge-success' : 'badge-info' ?>">
                                        <?= htmlspecialchars($log['action_type']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($log['user_name']) ?></td>
                                <td><?= htmlspecialchars($log['performed_by_name']) ?></td>
                                <td>
                                    <?php if (!empty($log['notes'])): ?>
                                        <button class="btn btn-sm btn-outline-primary"
                                                onclick="showNotes('<?= htmlspecialchars(addslashes($log['notes']), ENT_QUOTES) ?>')">
                                            <i data-lucide="message-square" style="width: 14px; height: 14px;"></i>
                                            View
                                        </button>
                                    <?php else: ?>
                                        <span class="text-gray-500">No notes</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Custom Timeline CSS -->
<style>
.timeline-container {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 30px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -19px;
    top: 40px;
    bottom: -10px;
    width: 2px;
    background: linear-gradient(to bottom, rgba(37, 99, 235, 0.3), rgba(37, 99, 235, 0.1));
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.timeline-content {
    margin-left: 20px;
}

@media (max-width: 768px) {
    .timeline-container {
        padding-left: 20px;
    }

    .timeline-marker {
        width: 32px;
        height: 32px;
        left: -26px;
    }

    .timeline-item::before {
        left: -15px;
    }
}
</style>

<!-- View Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // View toggle functionality
    const timelineViewBtn = document.getElementById('timelineViewBtn');
    const tableViewBtn = document.getElementById('tableViewBtn');
    const timelineView = document.getElementById('timelineView');
    const tableView = document.getElementById('tableView');

    if (timelineViewBtn && tableViewBtn) {
        timelineViewBtn.addEventListener('click', function() {
            timelineView.style.display = 'block';
            tableView.style.display = 'none';
            timelineViewBtn.classList.add('active');
            tableViewBtn.classList.remove('active');
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        tableViewBtn.addEventListener('click', function() {
            timelineView.style.display = 'none';
            tableView.style.display = 'block';
            tableViewBtn.classList.add('active');
            timelineViewBtn.classList.remove('active');
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    }
});

// Show notes in modal/alert
function showNotes(notes) {
    alert('Notes:\n\n' + notes);
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
