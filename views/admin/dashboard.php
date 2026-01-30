<?php
/**
 * Admin Dashboard View
 * ITAM System - P-line Company
 * 
 * Displays system statistics and recent activities
 * REQ-UI-005: Statistics cards
 * REQ-UI-006: Recent activities list
 */

$page_title = "Admin Dashboard";
include __DIR__ . '/../layouts/header.php';
?>

<!-- Dashboard Content -->
<div class="dashboard-content">
    
    <!-- Statistics Cards Row -->
    <div class="row g-3 mb-4">
        
        <!-- Total Assets Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card glass-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label m-0">Total Assets</p>
                        <h2 class="stat-value m-0"><?= $total_assets ?? 0 ?></h2>
                        <p class="m-0 mt-1" style="font-size: 12px; color: var(--color-success);">
                            <i data-lucide="trending-up" style="width: 14px; height: 14px;"></i>
                            All assets
                        </p>
                    </div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));">
                        <i data-lucide="package" style="width: 32px; height: 32px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Available Assets Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card glass-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label m-0">Available</p>
                        <h2 class="stat-value m-0"><?= $available_assets ?? 0 ?></h2>
                        <p class="m-0 mt-1" style="font-size: 12px; color: var(--color-success);">
                            <?php 
                            $percentage = $total_assets > 0 ? round(($available_assets / $total_assets) * 100) : 0;
                            ?>
                            <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i>
                            <?= $percentage ?>% available
                        </p>
                    </div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--color-success), #059669);">
                        <i data-lucide="check-circle" style="width: 32px; height: 32px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- In Use Assets Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card glass-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label m-0">In Use</p>
                        <h2 class="stat-value m-0"><?= $in_use_assets ?? 0 ?></h2>
                        <p class="m-0 mt-1" style="font-size: 12px; color: var(--color-warning);">
                            <?php 
                            $in_use_percentage = $total_assets > 0 ? round(($in_use_assets / $total_assets) * 100) : 0;
                            ?>
                            <i data-lucide="activity" style="width: 14px; height: 14px;"></i>
                            <?= $in_use_percentage ?>% in use
                        </p>
                    </div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--color-warning), #d97706);">
                        <i data-lucide="repeat" style="width: 32px; height: 32px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Value Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card glass-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="stat-label m-0">Total Value</p>
                        <h2 class="stat-value m-0"><?= format_currency($total_value ?? 0) ?></h2>
                        <p class="m-0 mt-1" style="font-size: 12px; color: var(--color-info);">
                            <i data-lucide="dollar-sign" style="width: 14px; height: 14px;"></i>
                            Asset worth
                        </p>
                    </div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                        <i data-lucide="dollar-sign" style="width: 32px; height: 32px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    <!-- End Statistics Cards -->
    
    <!-- Content Grid: Recent Activities + Category Chart -->
    <div class="row g-3 mb-4">
        
        <!-- Recent Activities -->
        <div class="col-12 col-lg-7">
            <div class="glass-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="m-0" style="font-size: 20px; font-weight: 600; color: var(--gray-800);">
                        <i data-lucide="activity" style="width: 20px; height: 20px; margin-right: 8px;"></i>
                        Recent Activities
                    </h3>
                    <a href="check-history.php" class="btn btn-sm btn-secondary" style="font-size: 13px;">
                        View All
                        <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                    </a>
                </div>
                
                <?php if (!empty($recent_activities)): ?>
                <div class="activity-list">
                    <?php foreach ($recent_activities as $activity): ?>
                    <div class="activity-item d-flex align-items-center gap-3 p-3 mb-2" 
                         style="background: var(--gray-50); border-radius: 12px; transition: all 0.3s ease;">
                        
                        <!-- Activity Icon -->
                        <div class="activity-icon" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; 
                             background: <?= $activity['action_type'] === 'Check Out' ? 'rgba(245, 158, 11, 0.1)' : 'rgba(16, 185, 129, 0.1)' ?>;">
                            <i data-lucide="<?= $activity['action_type'] === 'Check Out' ? 'arrow-up-circle' : 'arrow-down-circle' ?>" 
                               style="width: 20px; height: 20px; color: <?= $activity['action_type'] === 'Check Out' ? 'var(--color-warning)' : 'var(--color-success)' ?>;"></i>
                        </div>
                        
                        <!-- Activity Details -->
                        <div class="flex-grow-1">
                            <p class="m-0 fw-semibold" style="font-size: 14px; color: var(--gray-800);">
                                <?= escape($activity['action_type']) ?>: <?= escape($activity['asset_name']) ?>
                            </p>
                            <p class="m-0" style="font-size: 12px; color: var(--gray-600);">
                                <?= escape($activity['user_name']) ?> • 
                                <span class="badge badge-info" style="font-size: 11px;"><?= escape($activity['asset_code']) ?></span>
                            </p>
                        </div>
                        
                        <!-- Time -->
                        <div class="text-end">
                            <p class="m-0" style="font-size: 12px; color: var(--gray-500);">
                                <?= time_ago($activity['action_date']) ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i data-lucide="inbox" style="width: 48px; height: 48px; color: var(--gray-400);"></i>
                    <p class="mt-3 mb-0" style="color: var(--gray-600);">No recent activities</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Assets by Category -->
        <div class="col-12 col-lg-5">
            <div class="glass-card p-4">
                <h3 class="mb-3" style="font-size: 20px; font-weight: 600; color: var(--gray-800);">
                    <i data-lucide="pie-chart" style="width: 20px; height: 20px; margin-right: 8px;"></i>
                    Assets by Category
                </h3>
                
                <?php if (!empty($assets_by_category)): ?>
                <div class="category-list">
                    <?php 
                    $colors = ['#2563EB', '#10B981', '#F59E0B', '#8b5cf6', '#ec4899'];
                    $total_category_assets = array_sum(array_column($assets_by_category, 'count'));
                    foreach ($assets_by_category as $index => $cat): 
                        $percentage = $total_category_assets > 0 ? round(($cat['count'] / $total_category_assets) * 100) : 0;
                        $color = $colors[$index % count($colors)];
                    ?>
                    <div class="category-item mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-medium" style="font-size: 14px; color: var(--gray-700);">
                                <?= escape($cat['category']) ?>
                            </span>
                            <span class="fw-semibold" style="font-size: 14px; color: var(--gray-800);">
                                <?= $cat['count'] ?> items
                            </span>
                        </div>
                        <div class="progress" style="height: 8px; background: var(--gray-200); border-radius: 4px;">
                            <div class="progress-bar" 
                                 role="progressbar" 
                                 style="width: <?= $percentage ?>%; background: <?= $color ?>; border-radius: 4px;"
                                 aria-valuenow="<?= $percentage ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i data-lucide="inbox" style="width: 48px; height: 48px; color: var(--gray-400);"></i>
                    <p class="mt-3 mb-0" style="color: var(--gray-600);">No assets found</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
    <!-- End Content Grid -->
    
    <!-- Quick Actions -->
    <div class="glass-card p-4">
        <h3 class="mb-3" style="font-size: 20px; font-weight: 600; color: var(--gray-800);">
            <i data-lucide="zap" style="width: 20px; height: 20px; margin-right: 8px;"></i>
            Quick Actions
        </h3>
        
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <a href="assets.php?action=create" class="btn btn-primary w-100 d-flex flex-column align-items-center gap-2 py-3">
                    <i data-lucide="plus-circle" style="width: 24px; height: 24px;"></i>
                    <span>Add Asset</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="users.php?action=create" class="btn w-100 d-flex flex-column align-items-center gap-2 py-3" style="background: linear-gradient(135deg, var(--color-success), #059669); color: white;">
                    <i data-lucide="user-plus" style="width: 24px; height: 24px;"></i>
                    <span>Add User</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="check-out.php" class="btn w-100 d-flex flex-column align-items-center gap-2 py-3" style="background: linear-gradient(135deg, var(--color-warning), #d97706); color: white;">
                    <i data-lucide="repeat" style="width: 24px; height: 24px;"></i>
                    <span>Check Out</span>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="reports.php" class="btn w-100 d-flex flex-column align-items-center gap-2 py-3" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: white;">
                    <i data-lucide="file-text" style="width: 24px; height: 24px;"></i>
                    <span>Reports</span>
                </a>
            </div>
        </div>
    </div>
    
</div>
<!-- End Dashboard Content -->

<style>
.activity-item:hover {
    background: white !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>