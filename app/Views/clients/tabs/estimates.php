<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Estimates</h5>
    <button class="btn btn-sm btn-primary">New Estimate</button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Total amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($estimates)): ?>
                    <tr><td colspan="5" class="text-center py-3 text-muted">No estimates found.</td></tr>
                <?php else: ?>
                    <?php foreach ($estimates as $estimate): ?>
                    <tr>
                        <td class="fw-bold text-primary">#<?= esc($estimate['id']) ?></td>
                        <td><?= esc(date('Y-m-d', strtotime($estimate['created_at'] ?? 'now'))) ?></td>
                        <td><?= esc($estimate['title'] ?? '') ?></td>
                        <td><span class="badge bg-secondary"><?= esc($estimate['status'] ?? '') ?></span></td>
                        <td><?= number_format($estimate['total_amount'] ?? 0, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
