<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-plus-circle me-2 text-primary"></i>New Project</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('projects') ?>" class="text-decoration-none">Projects</a></li>
            <li class="breadcrumb-item active">New</li>
        </ol></nav>
    </div>
</div>

<div class="row justify-content-center">
<div class="col-xl-8">
<div class="card border-0 shadow-sm" style="border-radius:14px;">
<div class="card-body p-4">

<form method="post" action="<?= site_url('projects/store') ?>">
<?= csrf_field() ?>

<div class="row g-3">
    <!-- Title -->
    <div class="col-12">
        <label class="form-label fw-semibold">Project Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" placeholder="e.g. Office Fit-Out Block A" required value="<?= old('title') ?>">
    </div>

    <!-- Client + PM -->
    <div class="col-md-6">
        <label class="form-label fw-semibold">Client</label>
        <select name="client_id" class="form-select">
            <option value="">— No client —</option>
            <?php foreach ($clients as $c): ?>
            <option value="<?= $c['id'] ?>" <?= old('client_id') == $c['id'] ? 'selected' : '' ?>>
                <?= esc($c['company_name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Project Manager</label>
        <select name="pm_user_id" class="form-select">
            <option value="">— Unassigned —</option>
            <?php foreach ($users as $u): ?>
            <option value="<?= $u['id'] ?>" <?= old('pm_user_id') == $u['id'] ? 'selected' : '' ?>>
                <?= esc($u['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Status + Priority -->
    <div class="col-md-4">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select">
            <option value="draft" <?= old('status','draft')==='draft'?'selected':'' ?>>Draft</option>
            <option value="active" <?= old('status')==='active'?'selected':'' ?>>Active</option>
            <option value="on_hold" <?= old('status')==='on_hold'?'selected':'' ?>>On Hold</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Priority</label>
        <select name="priority" class="form-select">
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Currency</label>
        <select name="currency" class="form-select">
            <option value="USD">USD</option>
            <option value="EUR">EUR</option>
            <option value="GBP">GBP</option>
            <option value="INR">INR</option>
            <option value="AED">AED</option>
            <option value="SAR">SAR</option>
        </select>
    </div>

    <!-- Dates + Budget -->
    <div class="col-md-4">
        <label class="form-label fw-semibold">Start Date</label>
        <input type="date" name="start_date" class="form-control" value="<?= old('start_date') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">End Date</label>
        <input type="date" name="end_date" class="form-control" value="<?= old('end_date') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Budget</label>
        <input type="number" name="budget" class="form-control" placeholder="0.00" min="0" step="0.01" value="<?= old('budget') ?>">
    </div>

    <!-- Color label -->
    <div class="col-md-3">
        <label class="form-label fw-semibold">Project Color</label>
        <input type="color" name="color" class="form-control form-control-color w-100" value="<?= old('color','#4a90e2') ?>">
    </div>

    <!-- Description -->
    <div class="col-12">
        <label class="form-label fw-semibold">Description</label>
        <textarea name="description" class="form-control" rows="4" placeholder="Brief project overview..."><?= old('description') ?></textarea>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="<?= site_url('projects') ?>" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-check me-1"></i> Create Project
    </button>
</div>

</form>
</div>
</div>
</div>
</div>

<?= $this->endSection() ?>
