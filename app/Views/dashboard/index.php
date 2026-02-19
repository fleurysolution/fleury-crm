<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="dashboard-widgets-grid">
    <!-- Widget: Total Users -->
    <div class="card widget-card">
        <div class="widget-icon blue">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <div class="widget-label">Total Users</div>
            <div class="widget-value"><?= $stats['total_users'] ?></div>
        </div>
    </div>

    <!-- Widget: Active Roles -->
    <div class="card widget-card">
        <div class="widget-icon green">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div>
            <div class="widget-label">Roles</div>
            <div class="widget-value"><?= $stats['total_roles'] ?></div>
        </div>
    </div>

    <!-- Widget: Pending Leads (Placeholder) -->
    <div class="card widget-card">
        <div class="widget-icon orange">
            <i class="fa-solid fa-filter"></i>
        </div>
        <div>
            <div class="widget-label">New Leads</div>
            <div class="widget-value"><?= $stats['leads'] ?></div>
        </div>
    </div>

     <!-- Widget: Active Projects (Placeholder) -->
    <div class="card widget-card">
        <div class="widget-icon purple">
            <i class="fa-solid fa-layer-group"></i>
        </div>
        <div>
            <div class="widget-label">Projects</div>
            <div class="widget-value"><?= $stats['projects'] ?></div>
        </div>
    </div>
</div>

<div class="dashboard-sections-grid">
    <!-- Recent Activity -->
    <div class="card">
        <h4 style="margin-bottom: 1rem; color: var(--primary-color);">Recent Activity</h4>
        <div style="color: var(--text-muted); font-style: italic;">
            No recent activity to display.
        </div>
    </div>

    <!-- Quick Links -->
    <div class="card">
        <h4 style="margin-bottom: 1rem; color: var(--primary-color);">Quick Actions</h4>
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <a href="<?= site_url('team/create') ?>" class="btn btn-outline" style="text-align: left; justify-content: flex-start;">
                <i class="fa-solid fa-user-plus" style="margin-right: 0.5rem;"></i> Add Team Member
            </a>
            <a href="<?= site_url('settings') ?>" class="btn btn-outline" style="text-align: left; justify-content: flex-start;">
                <i class="fa-solid fa-cog" style="margin-right: 0.5rem;"></i> System Settings
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
