<?php
/**
 * Checkin Asset View
 * ITAM System - P-line Company
 *
 * Form for checking in assets from users
 * Security: REQ-SEC-007 (CSRF Protection)
 */

$page_title = "Check In Asset";
require_once __DIR__ . '/../../layouts/header.php';
?>

<!-- Main Content -->
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 font-bold text-gray-800">
                <i data-lucide="log-in" class="me-2"></i>Check In Asset
            </h1>
            <p class="text-gray-600 mb-0">Return this asset and make it available</p>
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
                    <div><span class="badge badge-warning"><?= htmlspecialchars($asset['status']) ?></span></div>
                </div>

                <!-- Current Assignment Info -->
                <?php if (!empty($asset['assigned_user_name'])): ?>
                <div class="alert alert-warning d-flex align-items-center mt-4">
                    <i data-lucide="user" class="me-3" style="width: 24px; height: 24px;"></i>
                    <div>
                        <strong>Currently Assigned To:</strong><br>
                        <?= htmlspecialchars($asset['assigned_user_name']) ?>
                        <?php if (!empty($asset['assigned_date'])): ?>
                            <br><small class="text-gray-600">Since: <?= format_date($asset['assigned_date']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Checkin Form -->
        <div class="col-md-7">
            <div class="glass-card p-4">
                <h3 class="h5 font-bold mb-4 text-primary">
                    <i data-lucide="clipboard-check" class="me-2"></i>Check-In Details
                </h3>

                <form method="POST" action="/controllers/CheckController.php?action=processCheckin" id="checkinForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="asset_id" value="<?= htmlspecialchars($asset['asset_id']) ?>">

                    <!-- Info Display -->
                    <div class="alert alert-info d-flex align-items-start mb-4">
                        <i data-lucide="info" class="me-3 mt-1" style="width: 20px; height: 20px;"></i>
                        <div>
                            <strong>Check-In Action:</strong><br>
                            This asset will be returned and marked as "Available". The assignment will be cleared.
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="input-group mb-4">
                        <label class="input-label">
                            <i data-lucide="message-square" style="width: 16px; height: 16px;"></i>
                            Return Notes (Optional)
                        </label>
                        <textarea name="notes"
                                  class="input-field"
                                  rows="5"
                                  placeholder="Add any notes about this check-in (e.g., asset condition, any damages, maintenance needed, etc.)"></textarea>
                        <small class="text-gray-600 mt-2">
                            <i data-lucide="info" style="width: 14px; height: 14px;"></i>
                            Document the asset condition and any issues found during return
                        </small>
                    </div>

                    <!-- Transaction Safety Info -->
                    <div class="alert alert-success d-flex align-items-start mb-4">
                        <i data-lucide="shield-check" class="me-3 mt-1" style="width: 20px; height: 20px;"></i>
                        <div>
                            <strong>Transaction Safety:</strong> This operation uses database transactions to ensure data integrity.
                            Both the asset status update and check log creation will be completed together, or both will be rolled back if an error occurs.
                        </div>
                    </div>

                    <!-- Checklist Card -->
                    <div class="glass-card-sm p-4 mb-4 bg-light">
                        <h4 class="h6 font-bold mb-3">
                            <i data-lucide="clipboard-list" class="me-2"></i>Pre-Check-In Checklist
                        </h4>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="check1" required>
                            <label class="form-check-label" for="check1">
                                Asset has been physically returned
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="check2" required>
                            <label class="form-check-label" for="check2">
                                Asset condition has been inspected
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="check3" required>
                            <label class="form-check-label" for="check3">
                                Any issues have been documented in notes
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check4" required>
                            <label class="form-check-label" for="check4">
                                Asset is ready to be reassigned
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-success flex-grow-1">
                            <i data-lucide="check-circle-2" class="me-2"></i>
                            Check In Asset
                        </button>
                        <a href="/views/admin/assets/view.php?id=<?= htmlspecialchars($asset['asset_id']) ?>"
                           class="btn btn-secondary">
                            <i data-lucide="x" class="me-2"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Success Info Card -->
            <div class="glass-card p-4 mt-4 border-start border-success border-4">
                <h4 class="h6 font-bold mb-3 text-success">
                    <i data-lucide="check-circle" class="me-2"></i>After Check-In
                </h4>
                <ul class="mb-0 text-gray-700">
                    <li class="mb-2">Asset status will change to "Available"</li>
                    <li class="mb-2">User assignment will be cleared</li>
                    <li class="mb-2">A check log entry will be created to track this return</li>
                    <li class="mb-2">The asset will be available for checkout to other users</li>
                    <li>All history will be preserved in the check logs</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Form Validation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Form validation
    const checkinForm = document.getElementById('checkinForm');
    if (checkinForm) {
        checkinForm.addEventListener('submit', function(e) {
            // Check if all checklist items are checked
            const checkboxes = document.querySelectorAll('#checkinForm input[type="checkbox"]');
            let allChecked = true;

            checkboxes.forEach(function(checkbox) {
                if (!checkbox.checked) {
                    allChecked = false;
                }
            });

            if (!allChecked) {
                e.preventDefault();
                alert('Please complete all checklist items before checking in the asset.');
                return false;
            }

            // Confirm checkin
            const confirmMessage = 'Are you sure you want to check in this asset?\n\n' +
                                 'Asset: <?= htmlspecialchars($asset['asset_name']) ?> (<?= htmlspecialchars($asset['asset_code']) ?>)\n' +
                                 'Current User: <?= !empty($asset['assigned_user_name']) ? htmlspecialchars($asset['assigned_user_name']) : 'N/A' ?>\n\n' +
                                 'The asset will be marked as Available and ready for reassignment.';

            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        });
    }

    // Auto-expand textarea as user types
    const notesTextarea = document.querySelector('textarea[name="notes"]');
    if (notesTextarea) {
        notesTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
