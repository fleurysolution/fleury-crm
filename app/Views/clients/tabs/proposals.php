<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Proposals</h5>
    <button class="btn btn-sm btn-primary">New Proposal</button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Date</th>
                    <th>Valid Until</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                 <?php if (empty($proposals)): ?>
                    <tr><td colspan="3" class="text-center py-3 text-muted">No proposals found.</td></tr>
                <?php else: ?>
                    <?php foreach ($proposals as $proposal): ?>
                    <tr>
                        <td><?= esc($proposal['proposal_date']) ?></td>
                        <td><?= esc($proposal['valid_until']) ?></td>
                        <td><span class="badge bg-secondary"><?= esc($proposal['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
