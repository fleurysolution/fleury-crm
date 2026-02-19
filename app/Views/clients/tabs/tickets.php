<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Tickets</h5>
    <button class="btn btn-sm btn-primary">New Ticket</button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                 <?php if (empty($tickets)): ?>
                    <tr><td colspan="3" class="text-center py-3 text-muted">No tickets found.</td></tr>
                <?php else: ?>
                    <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td class="fw-bold text-primary"><?= esc($ticket['title']) ?></td>
                        <td><span class="badge bg-secondary"><?= esc($ticket['status']) ?></span></td>
                        <td><?= esc($ticket['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
