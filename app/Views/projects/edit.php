<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit Project</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('projects') ?>" class="text-decoration-none">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('projects/' . $project['id']) ?>" class="text-decoration-none"><?= esc($project['title']) ?></a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>

<div class="row justify-content-center">
<div class="col-xl-8">
<div class="card border-0 shadow-sm" style="border-radius:14px;">
<div class="card-body p-4">

<form method="post" action="<?= site_url('projects/' . $project['id'] . '/update') ?>">
<?= csrf_field() ?>

<div class="row g-3">
    <!-- Title -->
    <div class="col-12">
        <label class="form-label fw-semibold">Project Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" required value="<?= esc($project['title']) ?>">
    </div>

    <!-- Client + PM -->
    <div class="col-md-6">
        <label class="form-label fw-semibold">Client</label>
        <select name="client_id" class="form-select">
            <option value="">— No client —</option>
            <?php foreach ($clients as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($project['client_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
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
            <option value="<?= $u['id'] ?>" <?= ($project['pm_user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                <?= esc($u['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Status + Priority -->
    <div class="col-md-4">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select">
            <?php foreach (['draft'=>'Draft','active'=>'Active','on_hold'=>'On Hold','completed'=>'Completed','archived'=>'Archived'] as $k=>$v): ?>
            <option value="<?= $k ?>" <?= ($project['status'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Priority</label>
        <select name="priority" class="form-select">
            <?php foreach (['low'=>'Low','medium'=>'Medium','high'=>'High','urgent'=>'Urgent'] as $k=>$v): ?>
            <option value="<?= $k ?>" <?= ($project['priority'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Currency</label>
        <select name="currency" class="form-select">
            <?php foreach (['USD','EUR','GBP','INR','AED','SAR'] as $cur): ?>
            <option value="<?= $cur ?>" <?= ($project['currency'] ?? 'USD') === $cur ? 'selected' : '' ?>><?= $cur ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Dates + Budget -->
    <div class="col-md-4">
        <label class="form-label fw-semibold">Start Date</label>
        <input type="date" name="start_date" class="form-control" value="<?= esc($project['start_date'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">End Date</label>
        <input type="date" name="end_date" class="form-control" value="<?= esc($project['end_date'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Budget</label>
        <input type="number" name="budget" class="form-control" placeholder="0.00" min="0" step="0.01" value="<?= esc($project['budget'] ?? '') ?>">
    </div>

    <!-- Color label -->
    <div class="col-md-3">
        <label class="form-label fw-semibold">Project Color</label>
        <input type="color" name="color" class="form-control form-control-color w-100" value="<?= esc($project['color'] ?? '#4a90e2') ?>">
    </div>

    <!-- Description -->
    <div class="col-12">
        <label class="form-label fw-semibold">Description</label>
        <textarea name="description" class="form-control" rows="4"><?= esc($project['description'] ?? '') ?></textarea>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="<?= site_url('projects/' . $project['id']) ?>" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-check me-1"></i> Save Changes
    </button>
</div>

</form>
</div>
</div>
</div>
</div>

<?= $this->endSection() ?>
