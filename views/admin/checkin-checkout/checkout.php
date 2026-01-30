<?php
/**
 * Checkout Asset View
 * ITAM System - P-line Company
 *
 * Form for checking out assets to users
 * Security: REQ-SEC-007 (CSRF Protection)
 */

$page_title = "Check Out Asset";
require_once __DIR__ . '/../../layouts/header.php';
?>

<!-- Main Content -->
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 font-bold text-gray-800">
                <i data-lucide="log-out" class="me-2"></i>Check Out Asset
            </h1>
            <p class="text-gray-600 mb-0">Assign this asset to a user</p>
        </div>
        <a href="/views/admin/assets/view.php?id=<?= htmlspecialchars($asset['asset_id']) ?>" class="btn btn-secondary">
            <i data-lucide="arrow-left" class="me-2"></i>Back to Asset
        </a>
    </div>

    <?php if ($flash = get_flash_message()): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Asset Information Card -->
        <div class="col-md-5">
            <div class="glass-card p-4">
                <h3 class="h5 font-bold mb-4 text-primary">
                    <i data-lucide="package" class="me-2"></i>Asset Information
                </h3>

                <!-- Asset Photo -->
                <?php if (!empty($asset['photo_url'])): ?>
                    <div class="mb-4 text-center">
                        <img src="/public/<?= htmlspecialchars($asset['photo_url']) ?>"
                             alt="<?= htmlspecialchars($asset['asset_name']) ?>"
                             class="img-fluid rounded"
                             style="max-height: 250px; object-fit: cover;">
                    </div>
                <?php else: ?>
                    <div class="mb-4 text-center p-5 bg-light rounded">
                        <i data-lucide="image" style="width: 80px; height: 80px;" class="text-gray-400"></i>
                        <p class="text-gray-600 mb-0 mt-2">No photo available</p>
                    </div>
                <?php endif; ?>

                <!-- Asset Details -->
                <div class="mb-3">
                    <label class="text-gray-600 small mb-1">Asset Code</label>
                    <div class="fw-bold text-primary fs-5"><?= htmlspecialchars($asset['asset_code']) ?></div>
                </div>

                <div class="mb-3">
                    <label class="text-gray-600 small mb-1">Asset Name</label>
                    <div class="fw-bold"><?= htmlspecialchars($asset['asset_name']) ?></div>
                </div>

                <div class="mb-3">
                    <label class="text-gray-600 small mb-1">Category</label>
                    <div><span class="badge badge-info"><?= htmlspecialchars($asset['category']) ?></span></div>
                </div>

                <?php if (!empty($asset['brand'])): ?>
                <div class="mb-3">
                    <label class="text-gray-600 small mb-1">Brand</label>
                    <div><?= htmlspecialchars($asset['brand']) ?></div>
                </div>
                <?php endif; ?>

                <?php if (!empty($asset['model'])): ?>
                <div class="mb-3">
                    <label class="text-gray-600 small mb-1">Model</label>
                    <div><?= htmlspecialchars($asset['model']) ?></div>
                </div>
                <?php endif; ?>

                <?php if (!empty($asset['serial_number'])): ?>
                <div class="mb-3">
                    <label class="text-gray-600 small mb-1">Serial Number</label>
                    <div class="font-monospace text-sm"><?= htmlspecialchars($asset['serial_number']) ?></div>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="text-gray-600 small mb-1">Current Status</label>
                    <div><span class="badge badge-success"><?= htmlspecialchars($asset['status']) ?></span></div>
                </div>
            </div>
        </div>

        <!-- Checkout Form -->
        <div class="col-md-7">
            <div class="glass-card p-4">
                <h3 class="h5 font-bold mb-4 text-primary">
                    <i data-lucide="user-check" class="me-2"></i>Checkout Details
                </h3>

                <form method="POST" action="/controllers/CheckController.php?action=processCheckout" id="checkoutForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="asset_id" value="<?= htmlspecialchars($asset['asset_id']) ?>">

                    <!-- User Selection -->
                    <div class="input-group mb-4">
                        <label class="input-label required">
                            <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                            Select User
                        </label>
                        <select name="user_id"
                                id="user_id"
                                class="input-field"
                                required>
                            <option value="">-- Select a user to assign this asset --</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= htmlspecialchars($user['user_id']) ?>">
                                    <?= htmlspecialchars($user['name']) ?> - <?= htmlspecialchars($user['email']) ?>
                                    <?php if ($user['role'] === 'Admin'): ?>(Admin)<?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-gray-600 mt-2">
                            <i data-lucide="info" style="width: 14px; height: 14px;"></i>
                            Only active users are shown in this list
                        </small>
                    </div>

                    <!-- Notes -->
                    <div class="input-group mb-4">
                        <label class="input-label">
                            <i data-lucide="message-square" style="width: 16px; height: 16px;"></i>
                            Notes (Optional)
                        </label>
                        <textarea name="notes"
                                  class="input-field"
                                  rows="4"
                                  placeholder="Add any notes about this checkout (e.g., purpose, expected return date, condition, etc.)"></textarea>
                        <small class="text-gray-600 mt-2">
                            <i data-lucide="info" style="width: 14px; height: 14px;"></i>
                            These notes will be visible in the asset history
                        </small>
                    </div>

                    <!-- Info Box -->
                    <div class="alert alert-info d-flex align-items-start mb-4">
                        <i data-lucide="info" class="me-3 mt-1" style="width: 20px; height: 20px;"></i>
                        <div>
                            <strong>Transaction Safety:</strong> This operation uses database transactions to ensure data integrity.
                            Both the asset status update and check log creation will be completed together, or both will be rolled back if an error occurs.
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i data-lucide="check-circle" class="me-2"></i>
                            Check Out Asset
                        </button>
                        <a href="/views/admin/assets/view.php?id=<?= htmlspecialchars($asset['asset_id']) ?>"
                           class="btn btn-secondary">
                            <i data-lucide="x" class="me-2"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Warning Card -->
            <div class="glass-card p-4 mt-4 border-start border-warning border-4">
                <h4 class="h6 font-bold mb-3 text-warning">
                    <i data-lucide="alert-triangle" class="me-2"></i>Important Notes
                </h4>
                <ul class="mb-0 text-gray-700">
                    <li class="mb-2">Once checked out, the asset status will change to "In Use"</li>
                    <li class="mb-2">The selected user will be assigned to this asset</li>
                    <li class="mb-2">A check log entry will be created to track this transaction</li>
                    <li class="mb-2">The asset can be checked back in from the asset detail page</li>
                    <li>Make sure to verify the asset condition before checkout</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced User Search (JavaScript) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Enhanced searchable dropdown for users
    const userSelect = document.getElementById('user_id');

    if (userSelect) {
        // Add search capability to the select dropdown
        userSelect.addEventListener('focus', function() {
            this.size = Math.min(this.options.length, 10);
        });

        userSelect.addEventListener('blur', function() {
            this.size = 1;
        });

        // Highlight the selected option
        userSelect.addEventListener('change', function() {
            if (this.value) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            }
        });
    }

    // Form validation
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const userId = document.getElementById('user_id').value;

            if (!userId) {
                e.preventDefault();
                alert('Please select a user to assign this asset to.');
                return false;
            }

            // Confirm checkout
            const confirmMessage = 'Are you sure you want to check out this asset?\n\n' +
                                 'Asset: <?= htmlspecialchars($asset['asset_name']) ?> (<?= htmlspecialchars($asset['asset_code']) ?>)\n' +
                                 'User: ' + userSelect.options[userSelect.selectedIndex].text;

            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
