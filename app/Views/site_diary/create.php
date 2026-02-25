<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="<?= site_url("projects/{$project['id']}?tab=site_diary") ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0 fw-bold text-dark">New Site Diary Entry</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="<?= site_url("projects/{$project['id']}/site-diary") ?>" method="POST" id="diaryForm">
                <?= csrf_field() ?>
                <input type="hidden" name="entry_date" value="<?= esc($date) ?>">

                <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <h5 class="fw-bold mb-1">General Information</h5>
                                <p class="text-muted small mb-0">Reporting for <strong><?= date('l, d M Y', strtotime($date)) ?></strong></p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2">DRAFT</span>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Weather</label>
                                <input type="text" name="weather" class="form-control" placeholder="e.g. Sunny, Light Rain">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Temperature</label>
                                <input type="text" name="temperature" class="form-control" placeholder="e.g. 28°C">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Manpower Count</label>
                                <input type="number" name="manpower_count" class="form-control" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Working Hours</label>
                                <input type="number" step="0.5" name="working_hours" class="form-control" value="8">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold mb-0">Work Progress & Observations</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()">
                                <i class="fa-solid fa-plus me-1"></i>Add Row
                            </button>
                        </div>
                        
                        <div id="itemsContainer">
                            <div class="row g-2 mb-2 item-row">
                                <div class="col-md-2">
                                    <select name="item_type[]" class="form-select form-select-sm">
                                        <option value="progress">Progress</option>
                                        <option value="observation">Note</option>
                                        <option value="delay">Delay</option>
                                        <option value="delivery">Delivery</option>
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" name="item_description[]" class="form-control form-control-sm" placeholder="Description of work performed…">
                                </div>
                                <div class="col-md-2">
                                    <select name="item_area_id[]" class="form-select form-select-sm">
                                        <option value="">Area…</option>
                                        <?php foreach ($areas as $a): ?>
                                        <option value="<?= $a['id'] ?>"><?= esc($a['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-sm btn-link text-danger p-0 mt-1" onclick="this.closest('.item-row').remove()">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">General Notes</h5>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Any additional site notes, safety issues, etc."></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-5">
                    <a href="<?= site_url("projects/{$project['id']}?tab=site_diary") ?>" class="btn btn-light px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5 fw-bold">Save Diary Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="rowTemplate">
    <div class="row g-2 mb-2 item-row">
        <div class="col-md-2">
            <select name="item_type[]" class="form-select form-select-sm">
                <option value="progress">Progress</option>
                <option value="observation">Note</option>
                <option value="delay">Delay</option>
                <option value="delivery">Delivery</option>
            </select>
        </div>
        <div class="col-md-7">
            <input type="text" name="item_description[]" class="form-control form-control-sm" placeholder="Description of work performed…">
        </div>
        <div class="col-md-2">
            <select name="item_area_id[]" class="form-select form-select-sm">
                <option value="">Area…</option>
                <?php foreach ($areas as $a): ?>
                <option value="<?= $a['id'] ?>"><?= esc($a['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-1 text-end">
            <button type="button" class="btn btn-sm btn-link text-danger p-0 mt-1" onclick="this.closest('.item-row').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
</template>

<script>
function addRow() {
    const template = document.getElementById('rowTemplate');
    const clone = template.content.cloneNode(true);
    document.getElementById('itemsContainer').appendChild(clone);
}
</script>
<?= $this->endSection() ?>
