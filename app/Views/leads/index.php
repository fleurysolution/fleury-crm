<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <div>
            <h3 class="m-0">Leads</h3>
            <p class="text-muted m-0" style="font-size: 0.875rem;">Manage your potential deals</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('leads/kanban') ?>" class="btn btn-secondary">
                <i class="fa-solid fa-columns"></i> Kanban
            </a>
            <a href="<?= site_url('leads/create') ?>" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> New Lead
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Title & Company</th>
                    <th>Status</th>
                    <th>Value</th>
                    <th>Assigned To</th>
                    <th>Created</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                <tr>
                    <td colspan="6" class="text-center p-4">
                        <div class="text-muted mb-2">No leads found.</div>
                        <a href="<?= site_url('leads/create') ?>" class="btn btn-sm btn-primary">Create your first lead</a>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td>
                            <div class="font-bold text-dark"><?= esc($lead['title']) ?></div>
                            <?php if ($lead['company_name']): ?>
                                <div class="text-muted small">
                                    <i class="fa-regular fa-building mr-1"></i> <?= esc($lead['company_name']) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                                $statusClass = 'badge-secondary';
                                if ($lead['status'] === 'new') $statusClass = 'badge-info';
                                if ($lead['status'] === 'contacted') $statusClass = 'badge-warning';
                                if ($lead['status'] === 'qualified') $statusClass = 'badge-primary'; // Helper not defined, fallback
                                if ($lead['status'] === 'won') $statusClass = 'badge-success';
                                if ($lead['status'] === 'lost') $statusClass = 'badge-danger';
                            ?>
                            <span class="badge <?= $statusClass ?>">
                                <?= ucfirst($lead['status']) ?>
                            </span>
                        </td>
                        <td class="font-mono">
                            <?= esc($lead['currency_symbol'] ?? '$') ?><?= number_format($lead['value'], 2) ?>
                        </td>
                         <td>
                            <?php if ($lead['assigned_to']): ?>
                                <span class="badge badge-secondary"><i class="fa-solid fa-user small mr-1"></i> <?= esc($lead['assigned_to']) ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small">
                            <?= date('M d, Y', strtotime($lead['created_at'])) ?>
                        </td>
                        <td class="text-right">
                            <a href="<?= site_url('leads/' . $lead['id']) ?>" class="btn btn-sm btn-secondary">
                                View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
