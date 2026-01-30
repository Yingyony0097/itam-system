<table class="data-table">
    <thead>
        <tr>
            <th>Asset Code</th>
            <th>Name</th>
            <th>Category</th>
            <th>Serial Number</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Status</th>
            <th>Purchase Price</th>
            <th>Assigned To</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($report_data as $asset): ?>
            <tr>
                <td><strong><?= htmlspecialchars($asset['asset_code']) ?></strong></td>
                <td><?= htmlspecialchars($asset['asset_name']) ?></td>
                <td><span class="badge badge-info"><?= htmlspecialchars($asset['category'] ?? 'N/A') ?></span></td>
                <td><?= htmlspecialchars($asset['serial_number'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($asset['brand'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($asset['model'] ?? 'N/A') ?></td>
                <td>
                    <?php
                    $status = $asset['status'];
                    $badgeClass = 'badge-secondary';
                    if ($status === 'Available') $badgeClass = 'badge-success';
                    elseif ($status === 'In Use') $badgeClass = 'badge-info';
                    elseif ($status === 'Maintenance') $badgeClass = 'badge-warning';
                    elseif ($status === 'Retired') $badgeClass = 'badge-error';
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                </td>
                <td><?= format_currency($asset['purchase_price'] ?? 0) ?></td>
                <td><?= htmlspecialchars($asset['assigned_user_name'] ?? 'N/A') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
