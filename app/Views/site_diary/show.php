<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= site_url("projects/{$project['id']}?tab=site_diary") ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h1 class="h3 mb-0 fw-bold text-dark">Site Diary: <?= date('d M Y', strtotime($diary['entry_date'])) ?></h1>
        </div>
        <div class="d-flex gap-2">
            <?php if ($diary['status'] === 'draft'): ?>
            <form action="<?= site_url("projects/{$project['id']}/site-diary/{$diary['id']}/submit") ?>" method="POST">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-warning">Submit for Approval</button>
            </form>
            <?php elseif ($diary['status'] === 'submitted'): ?>
            <form action="<?= site_url("projects/{$project['id']}/site-diary/{$diary['id']}/approve") ?>" method="POST">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-success">Approve Diary</button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-4">
                        <h5 class="fw-bold">General Info</h5>
                        <span class="badge bg-<?= $diary['status']==='approved'?'success':'warning' ?>-subtle text-<?= $diary['status']==='approved'?'success':'warning' ?> px-3 py-2">
                            <?= strtoupper($diary['status']) ?>
                        </span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="small text-muted d-block">Weather</label>
                            <input type="text" id="weather" class="form-control border-0 bg-light fw-semibold" value="<?= esc($diary['weather']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted d-block">Temperature</label>
                            <input type="text" id="temp" class="form-control border-0 bg-light fw-semibold" value="<?= esc($diary['temperature']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted d-block">Manpower</label>
                            <input type="number" id="manpower" class="form-control border-0 bg-light fw-semibold" value="<?= $diary['manpower_count'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted d-block">Hours Worked</label>
                            <input type="number" step="0.5" id="hours" class="form-control border-0 bg-light fw-semibold" value="<?= $diary['working_hours'] ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3 align-items-center">
                        <h5 class="fw-bold mb-0">Progress & Notes</h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="addRow()">Add Row</button>
                    </div>
                    <div id="itemsContainer">
                        <?php foreach ($items as $it): ?>
                        <div class="row g-2 mb-2 item-row">
                            <div class="col-md-2">
                                <select name="item_type[]" class="form-select form-select-sm border-0 bg-light fw-semibold">
                                    <option value="progress" <?= $it['type']==='progress'?'selected':'' ?>>Progress</option>
                                    <option value="observation" <?= $it['type']==='observation'?'selected':'' ?>>Note</option>
                                    <option value="delay" <?= $it['type']==='delay'?'selected':'' ?>>Delay</option>
                                    <option value="delivery" <?= $it['type']==='delivery'?'selected':'' ?>>Delivery</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <input type="text" name="item_description[]" class="form-control form-control-sm border-0 bg-light" value="<?= esc($it['description']) ?>">
                            </div>
                            <div class="col-md-2">
                                <select name="item_area_id[]" class="form-select form-select-sm border-0 bg-light">
                                    <option value="">Area…</option>
                                    <?php foreach ($areas as $a): ?>
                                    <option value="<?= $a['id'] ?>" <?= $it['area_id']===$a['id']?'selected':'' ?>><?= esc($a['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-sm text-danger mt-1 p-0" onclick="this.closest('.item-row').remove()"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">General Notes</h5>
                    <textarea id="notes" class="form-control border-0 bg-light" rows="4"><?= esc($diary['notes']) ?></textarea>
                </div>
            </div>
            
            <div class="text-end mb-5">
                <button type="button" class="btn btn-primary px-5 fw-bold shadow-sm" onclick="saveDiary()">Save Changes</button>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Logs & Metadata</h6>
                    <div class="small text-muted mb-2">
                        <i class="fa-solid fa-user me-2"></i>Created by: <span class="text-dark fw-semibold"><?= esc($diary['creator_name'] ?? 'System') ?></span>
                    </div>
                    <div class="small text-muted mb-2">
                        <i class="fa-solid fa-calendar me-2"></i>Created on: <?= date('d M Y, H:i', strtotime($diary['created_at'])) ?>
                    </div>
                    <?php if ($diary['approved_by']): ?>
                    <hr>
                    <div class="small text-muted mb-2">
                        <i class="fa-solid fa-check-circle text-success me-2"></i>Approved by: <span class="text-dark fw-semibold">User #<?= $diary['approved_by'] ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="templateRow">
    <div class="row g-2 mb-2 item-row">
        <div class="col-md-2">
            <select name="item_type[]" class="form-select form-select-sm border-0 bg-light fw-semibold">
                <option value="progress">Progress</option>
                <option value="observation">Note</option>
                <option value="delay">Delay</option>
                <option value="delivery">Delivery</option>
            </select>
        </div>
        <div class="col-md-7">
            <input type="text" name="item_description[]" class="form-control form-control-sm border-0 bg-light" placeholder="Describe progress…">
        </div>
        <div class="col-md-2">
            <select name="item_area_id[]" class="form-select form-select-sm border-0 bg-light">
                <option value="">Area…</option>
                <?php foreach ($areas as $a): ?>
                <option value="<?= $a['id'] ?>"><?= esc($a['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-1 text-end">
             <button type="button" class="btn btn-sm text-danger mt-1 p-0" onclick="this.closest('.item-row').remove()"><i class="fa-solid fa-xmark"></i></button>
        </div>
    </div>
</template>

<script>
const CSRF_NAME = '<?= csrf_token() ?>';
const CSRF_TOKEN = '<?= csrf_hash() ?>';

function addRow() {
    const t = document.getElementById('templateRow');
    const clone = t.content.cloneNode(true);
    document.getElementById('itemsContainer').appendChild(clone);
}

function saveDiary() {
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('weather', document.getElementById('weather').value);
    fd.append('temperature', document.getElementById('temp').value);
    fd.append('manpower_count', document.getElementById('manpower').value);
    fd.append('working_hours', document.getElementById('hours').value);
    fd.append('notes', document.getElementById('notes').value);

    // Collect line items
    const types = document.getElementsByName('item_type[]');
    const descs = document.getElementsByName('item_description[]');
    const areas = document.getElementsByName('item_area_id[]');

    for(let i=0; i<descs.length; i++) {
        fd.append('item_type[]', types[i].value);
        fd.append('item_description[]', descs[i].value);
        fd.append('item_area_id[]', areas[i].value);
    }

    fetch('<?= site_url("projects/{$project['id']}/site-diary/{$diary['id']}/update") ?>', {
        method: 'POST',
        body: fd
    }).then(r => r.json()).then(d => {
        if(d.success) location.reload();
        else alert('Error saving diary.');
    });
}
</script>
<?= $this->endSection() ?>
