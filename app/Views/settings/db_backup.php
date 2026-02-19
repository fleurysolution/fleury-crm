<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">Database Backup</h4>

<div class="card border-info mb-3">
    <div class="card-body">
         <p class="card-text">
            You can download the database backup from here. For security reasons, please delete the backup file after downloading.
        </p>
         <button class="btn btn-primary"><i class="fa-solid fa-download me-2"></i> Download Backup</button>
         <span class="text-muted ms-2">(Feature coming soon)</span>
    </div>
</div>

<?= $this->endSection() ?>
