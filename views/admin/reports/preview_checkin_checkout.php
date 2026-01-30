<table class="data-table">
    <thead>
        <tr>
            <th>Date & Time</th>
            <th>Asset Code</th>
            <th>Asset Name</th>
            <th>Action</th>
            <th>User</th>
            <th>Performed By</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($report_data as $log): ?>
            <tr>
                <td>
                    <div class="fw-bold"><?= date('M d, Y', strtotime($log['action_date'])) ?></div>
                    <small class="text-gray-600"><?= date('h:i A', strtotime($log['action_date'])) ?></small>
                </td>
                <td><strong><?= htmlspecialchars($log['asset_code']) ?></strong></td>
                <td><?= htmlspecialchars($log['asset_name']) ?></td>
                <td>
                    <?php if ($log['action_type'] === 'Check Out'): ?>
                        <span class="badge badge-success">
                            <i data-lucide="log-out" style="width: 12px; height: 12px;"></i> Check Out
                        </span>
                    <?php else: ?>
                        <span class="badge badge-info">
                            <i data-lucide="log-in" style="width: 12px; height: 12px;"></i> Check In
                        </span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($log['user_name']) ?></td>
                <td><?= htmlspecialchars($log['performed_by_name']) ?></td>
                <td><?= htmlspecialchars($log['notes'] ?? 'N/A') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
// Re-initialize Lucide icons after table render
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
