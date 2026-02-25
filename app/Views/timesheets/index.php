<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-clock me-2 text-primary"></i>Timesheets</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active">Timesheets</li>
        </ol></nav>
    </div>
    <a href="<?= site_url('timesheets/create') ?>" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i>New Timesheet
    </a>
</div>

<!-- This week's quick stats -->
<?php
$thisWeek  = date('Y-m-d', strtotime('monday this week'));
$myThisWk  = null;
foreach ($timesheets as $ts) { if ($ts['week_start'] === $thisWeek) { $myThisWk = $ts; break; } }
$statusColors = ['draft'=>'secondary','submitted'=>'warning','approved'=>'success','rejected'=>'danger'];
?>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3" style="border-radius:10px;">
            <div class="text-muted small">This Week</div>
            <?php if ($myThisWk): ?>
            <div class="fw-bold fs-4"><?= number_format($myThisWk['total_hours'],1) ?>h</div>
            <span class="badge bg-<?= $statusColors[$myThisWk['status']] ?>-subtle text-<?= $statusColors[$myThisWk['status']] ?>">
                <?= ucfirst($myThisWk['status']) ?>
            </span>
            <?php else: ?>
            <div class="fw-bold fs-5 text-muted">—</div>
            <a href="<?= site_url('timesheets/create') ?>" class="btn btn-sm btn-outline-primary mt-1">Start</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3" style="border-radius:10px;">
            <div class="text-muted small">Total Logged</div>
            <div class="fw-bold fs-4"><?= number_format(array_sum(array_column($timesheets,'total_hours')),1) ?>h</div>
            <div class="text-muted" style="font-size:.75rem;">all time</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3" style="border-radius:10px;">
            <div class="text-muted small">Pending Approval</div>
            <div class="fw-bold fs-4"><?= count(array_filter($timesheets, fn($t) => $t['status']==='submitted')) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3" style="border-radius:10px;">
            <div class="text-muted small">Total Timesheets</div>
            <div class="fw-bold fs-4"><?= count($timesheets) ?></div>
        </div>
    </div>
</div>

<!-- Timesheet list -->
<div class="card border-0 shadow-sm" style="border-radius:10px;overflow:hidden;">
<?php if (empty($timesheets)): ?>
<div class="text-center py-5 text-muted">
    <i class="fa-solid fa-clock fa-2x mb-2 opacity-25 d-block"></i>
    No timesheets yet. <a href="<?= site_url('timesheets/create') ?>">Create your first one</a>.
</div>
<?php else: ?>
<table class="table table-hover align-middle mb-0 small">
    <thead class="table-light">
        <tr>
            <th>Week</th>
            <th>Hours</th>
            <th>Status</th>
            <th>Submitted</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($timesheets as $ts):
        $weekEnd  = date('d M Y', strtotime($ts['week_start'] . ' +6 days'));
        $weekStart = date('d M Y', strtotime($ts['week_start']));
    ?>
    <tr>
        <td>
            <a href="<?= site_url("timesheets/{$ts['id']}") ?>" class="fw-semibold text-decoration-none text-dark">
                <?= $weekStart ?> – <?= $weekEnd ?>
            </a>
        </td>
        <td><span class="fw-bold"><?= number_format($ts['total_hours'],1) ?>h</span></td>
        <td><span class="badge bg-<?= $statusColors[$ts['status']] ?>-subtle text-<?= $statusColors[$ts['status']] ?>"><?= ucfirst($ts['status']) ?></span></td>
        <td class="text-muted"><?= $ts['submitted_at'] ? date('d M H:i', strtotime($ts['submitted_at'])) : '—' ?></td>
        <td class="text-end">
            <a href="<?= site_url("timesheets/{$ts['id']}") ?>" class="btn btn-sm btn-outline-primary">
                <?= $ts['status'] === 'draft' ? 'Fill' : 'View' ?>
            </a>
            <?php if ($ts['status'] === 'draft'): ?>
            <button class="btn btn-sm btn-outline-success" onclick="submitTimesheet(<?= $ts['id'] ?>)">
                Submit
            </button>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
</div>

<script>
function submitTimesheet(id) {
    if (!confirm('Submit timesheet for approval?')) return;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fetch(`/staging/public/timesheets/${id}/submit`, { method:'POST', body: fd })
        .then(() => location.reload());
}
</script>

<?= $this->endSection() ?>
