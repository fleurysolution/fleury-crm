<?php
// app/Views/projects/tabs/drivers_inline.php
$driverModel = new \App\Models\ProjectDriverModel();
$drivers     = $driverModel->forProject($project['id']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h6 class="fw-bold mb-0">Quantity & Production Drivers</h6>
        <p class="text-muted small mb-0">Set project-wide metrics (ITT, Facility Area, Capacity) to drive quantity calculations.</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#driverModal" onclick="resetDriverModal()">
        <i class="fa-solid fa-plus me-1"></i>Add Driver Metric
    </button>
</div>

<!-- Mini metric cards for key drivers (example highlights) -->
<div class="row g-3 mb-4">
    <?php foreach (array_slice($drivers, 0, 4) as $d): ?>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center" style="border-radius:12px; background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);">
            <div class="text-muted small mb-1"><?= esc($d['name']) ?></div>
            <div class="fs-4 fw-bold text-primary"><?= number_format($d['value'], 0) ?> <span class="small opacity-50"><?= esc($d['unit']) ?></span></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card border-0 shadow-sm overflow-hidden" style="border-radius:15px;">
    <?php if (empty($drivers)): ?>
    <div class="text-center py-5">
        <i class="fa-solid fa-gauge-high fa-3x text-light mb-3"></i>
        <p class="text-muted">No drivers defined. Add metrics like 'Total kW' or 'Floor Area' to start the engine.</p>
    </div>
    <?php else: ?>
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-4">Metric / Driver Name</th>
                <th>Unit</th>
                <th>Value</th>
                <th>Description</th>
                <th class="text-end pe-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($drivers as $d): ?>
            <tr>
                <td class="ps-4 fw-bold"><?= esc($d['name']) ?></td>
                <td><span class="badge bg-secondary-subtle text-secondary border"><?= esc($d['unit']) ?></span></td>
                <td class="fw-bold text-primary fs-6"><?= number_format($d['value'], 2) ?></td>
                <td class="text-muted small"><?= esc($d['description']) ?></td>
                <td class="text-end pe-4">
                    <button class="btn btn-sm btn-light border px-2" onclick='editDriver(<?= json_encode($d) ?>)'>
                        <i class="fa-solid fa-pencil text-muted"></i>
                    </button>
                    <button class="btn btn-sm btn-light border px-2 ms-1" onclick="deleteDriver(<?= $d['id'] ?>)">
                        <i class="fa-solid fa-trash-can text-danger"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<div class="modal fade" id="driverModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
    <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="driverModalTitle">Metric Parameters</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body p-4">
        <input type="hidden" id="drvId">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold small">Driver Name <span class="text-danger">*</span></label>
                <input type="text" id="drvName" class="form-control" placeholder="e.g. Total IT Load, Building Footprint...">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Unit of Measure</label>
                <input type="text" id="drvUnit" class="form-control" placeholder="kW, SqFt, Cabinets...">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Metric Value</label>
                <input type="number" id="drvValue" class="form-control" step="0.0001" value="0">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold small">Description / Source</label>
                <textarea id="drvDesc" class="form-control" rows="2" placeholder="Where is this quantity derived from?"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer border-0 p-4 pt-0">
        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" onclick="saveDriver()">Update Driver</button>
    </div>
</div>
</div>
</div>

<script>
function resetDriverModal() {
    document.getElementById('drvId').value = '';
    document.getElementById('drvName').value = '';
    document.getElementById('drvUnit').value = '';
    document.getElementById('drvValue').value = '0';
    document.getElementById('drvDesc').value = '';
    document.getElementById('driverModalTitle').textContent = 'Create Driver Metric';
}

function editDriver(data) {
    document.getElementById('drvId').value      = data.id;
    document.getElementById('drvName').value    = data.name;
    document.getElementById('drvUnit').value    = data.unit;
    document.getElementById('drvValue').value   = data.value;
    document.getElementById('drvDesc').value    = data.description || '';
    document.getElementById('driverModalTitle').textContent = 'Update Metric Baseline';
    new bootstrap.Modal(document.getElementById('driverModal')).show();
}

function saveDriver() {
    const id = document.getElementById('drvId').value;
    const url = id 
        ? '<?= site_url("drivers") ?>/' + id + '/update' 
        : `<?= site_url("projects/{$project['id']}/drivers") ?>`;
    
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('name',        document.getElementById('drvName').value);
    fd.append('unit',        document.getElementById('drvUnit').value);
    fd.append('value',       document.getElementById('drvValue').value);
    fd.append('description', document.getElementById('drvDesc').value);

    fetch(url, { method:'POST', body: fd })
        .then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
}

function deleteDriver(id) {
    if(!confirm('Delete this driver metric? This may affect quantity formulas if implemented.')) return;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fetch('<?= site_url("drivers") ?>/' + id + '/delete', { method:'POST', body: fd })
        .then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
}
</script>
