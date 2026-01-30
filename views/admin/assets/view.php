<?php
/**
 * Asset Detail View
 * ITAM System - P-line Company
 * 
 * Display detailed information about a specific asset
 */

$page_title = "Asset Details";
require_once __DIR__ . '/../../layouts/header.php';
?>

<!-- Main Content -->
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 font-bold text-gray-800">Asset Details</h1>
            <p class="text-gray-600 mb-0">
                <span class="badge badge-info font-mono">
                    <?= htmlspecialchars($asset['asset_code']) ?>
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <?php if (is_admin()): ?>
                <a href="/views/admin/assets/edit.php?id=<?= $asset['asset_id'] ?>" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit Asset
                </a>
            <?php endif; ?>
            <a href="/views/admin/assets/index.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Asset Information Card -->
        <div class="col-lg-8">
            <div class="glass-card p-4">
                <h3 class="h5 font-bold text-gray-800 mb-4 pb-3 border-bottom">
                    <i class="bi bi-info-circle text-primary me-2"></i>Asset Information
                </h3>

                <div class="row g-4">
                    <!-- Asset Name -->
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="text-sm text-gray-600 mb-1 d-block">Asset Name</label>
                            <p class="font-medium text-gray-800 mb-0">
                                <?= htmlspecialchars($asset['asset_name']) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Asset Code -->
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="text-sm text-gray-600 mb-1 d-block">Asset Code</label>
                            <p class="font-medium text-gray-800 mb-0">
                                <span class="badge badge-info font-mono">
                                    <?= htmlspecialchars($asset['asset_code']) ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="text-sm text-gray-600 mb-1 d-block">Category</label>
                            <p class="font-medium text-gray-800 mb-0">
                                <span class="badge badge-info">
                                    <i class="bi bi-tag me-1"></i>
                                    <?= htmlspecialchars($asset['category']) ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="text-sm text-gray-600 mb-1 d-block">Status</label>
                            <p class="font-medium text-gray-800 mb-0">
                                <?php if ($asset['status'] === 'Available'): ?>
                                    <span class="badge badge-success">
                                        <i class="bi bi-check-circle me-1"></i>Available
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-warning">
                                        <i class="bi bi-clock-history me-1"></i>In Use
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <!-- Serial Number -->
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="text-sm text-gray-600 mb-1 d-block">Serial Number</label>
                            <p class="font-medium text-gray-800 mb-0">
                                <?= !empty($asset['serial_number']) ? htmlspecialchars($asset['serial_number']) : '-' ?>
                            </p>
                        </div>
                    </div>

                    <!-- Brand -->
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="text-sm text-gray-600 mb-1 d-block">Brand</label>
                            <p class="font-medium text-gray-800 mb-0">
                                <?= !empty($asset['brand']) ? htmlspecialchars($asset['brand']) : '-' ?>
                            </p>
                        </div>
                    </div>

                    <!-- Model -->
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="text-sm text-gray-600 mb-1 d-block">Model</label>
                            <p class="font-medium text-gray-800 mb-0">
                                <?= !empty($asset['model']) ? htmlspecialchars($asset['model']) : '-' ?>
                            </p>
                        </div>
                    </div>

                    <!-- Purchase Date -->
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="text-sm text-gray-600 mb-1 d-block">Purchase Date</label>
                            <p class="font-medium text-gray-800 mb-0">
                                <i class="bi bi-calendar3 me-1"></i>
                                <?= format_date($asset['purchase_date'] ?? '') ?>
                            </p>
                        </div>
                    </div>

                    <!-- Purchase Price -->
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="text-sm text-gray-600 mb-1 d-block">Purchase Price</label>
                            <p class="font-medium text-primary mb-0">
                                <i class="bi bi-cash me-1"></i>
                                <?= !empty($asset['purchase_price']) ? format_currency($asset['purchase_price']) : '-' ?>
                            </p>
                        </div>
                    </div>

                    <!-- Assigned To -->
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="text-sm text-gray-600 mb-1 d-block">Assigned To</label>
                            <p class="font-medium text-gray-800 mb-0">
                                <?php if (!empty($asset['assigned_user_name'])): ?>
                                    <i class="bi bi-person-fill text-primary me-1"></i>
                                    <?= htmlspecialchars($asset['assigned_user_name']) ?>
                                <?php else: ?>
                                    <span class="text-gray-500">Not Assigned</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <!-- Assigned Date -->
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="text-sm text-gray-600 mb-1 d-block">Assigned Date</label>
                            <p class="font-medium text-gray-800 mb-0">
                                <?php if (!empty($asset['assigned_date'])): ?>
                                    <i class="bi bi-calendar-check me-1"></i>
                                    <?= format_date($asset['assigned_date']) ?>
                                <?php else: ?>
                                    <span class="text-gray-500">-</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <!-- Created At -->
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="text-sm text-gray-600 mb-1 d-block">Created At</label>
                            <p class="font-medium text-gray-800 mb-0">
                                <i class="bi bi-clock me-1"></i>
                                <?= format_date($asset['created_at'], 'M d, Y H:i') ?>
                            </p>
                        </div>
                    </div>

                    <!-- Updated At -->
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="text-sm text-gray-600 mb-1 d-block">Last Updated</label>
                            <p class="font-medium text-gray-800 mb-0">
                                <i class="bi bi-clock-history me-1"></i>
                                <?= format_date($asset['updated_at'], 'M d, Y H:i') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asset Photo Card -->
        <div class="col-lg-4">
            <div class="glass-card p-4">
                <h3 class="h5 font-bold text-gray-800 mb-4 pb-3 border-bottom">
                    <i class="bi bi-image text-primary me-2"></i>Asset Photo
                </h3>

                <?php if (!empty($asset['photo_url'])): ?>
                    <div class="text-center">
                        <img src="/public/<?= htmlspecialchars($asset['photo_url']) ?>" 
                             alt="<?= htmlspecialchars($asset['asset_name']) ?>" 
                             class="img-fluid rounded shadow-sm"
                             style="max-height: 400px;">
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-image display-1 text-gray-300"></i>
                        <p class="text-gray-600 mt-3 mb-0">No photo available</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions Card -->
            <?php if (is_admin()): ?>
                <div class="glass-card p-4 mt-4">
                    <h3 class="h6 font-bold text-gray-800 mb-3">Quick Actions</h3>
                    <div class="d-grid gap-2">
                        <a href="/views/admin/assets/edit.php?id=<?= $asset['asset_id'] ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Asset
                        </a>
                        
                        <?php if ($asset['status'] === 'Available'): ?>
                            <a href="/views/admin/checkin-checkout/checkout.php?asset_id=<?= $asset['asset_id'] ?>" class="btn btn-sm btn-success">
                                <i class="bi bi-box-arrow-right me-2"></i>Check Out
                            </a>
                        <?php else: ?>
                            <a href="/views/admin/checkin-checkout/checkin.php?asset_id=<?= $asset['asset_id'] ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-box-arrow-in-left me-2"></i>Check In
                            </a>
                        <?php endif; ?>
                        
                        <button type="button" 
                                class="btn btn-sm btn-danger"
                                onclick="confirmDelete(<?= $asset['asset_id'] ?>, '<?= htmlspecialchars($asset['asset_name'], ENT_QUOTES) ?>')">
                            <i class="bi bi-trash me-2"></i>Delete Asset
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<?php if (is_admin()): ?>
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
<?php endif; ?>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>