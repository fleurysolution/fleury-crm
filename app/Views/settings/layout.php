<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="row">
    
    <!-- Settings Sidebar -->
    <div class="col-md-3">
        <div class="card mb-3">
             <div class="card-header fw-bold text-primary">System Settings</div>
             <div class="list-group list-group-flush">
                <a href="<?= site_url('settings/general') ?>" class="list-group-item list-group-item-action <?= $tab == 'general' ? 'active' : '' ?>">
                    <i class="fa-solid fa-building me-2 w-20 text-center"></i> General
                </a>
                <a href="<?= site_url('settings/email') ?>" class="list-group-item list-group-item-action <?= $tab == 'email' ? 'active' : '' ?>">
                    <i class="fa-solid fa-envelope me-2 w-20 text-center"></i> Email
                </a>
                <a href="<?= site_url('settings/modules') ?>" class="list-group-item list-group-item-action <?= $tab == 'modules' ? 'active' : '' ?>">
                    <i class="fa-solid fa-cubes me-2 w-20 text-center"></i> Modules
                </a>
                 <a href="<?= site_url('settings/cron_job') ?>" class="list-group-item list-group-item-action <?= $tab == 'cron_job' ? 'active' : '' ?>">
                    <i class="fa-solid fa-clock me-2 w-20 text-center"></i> Cron Job
                </a>
                 <a href="<?= site_url('settings/notifications') ?>" class="list-group-item list-group-item-action <?= $tab == 'notifications' ? 'active' : '' ?>">
                    <i class="fa-solid fa-bell me-2 w-20 text-center"></i> Notifications
                </a>
                <a href="<?= site_url('settings/integration') ?>" class="list-group-item list-group-item-action <?= $tab == 'integration' ? 'active' : '' ?>">
                    <i class="fa-solid fa-plug me-2 w-20 text-center"></i> Integration
                </a>
                 <a href="<?= site_url('settings/client_permissions') ?>" class="list-group-item list-group-item-action <?= $tab == 'client_permissions' ? 'active' : '' ?>">
                    <i class="fa-solid fa-user-lock me-2 w-20 text-center"></i> Client Permissions
                </a>
                 <a href="<?= site_url('settings/invoices') ?>" class="list-group-item list-group-item-action <?= $tab == 'invoices' ? 'active' : '' ?>">
                    <i class="fa-solid fa-file-invoice me-2 w-20 text-center"></i> Invoices
                </a>
                 <a href="<?= site_url('settings/events') ?>" class="list-group-item list-group-item-action <?= $tab == 'events' ? 'active' : '' ?>">
                    <i class="fa-solid fa-calendar me-2 w-20 text-center"></i> Events
                </a>
                 <a href="<?= site_url('settings/tickets') ?>" class="list-group-item list-group-item-action <?= $tab == 'tickets' ? 'active' : '' ?>">
                    <i class="fa-solid fa-ticket me-2 w-20 text-center"></i> Tickets
                </a>
                 <a href="<?= site_url('settings/tasks') ?>" class="list-group-item list-group-item-action <?= $tab == 'tasks' ? 'active' : '' ?>">
                    <i class="fa-solid fa-list-check me-2 w-20 text-center"></i> Tasks
                </a>
                <a href="<?= site_url('settings/ip_restriction') ?>" class="list-group-item list-group-item-action <?= $tab == 'ip_restriction' ? 'active' : '' ?>">
                    <i class="fa-solid fa-ban me-2 w-20 text-center"></i> IP Restriction
                </a>
                <a href="<?= site_url('settings/db_backup') ?>" class="list-group-item list-group-item-action <?= $tab == 'db_backup' ? 'active' : '' ?>">
                    <i class="fa-solid fa-database me-2 w-20 text-center"></i> Database Backup
                </a>
             </div>
        </div>
    </div>

    <!-- Settings Content -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <?= $this->renderSection('settings_content') ?>
            </div>
        </div>
    </div>
</div>

<style>
.w-20 { width: 20px; }
.list-group-item.active {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}
</style>

<?= $this->endSection() ?>
