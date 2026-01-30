<table class="data-table">
    <thead>
        <tr>
            <th>User Name</th>
            <th>Asset Count</th>
            <th>Total Value</th>
            <th>Assigned Assets</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($report_data as $user_data): ?>
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="user-avatar" style="width: 32px; height: 32px; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 12px;">
                            <?= htmlspecialchars(get_user_initials($user_data['user_name'])) ?>
                        </div>
                        <strong><?= htmlspecialchars($user_data['user_name']) ?></strong>
                    </div>
                </td>
                <td><span class="badge badge-info"><?= count($user_data['assets']) ?> assets</span></td>
                <td><strong><?= format_currency($user_data['total_value']) ?></strong></td>
                <td>
                    <ul class="mb-0" style="padding-left: 20px;">
                        <?php foreach ($user_data['assets'] as $asset): ?>
                            <li>
                                <strong><?= htmlspecialchars($asset['asset_code']) ?></strong> -
                                <?= htmlspecialchars($asset['asset_name']) ?>
                                <small class="text-gray-600">(<?= format_currency($asset['purchase_price'] ?? 0) ?>)</small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
