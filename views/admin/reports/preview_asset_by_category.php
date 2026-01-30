<table class="data-table">
    <thead>
        <tr>
            <th>Category</th>
            <th>Total Assets</th>
            <th>Available</th>
            <th>In Use</th>
            <th>Total Value</th>
            <th>Percentage</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $grand_total = array_sum(array_column($report_data, 'total_count'));
        foreach ($report_data as $category):
            $percentage = $grand_total > 0 ? round(($category['total_count'] / $grand_total) * 100, 1) : 0;
        ?>
            <tr>
                <td><strong><?= htmlspecialchars($category['category']) ?></strong></td>
                <td><span class="badge badge-info"><?= $category['total_count'] ?></span></td>
                <td><span class="badge badge-success"><?= $category['available_count'] ?></span></td>
                <td><span class="badge badge-warning"><?= $category['in_use_count'] ?></span></td>
                <td><strong><?= format_currency($category['total_value']) ?></strong></td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress" style="flex: 1; height: 20px;">
                            <div class="progress-bar bg-primary" style="width: <?= $percentage ?>%;">
                                <?= $percentage ?>%
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background: rgba(37, 99, 235, 0.1); font-weight: 700;">
            <td>Total</td>
            <td><?= array_sum(array_column($report_data, 'total_count')) ?></td>
            <td><?= array_sum(array_column($report_data, 'available_count')) ?></td>
            <td><?= array_sum(array_column($report_data, 'in_use_count')) ?></td>
            <td><?= format_currency(array_sum(array_column($report_data, 'total_value'))) ?></td>
            <td>100%</td>
        </tr>
    </tfoot>
</table>
