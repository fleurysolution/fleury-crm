<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?php
/**
 * $project
 * $diary
 * $laborLines
 */
$isEditable = ($diary['status'] === 'Draft');
?>

<div class="mb-3">
    <a href="<?= site_url("projects/{$project['id']}?tab=field") ?>" class="text-decoration-none text-muted">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Field App
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Daily Site Log</h3>
        <div class="d-flex gap-3 text-muted small align-items-center">
            <span>Project: <strong class="text-dark"><?= esc($project['title']) ?></strong></span>
            <span>Date: <strong class="text-dark"><?= date('F d, Y', strtotime($diary['report_date'])) ?></strong></span>
            <span class="badge bg-<?= $diary['status']==='Approved'?'success':($diary['status']==='Submitted'?'primary':'secondary') ?>">
                <?= esc($diary['status']) ?>
            </span>
        </div>
    </div>
</div>

<form action="<?= site_url("field/diary/{$diary['id']}/save") ?>" method="POST" id="diaryForm">
    <?= csrf_field() ?>
    <input type="hidden" name="status_action" id="statusAction" value="save">

    <div class="row g-4">
        <!-- Site Conditions & Text Logs -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0">Site Conditions & Observations</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Weather</label>
                            <input type="text" name="weather_conditions" class="form-control" value="<?= esc($diary['weather_conditions']) ?>" placeholder="e.g. Sunny, Overcast, Rain" <?= !$isEditable ? 'disabled' : '' ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Temperature</label>
                            <input type="text" name="temperature" class="form-control" value="<?= esc($diary['temperature']) ?>" placeholder="e.g. 75°F" <?= !$isEditable ? 'disabled' : '' ?>>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-primary">Work Performed Today</label>
                        <textarea name="work_performed" class="form-control" rows="4" placeholder="Describe the day's activities, areas worked in, and general progress..." <?= !$isEditable ? 'disabled' : '' ?>><?= esc($diary['work_performed']) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-info">Material Deliveries</label>
                        <textarea name="materials_received" class="form-control" rows="3" placeholder="List any materials delivered to site..." <?= !$isEditable ? 'disabled' : '' ?>><?= esc($diary['materials_received']) ?></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-danger">Safety Observations / Incidents</label>
                        <textarea name="safety_observations" class="form-control" rows="3" placeholder="Log any safety hazards, toolbox talks, or incidents..." <?= !$isEditable ? 'disabled' : '' ?>><?= esc($diary['safety_observations']) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Labor & Equipment Grid -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Labor & Subcontractors</h5>
                    <?php if ($isEditable): ?>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addLaborRow()">
                            <i class="fa-solid fa-plus"></i> Add Row
                        </button>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="laborTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3" style="width: 50%;">Trade / Company</th>
                                <th style="width: 20%;">Count</th>
                                <th style="width: 20%;">Hours</th>
                                <?php if ($isEditable): ?><th style="width: 10%;"></th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($laborLines) && $isEditable): ?>
                                <!-- Empty default row -->
                                <tr>
                                    <td class="ps-3 p-1"><input type="text" name="trades[]" class="form-control form-control-sm" placeholder="e.g. Acme Plumbing"></td>
                                    <td class="p-1"><input type="number" name="worker_counts[]" class="form-control form-control-sm text-center" value="1" min="1"></td>
                                    <td class="p-1"><input type="number" step="0.5" name="hours_worked[]" class="form-control form-control-sm text-center" placeholder="e.g. 8"></td>
                                    <td class="p-1 text-center"><button type="button" class="btn btn-sm text-danger border-0" onclick="this.closest('tr').remove()"><i class="fa-solid fa-xmark"></i></button></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($laborLines as $labor): ?>
                                    <tr>
                                        <td class="ps-3 p-1">
                                            <?php if ($isEditable): ?>
                                                <input type="text" name="trades[]" class="form-control form-control-sm" value="<?= esc($labor['trade_or_company']) ?>">
                                            <?php else: ?>
                                                <div class="fw-medium text-dark px-2 py-1"><?= esc($labor['trade_or_company']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-1">
                                            <?php if ($isEditable): ?>
                                                <input type="number" name="worker_counts[]" class="form-control form-control-sm text-center" value="<?= esc($labor['worker_count']) ?>" min="1">
                                            <?php else: ?>
                                                <div class="text-center px-2 py-1"><?= esc($labor['worker_count']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-1">
                                            <?php if ($isEditable): ?>
                                                <input type="number" step="0.5" name="hours_worked[]" class="form-control form-control-sm text-center" value="<?= esc($labor['hours_worked']) ?>">
                                            <?php else: ?>
                                                <div class="text-center px-2 py-1"><?= esc($labor['hours_worked']) ?: '-' ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($isEditable): ?>
                                            <td class="p-1 text-center"><button type="button" class="btn btn-sm text-danger border-0" onclick="this.closest('tr').remove()"><i class="fa-solid fa-xmark"></i></button></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card bg-light border-0">
                <div class="card-body">
                    <?php if ($isEditable): ?>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary" onclick="submitDiary('save')">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Save Draft Progress
                            </button>
                            <button type="button" class="btn btn-success" onclick="submitDiary('submit')">
                                <i class="fa-solid fa-lock me-2"></i> Finalize & Sign Record
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success border-0 mb-0">
                            <i class="fa-solid fa-lock me-2"></i> This daily record has been finalized and locked.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>

<?php if ($isEditable): ?>
<script>
    function addLaborRow() {
        const tbody = document.querySelector('#laborTable tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="ps-3 p-1"><input type="text" name="trades[]" class="form-control form-control-sm" placeholder="e.g. Electricians"></td>
            <td class="p-1"><input type="number" name="worker_counts[]" class="form-control form-control-sm text-center" value="1" min="1"></td>
            <td class="p-1"><input type="number" step="0.5" name="hours_worked[]" class="form-control form-control-sm text-center" placeholder="e.g. 8"></td>
            <td class="p-1 text-center"><button type="button" class="btn btn-sm text-danger border-0" onclick="this.closest('tr').remove()"><i class="fa-solid fa-xmark"></i></button></td>
        `;
        tbody.appendChild(tr);
    }

    function submitDiary(action) {
        if(action === 'submit') {
            if(!confirm('Are you certain you want to finalize this daily log? This will lock it from future edits.')) return;
        }
        document.getElementById('statusAction').value = action;
        document.getElementById('diaryForm').submit();
    }
</script>
<?php endif; ?>

<?= $this->endSection() ?>
