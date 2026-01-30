<?php
/**
 * Header Layout
 * ITAM System - P-line Company
 * 
 * Displays page header with user info and notifications
 */

$user_name = $_SESSION['user_name'] ?? 'Guest';
$user_email = $_SESSION['user_email'] ?? '';
$user_role = $_SESSION['user_role'] ?? 'User';
$user_initials = get_user_initials($user_name);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= isset($page_title) ? escape($page_title) . ' - ' : '' ?>ITAM System</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/public/assets/images/favicon.ico">
    
    <!-- Bootstrap 5 CSS -->
    <link href="/public/assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS (Glassmorphism) -->
    <link href="/public/assets/css/custom.css" rel="stylesheet">
    
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh;">

<!-- Include Sidebar -->
<?php include __DIR__ . '/sidebar.php'; ?>

<!-- Main Content Wrapper -->
<div class="main-wrapper" style="margin-left: 256px; transition: margin-left 0.3s ease;">
    
    <!-- Top Header Bar -->
    <header class="top-header glass-card" style="margin: 16px; padding: 16px 24px; border-radius: 16px;">
        <div class="d-flex align-items-center justify-content-between">
            
            <!-- Left: Page Title & Breadcrumb -->
            <div class="header-left">
                <button class="btn-icon d-lg-none me-3" id="sidebarToggle">
                    <i data-lucide="menu" style="width: 20px; height: 20px;"></i>
                </button>
                
                <div>
                    <h1 class="m-0" style="font-size: 24px; font-weight: 700; color: var(--gray-800);">
                        <?= isset($page_title) ? escape($page_title) : 'Dashboard' ?>
                    </h1>
                    <p class="m-0" style="font-size: 14px; color: var(--gray-600);">
                        P-line Company - Vientiane, Laos
                    </p>
                </div>
            </div>
            
            <!-- Right: Notifications & User Profile -->
            <div class="header-right d-flex align-items-center gap-3">
                
                <!-- Notifications Button -->
                <div class="position-relative">
                    <button class="btn-icon" id="notificationBtn" title="Notifications">
                        <i data-lucide="bell" style="width: 20px; height: 20px;"></i>
                    </button>
                    <!-- Notification Badge -->
                    <span class="notification-badge" style="position: absolute; top: -4px; right: -4px; width: 8px; height: 8px; background: var(--color-error); border-radius: 50%; border: 2px solid white;"></span>
                </div>
                
                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <button class="d-flex align-items-center gap-3 border-0 bg-transparent" 
                            id="userDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false"
                            style="cursor: pointer;">
                        
                        <!-- User Avatar with Initials -->
                        <div class="user-avatar" style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 16px;">
                            <?= escape($user_initials) ?>
                        </div>
                        
                        <!-- User Info (Hidden on mobile) -->
                        <div class="d-none d-md-block text-start">
                            <p class="m-0" style="font-size: 14px; font-weight: 600; color: var(--gray-800);">
                                <?= escape($user_name) ?>
                            </p>
                            <p class="m-0" style="font-size: 12px; color: var(--gray-600);">
                                <?= escape($user_role) ?>
                            </p>
                        </div>
                        
                        <!-- Dropdown Icon -->
                        <i data-lucide="chevron-down" style="width: 16px; height: 16px; color: var(--gray-600);"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <ul class="dropdown-menu dropdown-menu-end glass-card" style="min-width: 200px; border-radius: 12px; padding: 8px; margin-top: 8px;">
                        <li>
                            <div class="dropdown-item-text px-3 py-2">
                                <p class="m-0 fw-semibold" style="font-size: 14px;"><?= escape($user_name) ?></p>
                                <p class="m-0 text-muted" style="font-size: 12px;"><?= escape($user_email) ?></p>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="profile.php" style="border-radius: 8px; padding: 10px 12px;">
                                <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="profile.php#change-password" style="border-radius: 8px; padding: 10px 12px;">
                                <i data-lucide="lock" style="width: 16px; height: 16px;"></i>
                                <span>Change Password</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="logout.php" style="border-radius: 8px; padding: 10px 12px;">
                                <i data-lucide="log-out" style="width: 16px; height: 16px;"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Flash Messages (Toast Notifications) -->
    <?php
    $flash = get_flash();
    if ($flash):
    ?>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div class="toast show align-items-center text-white border-0 <?= $flash['type'] === 'success' ? 'bg-success' : 'bg-danger' ?>" 
             role="alert" 
             aria-live="assertive" 
             aria-atomic="true"
             style="border-radius: 12px;">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i data-lucide="<?= $flash['type'] === 'success' ? 'check-circle' : 'alert-circle' ?>" style="width: 20px; height: 20px;"></i>
                    <span><?= escape($flash['message']) ?></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Page Content -->
    <main class="page-content" style="padding: 0 16px 16px 16px; min-height: calc(100vh - 120px);">