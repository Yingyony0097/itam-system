<?php
/**
 * Edit User View
 * ITAM System - P-line Company
 *
 * Form for editing an existing user
 * Security: REQ-SEC-007 (CSRF Protection)
 * UI: REQ-UI-024 to REQ-UI-026
 */

$page_title = "Edit User";
require_once __DIR__ . '/../../layouts/header.php';

// Get form errors and data from session
$errors = $_SESSION['form_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_data']);

// Check if editing self
$is_editing_self = ($user['user_id'] == $_SESSION['user_id']);
?>

<!-- Main Content -->
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 font-bold text-gray-800">
                <i data-lucide="user-cog" class="me-2"></i>Edit User
            </h1>
            <p class="text-gray-600 mb-0">Update user information</p>
        </div>
        <a href="/controllers/UserController.php?action=index" class="btn btn-secondary">
            <i data-lucide="arrow-left" class="me-2"></i>Back to Users
        </a>
    </div>

    <?php if ($flash = get_flash_message()): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- User Form Card -->
            <div class="glass-card p-4">
                <!-- User Avatar & Info Header -->
                <div class="d-flex align-items-center gap-3 mb-4 p-3" style="background: var(--gray-50); border-radius: 12px;">
                    <div class="user-avatar" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 24px;">
                        <?= htmlspecialchars(get_user_initials($user['name'])) ?>
                    </div>
                    <div>
                        <h3 class="h5 font-bold mb-1"><?= htmlspecialchars($user['name']) ?></h3>
                        <div class="text-sm text-gray-600">
                            <span class="badge <?= $user['role'] === 'Admin' ? 'badge-primary' : 'badge-secondary' ?>">
                                <?= htmlspecialchars($user['role']) ?>
                            </span>
                            <span class="ms-2">ID: <?= htmlspecialchars($user['user_id']) ?></span>
                        </div>
                    </div>
                </div>

                <h3 class="h5 font-bold mb-4 text-primary">
                    <i data-lucide="edit" class="me-2"></i>User Information
                </h3>

                <?php if ($is_editing_self): ?>
                    <div class="alert alert-warning d-flex align-items-start mb-4">
                        <i data-lucide="alert-triangle" class="me-3 mt-1" style="width: 20px; height: 20px;"></i>
                        <div>
                            <strong>Note:</strong> You are editing your own account.
                            You cannot change your own status. Use the profile page to update your password.
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/controllers/UserController.php?action=update" id="editUserForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token()) ?>">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">

                    <!-- Name Field -->
                    <div class="input-group mb-4">
                        <label class="input-label required">
                            <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                            Full Name
                        </label>
                        <input type="text"
                               name="name"
                               class="input-field <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                               placeholder="Enter full name"
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
                               placeholder="user@example.com"
                               value="<?= htmlspecialchars($form_data['email'] ?? $user['email']) ?>"
                               required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Password Change Section (Collapsible) -->
                    <div class="glass-card-sm p-3 mb-4">
                        <div class="form-check mb-3">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="changePasswordCheck"
                                   onclick="togglePasswordFields()">
                            <label class="form-check-label fw-bold" for="changePasswordCheck">
                                <i data-lucide="lock" style="width: 16px; height: 16px;"></i>
                                Change Password
                            </label>
                        </div>

                        <div id="passwordFields" style="display: none;">
                            <!-- New Password -->
                            <div class="input-group mb-3">
                                <label class="input-label">
                                    <i data-lucide="lock" style="width: 16px; height: 16px;"></i>
                                    New Password
                                </label>
                                <input type="password"
                                       name="password"
                                       class="input-field <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                       placeholder="Enter new password (minimum 6 characters)"
                                       id="passwordField">
                                <?php if (isset($errors['password'])): ?>
                                    <div class="error-message"><?= htmlspecialchars($errors['password']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Confirm Password -->
                            <div class="input-group mb-0">
                                <label class="input-label">
                                    <i data-lucide="lock" style="width: 16px; height: 16px;"></i>
                                    Confirm New Password
                                </label>
                                <input type="password"
                                       name="confirm_password"
                                       class="input-field <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                                       placeholder="Re-enter new password"
                                       id="confirmPasswordField">
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="error-message"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Role Field -->
                    <div class="input-group mb-4">
                        <label class="input-label required">
                            <i data-lucide="shield" style="width: 16px; height: 16px;"></i>
                            User Role
                        </label>
                        <select name="role" class="input-field <?= isset($errors['role']) ? 'is-invalid' : '' ?>" required>
                            <option value="Admin" <?= ($user['role'] === 'Admin') ? 'selected' : '' ?>>
                                Administrator
                            </option>
                            <option value="User" <?= ($user['role'] === 'User') ? 'selected' : '' ?>>
                                User
                            </option>
                        </select>
                        <?php if (isset($errors['role'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['role']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Active Status Checkbox -->
                    <?php if (!$is_editing_self): ?>
                        <div class="input-group mb-4">
                            <div class="form-check">
                                <input type="checkbox"
                                       name="is_active"
                                       class="form-check-input"
                                       id="isActiveCheck"
                                       value="1"
                                       <?= $user['is_active'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="isActiveCheck">
                                    <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                                    Active (User can login)
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- User Metadata -->
                    <div class="glass-card-sm p-3 mb-4">
                        <h4 class="h6 font-bold mb-3">
                            <i data-lucide="info" style="width: 16px; height: 16px;"></i>
                            Account Information
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-sm">
                                    <strong>Created:</strong>
                                    <div class="text-gray-600"><?= format_date($user['created_at'], 'M d, Y h:i A') ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-sm">
                                    <strong>Last Login:</strong>
                                    <div class="text-gray-600">
                                        <?= !empty($user['last_login']) ? format_date($user['last_login'], 'M d, Y h:i A') : 'Never' ?>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($user['updated_at'])): ?>
                                <div class="col-12">
                                    <div class="text-sm">
                                        <strong>Last Updated:</strong>
                                        <div class="text-gray-600">
                                            <?= format_date($user['updated_at'], 'M d, Y h:i A') ?>
                                            (<?= time_ago($user['updated_at']) ?>)
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i data-lucide="save" class="me-2"></i>
                            Update User
                        </button>
                        <a href="/controllers/UserController.php?action=index" class="btn btn-secondary">
                            <i data-lucide="x" class="me-2"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Form Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Form validation
    const form = document.getElementById('editUserForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const changePasswordCheck = document.getElementById('changePasswordCheck');

            if (changePasswordCheck && changePasswordCheck.checked) {
                const password = document.getElementById('passwordField').value;
                const confirmPassword = document.getElementById('confirmPasswordField').value;

                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                    return false;
                }

                if (password.length < 6) {
                    e.preventDefault();
                    alert('Password must be at least 6 characters long!');
                    return false;
                }
            }
        });
    }
});

// Toggle password fields visibility
function togglePasswordFields() {
    const passwordFields = document.getElementById('passwordFields');
    const changePasswordCheck = document.getElementById('changePasswordCheck');
    const passwordField = document.getElementById('passwordField');
    const confirmPasswordField = document.getElementById('confirmPasswordField');

    if (changePasswordCheck.checked) {
        passwordFields.style.display = 'block';
        passwordField.required = true;
        confirmPasswordField.required = true;
    } else {
        passwordFields.style.display = 'none';
        passwordField.required = false;
        confirmPasswordField.required = false;
        passwordField.value = '';
        confirmPasswordField.value = '';
    }

    // Re-initialize icons after DOM change
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
