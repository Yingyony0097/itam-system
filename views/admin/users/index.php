<?php
/**
 * User List View
 * ITAM System - P-line Company
 *
 * Display all users with search/filter and CRUD actions
 * Security: REQ-SEC-007 (CSRF Protection)
 * UI: REQ-UI-024 to REQ-UI-026
 */

$page_title = "User Management";
require_once __DIR__ . '/../../layouts/header.php';
?>

<!-- Main Content -->
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 font-bold text-gray-800">
                <i data-lucide="users" class="me-2"></i>User Management
            </h1>
            <p class="text-gray-600 mb-0">Manage system users and their access</p>
        </div>
        <a href="/controllers/UserController.php?action=create" class="btn btn-primary">
            <i data-lucide="user-plus" class="me-2"></i>Add New User
        </a>
    </div>

    <?php if ($flash = get_flash_message()): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));">
                    <i data-lucide="users"></i>
                </div>
                <div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value"><?= $total_users ?? 0 ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i data-lucide="user-check"></i>
                </div>
                <div>
                    <div class="stat-label">Active Users</div>
                    <div class="stat-value"><?= $active_users ?? 0 ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i data-lucide="shield"></i>
                </div>
                <div>
                    <div class="stat-label">Administrators</div>
                    <div class="stat-value"><?= $admin_count ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="glass-card p-4 mb-4">
        <div class="d-flex align-items-center mb-3">
            <i data-lucide="filter" class="me-2"></i>
            <h3 class="h5 mb-0 font-bold">Search & Filter</h3>
        </div>

        <form method="GET" action="/controllers/UserController.php" id="filterForm">
            <input type="hidden" name="action" value="index">

            <div class="row g-3">
                <!-- Search Text -->
                <div class="col-md-5">
                    <label class="input-label">Search</label>
                    <input type="text"
                           name="search"
                           class="input-field"
                           placeholder="Search by name or email..."
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>

                <!-- Role Filter -->
                <div class="col-md-3">
                    <label class="input-label">Role</label>
                    <select name="role" class="input-field">
                        <option value="">All Roles</option>
                        <option value="Admin" <?= (isset($_GET['role']) && $_GET['role'] === 'Admin') ? 'selected' : '' ?>>
                            Admin
                        </option>
                        <option value="User" <?= (isset($_GET['role']) && $_GET['role'] === 'User') ? 'selected' : '' ?>>
                            User
                        </option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-md-2">
                    <label class="input-label">Status</label>
                    <select name="status" class="input-field">
                        <option value="">All Status</option>
                        <option value="1" <?= (isset($_GET['status']) && $_GET['status'] === '1') ? 'selected' : '' ?>>
                            Active
                        </option>
                        <option value="0" <?= (isset($_GET['status']) && $_GET['status'] === '0') ? 'selected' : '' ?>>
                            Inactive
                        </option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="col-md-2">
                    <label class="input-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                        </button>
                        <a href="/controllers/UserController.php?action=index" class="btn btn-secondary">
                            <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="h5 mb-0 font-bold">
                <i data-lucide="list" class="me-2"></i>Users List
            </h3>
            <span class="text-gray-600">Showing <?= count($users) ?> user(s)</span>
        </div>

        <?php if (empty($users)): ?>
            <div class="text-center py-5">
                <i data-lucide="inbox" style="width: 64px; height: 64px;" class="text-gray-400 mb-3"></i>
                <p class="text-gray-600 mb-0">No users found</p>
                <small class="text-gray-500">Try adjusting your search filters</small>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th>Last Login</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <!-- User Avatar & Name -->
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="user-avatar" style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 14px;">
                                            <?= htmlspecialchars(get_user_initials($user['name'])) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($user['name']) ?></div>
                                            <small class="text-gray-600">ID: <?= htmlspecialchars($user['user_id']) ?></small>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td>
                                    <i data-lucide="mail" style="width: 14px; height: 14px;" class="me-1 text-gray-500"></i>
                                    <?= htmlspecialchars($user['email']) ?>
                                </td>

                                <!-- Role -->
                                <td>
                                    <?php if ($user['role'] === 'Admin'): ?>
                                        <span class="badge" style="background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); color: white;">
                                            <i data-lucide="shield" style="width: 12px; height: 12px;"></i> Admin
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">
                                            <i data-lucide="user" style="width: 12px; height: 12px;"></i> User
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Status -->
                                <td>
                                    <?php if ($user['is_active']): ?>
                                        <span class="badge badge-success">
                                            <i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> Active
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-error">
                                            <i data-lucide="x-circle" style="width: 12px; height: 12px;"></i> Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Joined Date -->
                                <td>
                                    <div class="text-sm"><?= format_date($user['created_at']) ?></div>
                                    <small class="text-gray-500"><?= time_ago($user['created_at']) ?></small>
                                </td>

                                <!-- Last Login -->
                                <td>
                                    <?php if (!empty($user['last_login'])): ?>
                                        <div class="text-sm"><?= format_date($user['last_login']) ?></div>
                                        <small class="text-gray-500"><?= time_ago($user['last_login']) ?></small>
                                    <?php else: ?>
                                        <span class="text-gray-500">Never</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <!-- Edit Button -->
                                        <a href="/controllers/UserController.php?action=edit&id=<?= $user['user_id'] ?>"
                                           class="btn btn-sm btn-primary"
                                           title="Edit User">
                                            <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                                        </a>

                                        <!-- Toggle Status Button -->
                                        <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                                            <form method="POST"
                                                  action="/controllers/UserController.php?action=toggleStatus"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to <?= $user['is_active'] ? 'deactivate' : 'activate' ?> this user?');">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token()) ?>">
                                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                                <button type="submit"
                                                        class="btn btn-sm btn-<?= $user['is_active'] ? 'warning' : 'success' ?>"
                                                        title="<?= $user['is_active'] ? 'Deactivate' : 'Activate' ?> User">
                                                    <i data-lucide="<?= $user['is_active'] ? 'user-x' : 'user-check' ?>" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>

                                            <!-- Delete Button -->
                                            <form method="POST"
                                                  action="/controllers/UserController.php?action=delete"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone!');">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token()) ?>">
                                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete User">
                                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge badge-info" style="font-size: 11px;">You</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Initialize Icons -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
