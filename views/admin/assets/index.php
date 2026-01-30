<?php
/**
 * Asset List View
 * ITAM System - P-line Company
 * 
 * Display all assets with search and filter functionality
 */

$page_title = "Asset Management";
require_once __DIR__ . '/../../layouts/header.php';
?>

<!-- Main Content -->
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 font-bold text-gray-800">Asset Management</h1>
            <p class="text-gray-600 mb-0">Manage and track all IT assets</p>
        </div>
        <a href="/views/admin/assets/create.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add New Asset
        </a>
    </div>

    <?php if ($flash = get_flash_message()): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Search and Filter Card -->
    <div class="glass-card p-4 mb-4">
        <form method="GET" action="/views/admin/assets/index.php" class="row g-3">
            <!-- Search Input -->
            <div class="col-md-4">
                <label class="input-label">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="input-field" 
                           placeholder="Search by name, code, serial..."
                           value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
            </div>

            <!-- Category Filter -->
            <div class="col-md-3">
                <label class="input-label">Category</label>
                <select name="category" class="input-field">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category) ?>" 
                                <?= ($filters['category'] ?? '') === $category ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-md-3">
                <label class="input-label">Status</label>
                <select name="status" class="input-field">
                    <option value="">All Status</option>
                    <option value="Available" <?= ($filters['status'] ?? '') === 'Available' ? 'selected' : '' ?>>
                        Available
                    </option>
                    <option value="In Use" <?= ($filters['status'] ?? '') === 'In Use' ? 'selected' : '' ?>>
                        In Use
                    </option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="/views/admin/assets/index.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Assets Table Card -->
    <div class="glass-card p-0">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Asset Code</th>
                        <th>Asset Name</th>
                        <th>Category</th>
                        <th>Serial Number</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($assets)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-gray-600">
                                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                <p class="mb-0">No assets found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($assets as $asset): ?>
                            <tr>
                                <td>
                                    <span class="badge badge-info font-mono">
                                        <?= htmlspecialchars($asset['asset_code']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium text-gray-800">
                                            <?= htmlspecialchars($asset['asset_name']) ?>
                                        </div>
                                        <?php if (!empty($asset['assigned_user_name'])): ?>
                                            <small class="text-gray-600">
                                                <i class="bi bi-person-fill"></i>
                                                Assigned to: <?= htmlspecialchars($asset['assigned_user_name']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        <?= htmlspecialchars($asset['category']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="font-mono text-sm text-gray-600">
                                        <?= htmlspecialchars($asset['serial_number'] ?? '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($asset['status'] === 'Available'): ?>
                                        <span class="badge badge-success">
                                            <i class="bi bi-check-circle me-1"></i>Available
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">
                                            <i class="bi bi-clock-history me-1"></i>In Use
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="font-semibold text-gray-800">
                                        <?= !empty($asset['purchase_price']) ? format_currency($asset['purchase_price']) : '-' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- View Button -->
                                        <a href="/views/admin/assets/view.php?id=<?= $asset['asset_id'] ?>" 
                                           class="btn-icon btn-sm"
                                           title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        <!-- Edit Button -->
                                        <a href="/views/admin/assets/edit.php?id=<?= $asset['asset_id'] ?>" 
                                           class="btn-icon btn-sm"
                                           title="Edit Asset">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        
                                        <!-- Delete Button -->
                                        <button type="button" 
                                                class="btn-icon btn-sm text-danger"
                                                onclick="confirmDelete(<?= $asset['asset_id'] ?>, '<?= htmlspecialchars($asset['asset_name'], ENT_QUOTES) ?>')"
                                                title="Delete Asset">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($assets)): ?>
            <div class="p-4 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="text-sm text-gray-600 mb-0">
                        Showing <?= count($assets) ?> asset(s)
                    </p>
                    <div class="text-sm text-gray-600">
                        Total Value: <span class="font-semibold text-primary">
                            <?php
                            $total_value = array_sum(array_column($assets, 'purchase_price'));
                            echo format_currency($total_value);
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete asset <strong id="deleteAssetName"></strong>?</p>
                <p class="text-danger mb-0">
                    <small>This action cannot be undone. All related check logs will also be deleted.</small>
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete Asset
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(assetId, assetName) {
    document.getElementById('deleteAssetName').textContent = assetName;
    document.getElementById('deleteForm').action = '/views/admin/assets/delete.php?id=' + assetId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>