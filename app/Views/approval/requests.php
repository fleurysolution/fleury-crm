<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1"><?= esc(t('approval_requests_title')) ?></h4>
        <p class="text-muted mb-0"><?= esc(t('approval_requests_subtitle')) ?></p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table id="approvalRequestsTable" class="table table-hover align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th><?= esc(t('request_key')) ?></th>
                    <th><?= esc(t('module')) ?></th>
                    <th><?= esc(t('entity')) ?></th>
                    <th><?= esc(t('status')) ?></th>
                    <th><?= esc(t('current_step')) ?></th>
                    <th><?= esc(t('action')) ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($requests)): ?>
                    <?php foreach ($requests as $r): ?>
                        <?php
                            $status = $r['status'] ?? 'pending';
                            $badgeClass = match ($status) {
                                'approved' => 'bg-success-subtle text-success',
                                'rejected' => 'bg-danger-subtle text-danger',
                                'cancelled' => 'bg-secondary-subtle text-secondary',
                                default => 'bg-warning-subtle text-warning'
                            };
                        ?>
                        <tr>
                            <td><?= (int)$r['id'] ?></td>
                            <td><?= esc($r['request_key']) ?></td>
                            <td><?= esc($r['module_key']) ?></td>
                            <td><?= esc($r['entity_type']) ?> #<?= esc($r['entity_id']) ?></td>
                            <td><span class="badge rounded-pill <?= $badgeClass ?>"><?= esc(ucfirst($status)) ?></span></td>
                            <td><?= (int)$r['current_step_no'] ?></td>
                            <td><a class="btn btn-sm btn-outline-primary" href="<?= site_url('approval/requests/' . $r['id']) ?>"><?= esc(t('view')) ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7"><?= esc(t('no_requests_found')) ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $pageScript = 'approval.js'; ?>
<?= $this->endSection() ?>
