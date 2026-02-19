<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Contracts</h5>
    <button class="btn btn-sm btn-primary">New Contract</button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Title</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                 <?php if (empty($contracts)): ?>
                    <tr><td colspan="4" class="text-center py-3 text-muted">No contracts found.</td></tr>
                <?php else: ?>
                    <?php foreach ($contracts as $contract): ?>
                    <tr>
                        <td class="fw-bold text-primary"><?= esc($contract['title']) ?></td>
                        <td><?= esc($contract['contract_date']) ?></td>
                        <td><?= esc($contract['valid_until']) ?></td>
                        <td><span class="badge bg-secondary"><?= esc($contract['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
