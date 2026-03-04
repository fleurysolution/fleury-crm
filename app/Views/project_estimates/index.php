<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <div>
            <h3 class="m-0">Estimates</h3>
            <p class="text-muted m-0" style="font-size: 0.875rem;">Create and manage estimates</p>
        </div>
        <a href="<?= site_url('estimates/new') ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> New Estimate
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Valid Until</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($estimates)): ?>
                    <tr>
                        <td colspan="7" class="text-center p-4">
                            <div class="text-muted mb-2">No estimates found.</div>
                            <a href="<?= site_url('estimates/new') ?>" class="btn btn-sm btn-primary">Create one</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($estimates as $estimate): ?>
                        <tr>
                            <td class="font-mono">#<?= $estimate['id'] ?></td>
                            <td>
                                <a href="<?= site_url('clients/' . $estimate['client_id']) ?>" class="font-bold">
                                    <?= esc($estimate['company_name']) ?>
                                </a>
                            </td>
                            <td><?= date('M d, Y', strtotime($estimate['estimate_date'])) ?></td>
                            <td class="text-muted"><?= date('M d, Y', strtotime($estimate['valid_until'])) ?></td>
                            <td>
                                <?php 
                                    $statusClass = 'badge-secondary';
                                    if ($estimate['status'] == 'accepted') $statusClass = 'badge-success';
                                    if ($estimate['status'] == 'draft') $statusClass = 'badge-secondary';
                                    if ($estimate['status'] == 'sent') $statusClass = 'badge-info';
                                ?>
                                <span class="badge <?= $statusClass ?>">
                                    <?= ucfirst($estimate['status']) ?>
                                </span>
                            </td>
                            <td class="font-mono">
                                <?= $estimate['currency_symbol'] . number_format($estimate['discount_amount'], 2) ?>
                            </td>
                            <td class="text-right">
                                <a href="<?= site_url('estimates/' . $estimate['id']) ?>" class="btn btn-sm btn-secondary">View</a>
                                <a href="<?= site_url('estimates/edit/' . $estimate['id']) ?>" class="btn btn-sm btn-outline"><i class="fa-solid fa-edit"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
