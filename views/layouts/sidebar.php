<?php
/**
 * Sidebar Navigation Layout
 * ITAM System - P-line Company
 * 
 * Displays role-based navigation menu
 * Admin: Dashboard, Assets, Users, Reports
 * User: Dashboard, My Assets, Profile
 */

// Get current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
$user_role = $_SESSION['user_role'] ?? 'User';
?>

<!-- Sidebar Navigation -->
<aside class="sidebar" id="sidebar">
    <!-- Brand Logo -->
    <div class="sidebar-brand">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="brand-icon" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="package" style="width: 28px; height: 28px; color: white;"></i>
                </div>
                <div class="brand-text">
                    <h1 class="m-0" style="font-size: 20px; font-weight: 700; color: white;">ITAM System</h1>
                    <p class="m-0" style="font-size: 12px; color: rgba(255,255,255,0.7);">P-line Company</p>
                </div>
            </div>
            <button class="btn-icon d-lg-none" id="sidebarClose" style="background: rgba(255,255,255,0.1); color: white; border: none;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav mt-4">
        <?php if ($user_role === 'Admin'): ?>
            <!-- Admin Menu Items -->
            <a href="dashboard.php" class="sidebar-item <?= ($current_page === 'dashboard.php') ? 'active' : '' ?>">
                <i data-lucide="home" style="width: 20px; height: 20px;"></i>
                <span>Dashboard</span>
            </a>

            <a href="assets.php" class="sidebar-item <?= (strpos($current_page, 'asset') !== false) ? 'active' : '' ?>">
                <i data-lucide="package" style="width: 20px; height: 20px;"></i>
                <span>Assets</span>
            </a>

            <a href="check-in-out.php" class="sidebar-item <?= (strpos($current_page, 'check') !== false) ? 'active' : '' ?>">
                <i data-lucide="repeat" style="width: 20px; height: 20px;"></i>
                <span>Check-In/Out</span>
            </a>

            <a href="users.php" class="sidebar-item <?= (strpos($current_page, 'user') !== false && $current_page !== 'dashboard.php') ? 'active' : '' ?>">
                <i data-lucide="users" style="width: 20px; height: 20px;"></i>
                <span>Users</span>
            </a>

            <a href="reports.php" class="sidebar-item <?= (strpos($current_page, 'report') !== false) ? 'active' : '' ?>">
                <i data-lucide="file-text" style="width: 20px; height: 20px;"></i>
                <span>Reports</span>
            </a>

        <?php else: ?>
            <!-- User Menu Items -->
            <a href="dashboard.php" class="sidebar-item <?= ($current_page === 'dashboard.php') ? 'active' : '' ?>">
                <i data-lucide="home" style="width: 20px; height: 20px;"></i>
                <span>Dashboard</span>
            </a>

            <a href="my-assets.php" class="sidebar-item <?= (strpos($current_page, 'asset') !== false) ? 'active' : '' ?>">
                <i data-lucide="package" style="width: 20px; height: 20px;"></i>
                <span>My Assets</span>
            </a>
        <?php endif; ?>

        <!-- Common Menu Items (Both Admin & User) -->
        <a href="profile.php" class="sidebar-item <?= (strpos($current_page, 'profile') !== false) ? 'active' : '' ?>">
            <i data-lucide="settings" style="width: 20px; height: 20px;"></i>
            <span>Profile</span>
        </a>

        <!-- Logout -->
        <a href="logout.php" class="sidebar-item" style="margin-top: auto; position: absolute; bottom: 24px; width: calc(100% - 48px);">
            <i data-lucide="log-out" style="width: 20px; height: 20px;"></i>
            <span>Logout</span>
        </a>
    </nav>
</aside>

<!-- Mobile Overlay -->
<div class="sidebar-overlay d-lg-none" id="sidebarOverlay"></div>

<style>
/* Sidebar Styles */
.sidebar {
    width: 256px;
    height: 100vh;
    background: linear-gradient(180deg, #2563EB 0%, #1E40AF 100%);
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    overflow-y: auto;
    transition: transform 0.3s ease;
}

.sidebar-brand {
    padding: 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-nav {
    padding: 16px 24px;
    height: calc(100vh - 120px);
    position: relative;
}

.sidebar-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 12px 16px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    margin-bottom: 8px;
    font-size: 15px;
    font-weight: 500;
}

.sidebar-item:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    transform: translateX(4px);
}

.sidebar-item.active {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    font-weight: 600;
    border-left: 4px solid white;
    padding-left: 12px;
}

.sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
}

/* Mobile Responsive */
@media (max-width: 991px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar.open {
        transform: translateX(0);
    }
    
    .sidebar-overlay.show {
        display: block;
    }
}

/* Scrollbar Styling */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}
</style>

<script>
// Mobile Sidebar Toggle
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.add('open');
            sidebarOverlay.classList.add('show');
        });
    }
    
    if (sidebarClose) {
        sidebarClose.addEventListener('click', function() {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('show');
        });
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('show');
        });
    }
});
</script>