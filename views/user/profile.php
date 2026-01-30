<?php
/**
 * User Profile View
 * ITAM System - P-line Company
 *
 * User profile page with assigned assets, activity history, and password change
 * Security: REQ-SEC-007 (CSRF Protection)
 * UI: REQ-UI-030 to REQ-UI-031
 */

$page_title = "My Profile";
require_once __DIR__ . '/../layouts/header.php';

// Get form errors and data from session
$errors = $_SESSION['form_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_data']);
?>

<!-- Main Content -->
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 font-bold text-gray-800">
                <i data-lucide="user" class="me-2"></i>My Profile
            </h1>
            <p class="text-gray-600 mb-0">Manage your account information and settings</p>
        </div>
    </div>

    <?php if ($flash = get_flash_message()): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Left Column: Profile Info & Statistics -->
        <div class="col-md-4">
            <!-- User Profile Card -->
            <div class="glass-card p-4 text-center mb-4">
                <!-- Large Avatar -->
                <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 32px;">
                    <?= htmlspecialchars(get_user_initials($user['name'])) ?>
                </div>

                <h3 class="h4 font-bold mb-1"><?= htmlspecialchars($user['name']) ?></h3>
                <p class="text-gray-600 mb-3">
                    <i data-lucide="mail" style="width: 14px; height: 14px;"></i>
                    <?= htmlspecialchars($user['email']) ?>
                </p>

                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge <?= $user['role'] === 'Admin' ? 'badge-primary' : 'badge-secondary' ?>">
                        <i data-lucide="shield" style="width: 12px; height: 12px;"></i>
                        <?= htmlspecialchars($user['role']) ?>
                    </span>
                    <span class="badge badge-success">
                        <i data-lucide="check-circle" style="width: 12px; height: 12px;"></i>
                        Active
                    </span>
                </div>

                <div class="text-sm text-gray-600">
                    <div class="mb-2">
                        <strong>Member Since:</strong>
                        <div><?= format_date($user['created_at'], 'M d, Y') ?></div>
                        <small class="text-gray-500"><?= time_ago($user['created_at']) ?></small>
                    </div>
                    <?php if (!empty($user['last_login'])): ?>
                        <div>
                            <strong>Last Login:</strong>
                            <div><?= format_date($user['last_login'], 'M d, Y h:i A') ?></div>
                            <small class="text-gray-500"><?= time_ago($user['last_login']) ?></small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="glass-card p-4">
                <h3 class="h6 font-bold mb-3">
                    <i data-lucide="bar-chart" class="me-2"></i>My Statistics
                </h3>

                <div class="stat-card mb-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));">
                        <i data-lucide="package"></i>
                    </div>
                    <div>
                        <div class="stat-label">Total Assigned</div>
                        <div class="stat-value"><?= $total_assigned ?? 0 ?></div>
                    </div>
                </div>

                <div class="stat-card mb-3">
                    <div class="stat-icon bg-warning">
                        <i data-lucide="repeat"></i>
                    </div>
                    <div>
                        <div class="stat-label">Currently In Use</div>
                        <div class="stat-value"><?= $currently_in_use ?? 0 ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bg-info">
                        <i data-lucide="activity"></i>
                    </div>
                    <div>
                        <div class="stat-label">Total Activities</div>
                        <div class="stat-value"><?= $total_activities ?? 0 ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Assigned Assets & Activity -->
        <div class="col-md-8">
            <!-- Assigned Assets -->
            <div class="glass-card p-4 mb-4">
                <h3 class="h5 font-bold mb-4">
                    <i data-lucide="package" class="me-2"></i>My Assigned Assets
                </h3>

                <?php if (empty($assigned_assets)): ?>
                    <div class="text-center py-4">
                        <i data-lucide="inbox" style="width: 48px; height: 48px;" class="text-gray-400 mb-3"></i>
                        <p class="text-gray-600 mb-0">No assets currently assigned</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($assigned_assets as $asset): ?>
                            <div class="col-md-6">
                                <div class="glass-card-sm p-3">
                                    <div class="d-flex gap-3">
                                        <!-- Asset Image/Icon -->
                                        <?php if (!empty($asset['photo_url'])): ?>
                                            <img src="/public/<?= htmlspecialchars($asset['photo_url']) ?>"
                                                 alt="<?= htmlspecialchars($asset['asset_name']) ?>"
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                        <?php else: ?>
                                            <div style="width: 60px; height: 60px; background: var(--gray-200); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i data-lucide="package" style="width: 24px; height: 24px;" class="text-gray-400"></i>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Asset Info -->
                                        <div class="flex-grow-1">
                                            <h4 class="h6 font-bold mb-1"><?= htmlspecialchars($asset['asset_name']) ?></h4>
                                            <div class="text-sm text-gray-600 mb-2">
                                                <span class="badge badge-primary"><?= htmlspecialchars($asset['asset_code']) ?></span>
                                                <span class="badge badge-info"><?= htmlspecialchars($asset['category']) ?></span>
                                            </div>
                                            <div class="text-sm">
                                                <span class="badge badge-warning">
                                                    <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                                                    <?= htmlspecialchars($asset['status']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Activity -->
            <div class="glass-card p-4 mb-4">
                <h3 class="h5 font-bold mb-4">
                    <i data-lucide="activity" class="me-2"></i>Recent Activity
                </h3>

                <?php if (empty($recent_activities)): ?>
                    <div class="text-center py-4">
                        <i data-lucide="inbox" style="width: 48px; height: 48px;" class="text-gray-400 mb-3"></i>
                        <p class="text-gray-600 mb-0">No recent activities</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Asset</th>
                                    <th>Action</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_activities as $activity): ?>
                                    <tr>
                                        <td>
                                            <div class="text-sm"><?= format_date($activity['action_date'], 'M d, Y') ?></div>
                                            <small class="text-gray-500"><?= time_ago($activity['action_date']) ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?= htmlspecialchars($activity['asset_name']) ?></div>
                                            <small class="text-gray-600"><?= htmlspecialchars($activity['asset_code']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge <?= $activity['action_type'] === 'Check Out' ? 'badge-success' : 'badge-info' ?>">
                                                <?= htmlspecialchars($activity['action_type']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($activity['notes'])): ?>
                                                <small class="text-gray-600"><?= htmlspecialchars(substr($activity['notes'], 0, 50)) . (strlen($activity['notes']) > 50 ? '...' : '') ?></small>
                                            <?php else: ?>
                                                <span class="text-gray-400">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Update Profile Section -->
            <div class="glass-card p-4 mb-4">
                <h3 class="h5 font-bold mb-4 text-primary">
                    <i data-lucide="edit" class="me-2"></i>Update Profile Information
                </h3>

                <form method="POST" action="/controllers/ProfileController.php?action=updateProfile" id="updateProfileForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token()) ?>">

                    <!-- Name Field -->
                    <div class="input-group mb-4">
                        <label class="input-label required">
                            <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                            Full Name
                        </label>
                        <input type="text"
                               name="name"
                               class="input-field <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($form_data['name'] ?? $user['name']) ?>"
                               required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Email Field -->
                    <div class="input-group mb-4">
                        <label class="input-label required">
                            <i data-lucide="mail" style="width: 16px; height: 16px;"></i>
                            Email Address
                        </label>
                        <input type="email"
                               name="email"
                               class="input-field <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($form_data['email'] ?? $user['email']) ?>"
                               required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="me-2"></i>
                        Update Profile
                    </button>
                </form>
            </div>

            <!-- Change Password Section -->
            <div class="glass-card p-4" id="change-password">
                <h3 class="h5 font-bold mb-4 text-primary">
                    <i data-lucide="lock" class="me-2"></i>Change Password
                </h3>

                <div class="alert alert-info d-flex align-items-start mb-4">
                    <i data-lucide="info" class="me-3 mt-1" style="width: 20px; height: 20px;"></i>
                    <div>
                        <strong>Password Security:</strong> Choose a strong password with at least 6 characters.
                        Your password is securely hashed before storage.
                    </div>
                </div>

                <form method="POST" action="/controllers/ProfileController.php?action=changePassword" id="changePasswordForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token()) ?>">

                    <!-- Current Password -->
                    <div class="input-group mb-4">
                        <label class="input-label required">
                            <i data-lucide="lock" style="width: 16px; height: 16px;"></i>
                            Current Password
                        </label>
                        <input type="password"
                               name="current_password"
                               class="input-field <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>"
                               placeholder="Enter your current password"
                               required>
                        <?php if (isset($errors['current_password'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['current_password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- New Password -->
                    <div class="input-group mb-4">
                        <label class="input-label required">
                            <i data-lucide="key" style="width: 16px; height: 16px;"></i>
                            New Password
                        </label>
                        <input type="password"
                               name="new_password"
                               class="input-field <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>"
                               placeholder="Enter new password (minimum 6 characters)"
                               id="newPasswordField"
                               required>
                        <?php if (isset($errors['new_password'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['new_password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="input-group mb-4">
                        <label class="input-label required">
                            <i data-lucide="key" style="width: 16px; height: 16px;"></i>
                            Confirm New Password
                        </label>
                        <input type="password"
                               name="confirm_password"
                               class="input-field <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                               placeholder="Re-enter new password"
                               id="confirmNewPasswordField"
                               required>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="shield" class="me-2"></i>
                        Change Password
                    </button>
                </form>
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

    // Password change form validation
    const changePasswordForm = document.getElementById('changePasswordForm');
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('newPasswordField').value;
            const confirmPassword = document.getElementById('confirmNewPasswordField').value;

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match!');
                return false;
            }

            if (newPassword.length < 6) {
                e.preventDefault();
                alert('New password must be at least 6 characters long!');
                return false;
            }
        });
    }

    // Auto-scroll to password section if hash is present
    if (window.location.hash === '#change-password') {
        setTimeout(function() {
            document.getElementById('change-password').scrollIntoView({ behavior: 'smooth' });
        }, 100);
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
