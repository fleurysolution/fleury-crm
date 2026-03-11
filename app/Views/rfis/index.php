<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="row mb-3">
    <div class="col-md-8">
        <h4 class="fw-bold"><?= esc($title) ?></h4>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>RFI #</th>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Due Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rfis)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No RFIs found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rfis as $rfi): ?>
                            <tr>
                                <td><span class="fw-bold text-primary">#<?= esc($rfi['rfi_number']) ?></span></td>
                                <td><?= esc($rfi['title']) ?></td>
                                <td>Project #<?= esc($rfi['project_id']) ?></td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'open' => 'bg-info',
                                        'answered' => 'bg-success',
                                        'closed' => 'bg-secondary',
                                        'overdue' => 'bg-danger'
                                    ][$rfi['status']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= ucfirst($rfi['status']) ?></span>
                                </td>
                                <td>User #<?= esc($rfi['assigned_to']) ?></td>
                                <td><?= $rfi['due_date'] ? date('M d, Y', strtotime($rfi['due_date'])) : '—' ?></td>
                                <td class="text-end">
                                    <a href="<?= site_url("rfis/{$rfi['id']}") ?>" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
