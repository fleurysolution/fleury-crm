<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Projects</h5>
    <button class="btn btn-sm btn-primary">New Project</button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Title</th>
                    <th>Start Date</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($projects)): ?>
                    <tr><td colspan="5" class="text-center py-3 text-muted">No projects found.</td></tr>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                    <tr>
                        <td class="fw-bold text-primary"><?= esc($project['title']) ?></td>
                        <td><?= esc($project['start_date']) ?></td>
                        <td><?= esc($project['deadline']) ?></td>
                        <td><span class="badge bg-secondary"><?= esc($project['status']) ?></span></td>
                        <td><?= number_format($project['price'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
