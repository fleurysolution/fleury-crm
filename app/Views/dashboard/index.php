<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<!-- Stat Widgets -->
<div class="dashboard-widgets-grid mb-4">

    <div class="widget-card">
        <div class="widget-icon blue"><i class="fa-solid fa-users"></i></div>
        <div>
            <div class="widget-label">Total Users</div>
            <div class="widget-value"><?= $stats['total_users'] ?? 0 ?></div>
        </div>
    </div>

    <div class="widget-card">
        <div class="widget-icon purple"><i class="fa-solid fa-shield-halved"></i></div>
        <div>
            <div class="widget-label">Roles</div>
            <div class="widget-value"><?= $stats['total_roles'] ?? 0 ?></div>
        </div>
    </div>

    <div class="widget-card">
        <div class="widget-icon orange"><i class="fa-solid fa-filter"></i></div>
        <div>
            <div class="widget-label">New Leads</div>
            <div class="widget-value"><?= $stats['leads'] ?? 0 ?></div>
        </div>
    </div>

    <div class="widget-card">
        <div class="widget-icon green"><i class="fa-solid fa-layer-group"></i></div>
        <div>
            <div class="widget-label">Projects</div>
            <div class="widget-value"><?= $stats['projects'] ?? 0 ?></div>
        </div>
    </div>

</div>

<!-- Secondary Widgets (if available) -->
<div class="row g-4 mb-4">

    <!-- Recent Activity -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-timeline me-2 text-primary opacity-75"></i>Recent Activity</span>
            </div>
            <div class="card-body">
                <?php if (!empty($stats['recent_activity'])): ?>
                    <ul class="list-group list-group-flush">
                    <?php foreach($stats['recent_activity'] as $act): ?>
                        <li class="list-group-item d-flex align-items-center gap-3 px-0">
                            <div class="widget-icon cyan" style="width:34px;height:34px;border-radius:8px;font-size:.8rem;">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:.875rem;"><?= esc($act['text'] ?? '') ?></div>
                                <div class="text-muted" style="font-size:.75rem;"><?= esc($act['time'] ?? '') ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <i class="fa-solid fa-inbox fa-2x mb-3 opacity-25"></i>
                        <p class="mb-0 small">No recent activity to display.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fa-solid fa-bolt me-2 text-primary opacity-75"></i>Quick Actions
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="<?= site_url('team/create') ?>" class="btn btn-outline-primary btn-sm text-start">
                    <i class="fa-solid fa-user-plus me-2"></i>Add Team Member
                </a>
                <a href="<?= site_url('leads') ?>" class="btn btn-outline-secondary btn-sm text-start">
                    <i class="fa-solid fa-filter me-2"></i>View Leads
                </a>
                <a href="<?= site_url('clients') ?>" class="btn btn-outline-secondary btn-sm text-start">
                    <i class="fa-solid fa-building me-2"></i>View Clients
                </a>
                <a href="<?= site_url('invoices') ?>" class="btn btn-outline-secondary btn-sm text-start">
                    <i class="fa-solid fa-file-invoice me-2"></i>View Invoices
                </a>
                <a href="<?= site_url('settings/general') ?>" class="btn btn-outline-secondary btn-sm text-start mt-auto">
                    <i class="fa-solid fa-gear me-2"></i>System Settings
                </a>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
