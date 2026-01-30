<div class="row g-4">
    <!-- Summary Section -->
    <div class="col-12">
        <div class="glass-card-sm p-4" style="background: rgba(37, 99, 235, 0.1);">
            <h4 class="h5 font-bold mb-3">
                <i data-lucide="info" class="me-2"></i>Summary
            </h4>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-gray-600">Total Assets</div>
                    <div class="h3 font-bold text-primary"><?= number_format($report_data['total_assets']) ?></div>
                </div>
                <div class="col-md-4">
                    <div class="text-gray-600">Total Value</div>
                    <div class="h3 font-bold text-success"><?= format_currency($report_data['total_value']) ?></div>
                </div>
                <div class="col-md-4">
                    <div class="text-gray-600">Average Value</div>
                    <div class="h3 font-bold text-info">
                        <?= $report_data['total_assets'] > 0 ? format_currency($report_data['total_value'] / $report_data['total_assets']) : format_currency(0) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- By Status Section -->
    <div class="col-md-6">
        <div class="glass-card-sm p-4">
            <h4 class="h5 font-bold mb-3">
                <i data-lucide="layers" class="me-2"></i>Valuation by Status
            </h4>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                        <th>Value</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report_data['by_status'] as $status => $stats): ?>
                        <?php if ($stats['count'] > 0): ?>
                            <tr>
                                <td>
                                    <?php
                                    $badgeClass = 'badge-secondary';
                                    if ($status === 'Available') $badgeClass = 'badge-success';
                                    elseif ($status === 'In Use') $badgeClass = 'badge-info';
                                    elseif ($status === 'Maintenance') $badgeClass = 'badge-warning';
                                    elseif ($status === 'Retired') $badgeClass = 'badge-error';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                                </td>
                                <td><?= $stats['count'] ?></td>
                                <td><strong><?= format_currency($stats['value']) ?></strong></td>
                                <td>
                                    <?php
                                    $percentage = $report_data['total_value'] > 0 ? round(($stats['value'] / $report_data['total_value']) * 100, 1) : 0;
                                    ?>
                                    <div class="progress" style="width: 100px; height: 20px;">
                                        <div class="progress-bar bg-primary" style="width: <?= $percentage ?>%;">
                                            <?= $percentage ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- By Category Section -->
    <div class="col-md-6">
        <div class="glass-card-sm p-4">
            <h4 class="h5 font-bold mb-3">
                <i data-lucide="grid" class="me-2"></i>Valuation by Category
            </h4>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Count</th>
                        <th>Value</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Sort by value descending
                    $categories = $report_data['by_category'];
                    uasort($categories, function($a, $b) {
                        return $b['value'] <=> $a['value'];
                    });

                    foreach ($categories as $category => $stats):
                    ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($category) ?></strong></td>
                            <td><?= $stats['count'] ?></td>
                            <td><strong><?= format_currency($stats['value']) ?></strong></td>
                            <td>
                                <?php
                                $percentage = $report_data['total_value'] > 0 ? round(($stats['value'] / $report_data['total_value']) * 100, 1) : 0;
                                ?>
                                <div class="progress" style="width: 100px; height: 20px;">
                                    <div class="progress-bar bg-success" style="width: <?= $percentage ?>%;">
                                        <?= $percentage ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Re-initialize Lucide icons after table render
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
