<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-database text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Database Backup</h5>
        <small class="text-muted">Create and download database snapshots</small>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 bg-light" style="border-radius:12px;">
            <div class="card-body p-4 text-center">
                <i class="fa-solid fa-cloud-arrow-down fa-3x text-primary mb-3 opacity-75"></i>
                <h6 class="fw-bold mb-1">Create Backup Now</h6>
                <p class="text-muted small mb-3">Generate a fresh SQL dump of your entire database and download it immediately.</p>
                <a href="<?= site_url('settings/db_backup/download') ?>"
                   class="btn btn-save">
                    <i class="fa-solid fa-download me-2"></i>Download SQL Backup
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 bg-light" style="border-radius:12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="fa-solid fa-circle-info text-primary me-2"></i>Backup Info</h6>
                <ul class="list-unstyled mb-0 small text-muted">
                    <li class="mb-2"><i class="fa-solid fa-check-circle text-success me-2"></i>Full schema + data export</li>
                    <li class="mb-2"><i class="fa-solid fa-check-circle text-success me-2"></i>Compatible with MySQL/MariaDB</li>
                    <li class="mb-2"><i class="fa-solid fa-check-circle text-success me-2"></i>Compressed .sql.gz format</li>
                    <li><i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>Store backups in a secure location</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;}</style>
<?= $this->endSection() ?>
