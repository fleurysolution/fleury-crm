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
    <div class="col-md-3">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select">
            <?php foreach (['draft'=>'Draft','active'=>'Active','on_hold'=>'On Hold','completed'=>'Completed','archived'=>'Archived'] as $k=>$v): ?>
            <option value="<?= $k ?>" <?= ($project['status'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Project Stage</label>
        <select name="project_stage" class="form-select">
            <option value="bidding" <?= ($project['project_stage'] ?? '')==='bidding'?'selected':'' ?>>Bidding</option>
            <option value="pre_construction" <?= ($project['project_stage'] ?? '')==='pre_construction'?'selected':'' ?>>Pre-Construction</option>
            <option value="active" <?= ($project['project_stage'] ?? '')==='active'?'selected':'' ?>>Active</option>
            <option value="closeout" <?= ($project['project_stage'] ?? '')==='closeout'?'selected':'' ?>>Closeout</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Priority</label>
        <select name="priority" class="form-select">
            <?php foreach (['low'=>'Low','medium'=>'Medium','high'=>'High','urgent'=>'Urgent'] as $k=>$v): ?>
            <option value="<?= $k ?>" <?= ($project['priority'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Currency</label>
        <select name="currency" class="form-select">
            <?php foreach (['USD','EUR','GBP','INR','AED','SAR'] as $cur): ?>
            <option value="<?= $cur ?>" <?= ($project['currency'] ?? 'USD') === $cur ? 'selected' : '' ?>><?= $cur ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Contract + Commercial -->
    <div class="col-md-4">
        <label class="form-label fw-semibold">Contract Type</label>
        <select name="contract_type" class="form-select">
            <option value="lump_sum" <?= ($project['contract_type'] ?? '')==='lump_sum'?'selected':'' ?>>Lump Sum / Fixed Price</option>
            <option value="cost_plus" <?= ($project['contract_type'] ?? '')==='cost_plus'?'selected':'' ?>>Cost Plus</option>
            <option value="unit_price" <?= ($project['contract_type'] ?? '')==='unit_price'?'selected':'' ?>>Unit Price</option>
            <option value="time_materials" <?= ($project['contract_type'] ?? '')==='time_materials'?'selected':'' ?>>Time & Materials</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Original Budget Baseline</label>
        <input type="number" name="versioned_budget_baseline" class="form-control" placeholder="0.00" min="0" step="0.01" value="<?= esc($project['versioned_budget_baseline'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Current/Forecast Budget</label>
        <input type="number" name="budget" class="form-control" placeholder="0.00" min="0" step="0.01" value="<?= esc($project['budget'] ?? '') ?>">
    </div>

    <!-- Baseline Assumptions (Driver Engine) -->
    <div class="col-12 mt-4">
        <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="fa-solid fa-gauge-high me-2"></i>Project Baseline & Production Drivers</h6>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Sector / Project Type</label>
        <select name="sector" class="form-select">
            <?php foreach(['commercial'=>'Commercial','data_center'=>'Data Center','healthcare'=>'Healthcare','industrial'=>'Industrial','residential'=>'Residential','infrastructure'=>'Infrastructure'] as $k=>$v): ?>
            <option value="<?= $k ?>" <?= ($project['sector'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Gross SQFT</label>
        <input type="number" name="gross_sqft" class="form-control" value="<?= esc($project['gross_sqft'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Total Floors</label>
        <input type="number" name="total_floors" class="form-control" value="<?= esc($project['total_floors'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Site Acreage</label>
        <input type="number" step="0.01" name="site_acreage" class="form-control" value="<?= esc($project['site_acreage'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Estimated Duration (Months)</label>
        <input type="number" step="0.1" name="duration_months" class="form-control" value="<?= esc($project['duration_months'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Labor Productivity Factor</label>
        <input type="number" step="0.01" name="labor_productivity_factor" class="form-control" value="<?= esc($project['labor_productivity_factor'] ?? '1.00') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Technical Standard</label>
        <select name="standard_owner_id" class="form-select">
            <option value="">— Standard Company Logic —</option>
            <option value="1" <?= ($project['standard_owner_id'] ?? '') == '1' ? 'selected' : '' ?>>Owner Standard A (Enterprise)</option>
            <option value="2" <?= ($project['standard_owner_id'] ?? '') == '2' ? 'selected' : '' ?>>Owner Standard B (Colocation)</option>
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


    <!-- Color label -->
    <div class="col-md-3">
        <label class="form-label fw-semibold">Project Color</label>
        <input type="color" name="color" class="form-control form-control-color w-100" value="<?= esc($project['color'] ?? '#4a90e2') ?>">
    </div>

    <!-- Geofencing -->
    <div class="col-12 mt-4">
        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-solid fa-location-dot me-2"></i>Site Location & Geofencing</h6>
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">Latitude</label>
        <input type="text" name="latitude" class="form-control" placeholder="e.g. 40.7128" value="<?= esc($project['latitude'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">Longitude</label>
        <input type="text" name="longitude" class="form-control" placeholder="e.g. -74.0060" value="<?= esc($project['longitude'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">Radius (Meters)</label>
        <input type="number" name="geofence_radius" class="form-control" value="<?= esc($project['geofence_radius'] ?? '100') ?>">
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
