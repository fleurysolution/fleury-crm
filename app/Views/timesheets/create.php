<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-plus-circle me-2 text-primary"></i>New Timesheet</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="<?= site_url('timesheets') ?>" class="text-decoration-none">Timesheets</a></li>
            <li class="breadcrumb-item active">New</li>
        </ol></nav>
    </div>
</div>

<div class="row justify-content-center">
<div class="col-md-6">
<div class="card border-0 shadow-sm" style="border-radius:14px;">
<div class="card-body p-4">

<form method="post" action="<?= site_url('timesheets/store') ?>">
<?= csrf_field() ?>

<div class="mb-3">
    <label class="form-label fw-semibold">Week Starting (picks closest Monday)</label>
    <input type="date" name="week_start" class="form-control" value="<?= date('Y-m-d', strtotime('monday this week')) ?>" required>
    <div class="form-text">Will be adjusted to the Monday of the selected week.</div>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Pre-select Projects</label>
    <p class="text-muted small mb-2">Choose which projects to pre-populate rows for (you can add more later).</p>
    <div class="row g-2">
    <?php foreach ($projects as $p): ?>
    <div class="col-md-6">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="project_ids[]" value="<?= $p['id'] ?>" id="proj-<?= $p['id'] ?>">
            <label class="form-check-label" for="proj-<?= $p['id'] ?>"><?= esc($p['title']) ?></label>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="<?= site_url('timesheets') ?>" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-check me-1"></i> Create Timesheet
    </button>
</div>
</form>

</div>
</div>
</div>
</div>

<?= $this->endSection() ?>
