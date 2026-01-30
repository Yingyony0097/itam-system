<?php
/**
 * Create User View
 * ITAM System - P-line Company
 *
 * Form for adding a new user
 * Security: REQ-SEC-007 (CSRF Protection)
 * UI: REQ-UI-024 to REQ-UI-026
 */

$page_title = "Add New User";
require_once __DIR__ . '/../../layouts/header.php';

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
                <i data-lucide="user-plus" class="me-2"></i>Add New User
            </h1>
            <p class="text-gray-600 mb-0">Create a new user account</p>
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
                <h3 class="h5 font-bold mb-4 text-primary">
                    <i data-lucide="user-cog" class="me-2"></i>User Information
                </h3>

                <form method="POST" action="/controllers/UserController.php?action=store" id="createUserForm">
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
                               placeholder="Enter full name"
                               value="<?= htmlspecialchars($form_data['name'] ?? '') ?>"
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
                               value="<?= htmlspecialchars($form_data['email'] ?? '') ?>"
                               required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
                        <?php endif; ?>
                        <small class="text-gray-600 mt-2">
                            <i data-lucide="info" style="width: 14px; height: 14px;"></i>
                            This email will be used for login
                        </small>
                    </div>

                    <!-- Password Field -->
                    <div class="input-group mb-4">
                        <label class="input-label required">
                            <i data-lucide="lock" style="width: 16px; height: 16px;"></i>
                            Password
                        </label>
                        <input type="password"
                               name="password"
                               class="input-field <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                               placeholder="Enter password (minimum 6 characters)"
                               required>
                        <?php if (isset($errors['password'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['password']) ?></div>
                        <?php endif; ?>
                        <small class="text-gray-600 mt-2">
                            <i data-lucide="shield" style="width: 14px; height: 14px;"></i>
                            Password must be at least 6 characters long
                        </small>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="input-group mb-4">
                        <label class="input-label required">
                            <i data-lucide="lock" style="width: 16px; height: 16px;"></i>
                            Confirm Password
                        </label>
                        <input type="password"
                               name="confirm_password"
                               class="input-field <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                               placeholder="Re-enter password"
                               required>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Role Field -->
                    <div class="input-group mb-4">
                        <label class="input-label required">
                            <i data-lucide="shield" style="width: 16px; height: 16px;"></i>
                            User Role
                        </label>
                        <select name="role" class="input-field <?= isset($errors['role']) ? 'is-invalid' : '' ?>" required>
                            <option value="">-- Select Role --</option>
                            <option value="Admin" <?= (isset($form_data['role']) && $form_data['role'] === 'Admin') ? 'selected' : '' ?>>
                                Administrator
                            </option>
                            <option value="User" <?= (isset($form_data['role']) && $form_data['role'] === 'User') ? 'selected' : '' ?>>
                                User
                            </option>
                        </select>
                        <?php if (isset($errors['role'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['role']) ?></div>
                        <?php endif; ?>
                        <small class="text-gray-600 mt-2">
                            <i data-lucide="info" style="width: 14px; height: 14px;"></i>
                            Admin: Full system access | User: Limited access to assigned assets
                        </small>
                    </div>

                    <!-- Active Status Checkbox -->
                    <div class="input-group mb-4">
                        <div class="form-check">
                            <input type="checkbox"
                                   name="is_active"
                                   class="form-check-input"
                                   id="isActiveCheck"
                                   value="1"
                                   <?= (!isset($form_data['is_active']) || $form_data['is_active']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActiveCheck">
                                <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                                Active (User can login)
                            </label>
                        </div>
                        <small class="text-gray-600 mt-2 d-block">
                            <i data-lucide="info" style="width: 14px; height: 14px;"></i>
                            Uncheck to create an inactive user account
                        </small>
                    </div>

                    <!-- Info Box -->
                    <div class="alert alert-info d-flex align-items-start mb-4">
                        <i data-lucide="info" class="me-3 mt-1" style="width: 20px; height: 20px;"></i>
                        <div>
                            <strong>Security Notice:</strong> User passwords are securely hashed using bcrypt before storage.
                            The user will be able to change their password after first login.
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i data-lucide="user-plus" class="me-2"></i>
                            Create User
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

<!-- Form Validation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Form validation
    const form = document.getElementById('createUserForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;

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
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
