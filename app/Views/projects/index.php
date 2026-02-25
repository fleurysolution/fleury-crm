<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-layer-group me-2 text-primary"></i>Projects</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active">Projects</li>
        </ol></nav>
    </div>
    <a href="<?= site_url('projects/create') ?>" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> New Project
    </a>
</div>

<!-- Status filter tabs -->
<div class="d-flex gap-2 flex-wrap mb-4">
    <?php
    $labels = ['all'=>'All','draft'=>'Draft','active'=>'Active','on_hold'=>'On Hold','completed'=>'Completed','archived'=>'Archived'];
    foreach ($labels as $s => $l):
        $active = ($filter === $s) ? 'btn-primary' : 'btn-outline-secondary';
        $cnt    = $s === 'all' ? array_sum($counts) : ($counts[$s] ?? 0);
    ?>
    <a href="<?= site_url('projects?status='.$s) ?>" class="btn btn-sm <?= $active ?>">
        <?= $l ?> <span class="badge bg-white text-dark ms-1"><?= $cnt ?></span>
    </a>
    <?php endforeach; ?>
</div>

<?php if (empty($projects)): ?>
<div class="card border-0 shadow-sm text-center py-5">
    <div class="card-body">
        <i class="fa-solid fa-folder-open fa-3x text-muted mb-3 d-block opacity-25"></i>
        <h6 class="text-muted">No projects yet</h6>
        <a href="<?= site_url('projects/create') ?>" class="btn btn-primary mt-2">
            <i class="fa-solid fa-plus me-1"></i> Create your first project
        </a>
    </div>
</div>
<?php else: ?>
<div class="row g-3">
<?php foreach ($projects as $p):
    $statusBadge = [
        'draft'=>'secondary','active'=>'success','on_hold'=>'warning',
        'completed'=>'primary','archived'=>'dark'
    ][$p['status']] ?? 'secondary';
    $pct = 0; // populated dynamically
?>
<div class="col-xl-4 col-lg-6">
    <div class="card border-0 shadow-sm h-100 project-card" style="border-top: 3px solid <?= esc($p['color']) ?> !important; border-radius: 12px;">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <h6 class="fw-bold mb-0">
                    <a href="<?= site_url('projects/'.$p['id']) ?>" class="text-decoration-none text-dark">
                        <?= esc($p['title']) ?>
                    </a>
                </h6>
                <span class="badge bg-<?= $statusBadge ?>-subtle text-<?= $statusBadge ?> ms-2">
                    <?= ucfirst(str_replace('_',' ',$p['status'])) ?>
                </span>
            </div>

            <?php if ($p['client_name']): ?>
            <div class="text-muted small mb-2">
                <i class="fa-solid fa-building me-1"></i><?= esc($p['client_name']) ?>
            </div>
            <?php endif; ?>

            <?php if ($p['description']): ?>
            <p class="small text-muted mb-3" style="line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                <?= esc($p['description']) ?>
            </p>
            <?php endif; ?>

            <div class="d-flex gap-3 small text-muted mb-3">
                <?php if ($p['start_date']): ?>
                <span><i class="fa-regular fa-calendar me-1"></i><?= date('d M Y', strtotime($p['start_date'])) ?></span>
                <?php endif; ?>
                <?php if ($p['end_date']): ?>
                <span><i class="fa-solid fa-flag-checkered me-1"></i><?= date('d M Y', strtotime($p['end_date'])) ?></span>
                <?php endif; ?>
            </div>

            <div class="d-flex align-items-center justify-content-between">
                <?php if ($p['pm_name']): ?>
                <div class="d-flex align-items-center gap-2">
                    <div class="user-avatar" style="width:28px;height:28px;font-size:.7rem;" title="<?= esc($p['pm_name']) ?>">
                        <?= strtoupper(substr($p['pm_name'],0,1)) ?>
                    </div>
                    <span class="small text-muted"><?= esc($p['pm_name']) ?></span>
                </div>
                <?php else: ?><div></div><?php endif; ?>
                <a href="<?= site_url('projects/'.$p['id']) ?>" class="btn btn-sm btn-outline-primary">
                    Open <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
