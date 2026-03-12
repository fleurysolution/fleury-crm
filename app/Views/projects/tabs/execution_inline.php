<?php
// app/Views/projects/tabs/execution_inline.php
$staffModel = new \App\Models\ProjectStaffingModel();
$equipModel = new \App\Models\ProjectEquipmentModel();

$staffPlan = $staffModel->forProject($project['id']);
$equipPlan = $equipModel->forProject($project['id']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h6 class="fw-bold mb-0">Execution Setup & Resource Planning</h6>
        <p class="text-muted small mb-0">Define baseline staffing and equipment requirements for project execution.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#staffModal" onclick="resetStaffModal()">
            <i class="fa-solid fa-users-gear me-1"></i>Plan Staffing
        </button>
        <button class="btn btn-outline-info btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#equipModal" onclick="resetEquipModal()">
            <i class="fa-solid fa-truck-pickup me-1"></i>Plan Equipment
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Staffing Plan -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius:15px; overflow:hidden;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="fa-solid fa-people-group text-primary me-2"></i>
                <h6 class="mb-0 fw-bold">Manpower / Staffing Baseline</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Role / Trade</th>
                            <th>Qty</th>
                            <th>Timeline</th>
                            <th class="text-end pe-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($staffPlan)): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">No staffing plan defined.</td></tr>
                        <?php else: foreach ($staffPlan as $s): ?>
                        <tr>
                            <td class="ps-3 fw-semibold"><?= esc($s['role_or_trade']) ?></td>
                            <td class="fw-bold text-primary"><?= (int)$s['planned_count'] ?></td>
                            <td class="text-muted" style="font-size: 10px;">
                                <?= $s['start_date'] ? date('M y', strtotime($s['start_date'])) : '—' ?>
                                → <?= $s['end_date'] ? date('M y', strtotime($s['end_date'])) : '—' ?>
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-xs btn-light border" onclick='editStaff(<?= json_encode($s) ?>)'><i class="fa-solid fa-pencil"></i></button>
                                <button class="btn btn-xs btn-light border ms-1" onclick="deleteStaff(<?= $s['id'] ?>)"><i class="fa-solid fa-trash-can text-danger"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Equipment Plan -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius:15px; overflow:hidden;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="fa-solid fa-tractor text-info me-2"></i>
                <h6 class="mb-0 fw-bold">Equipment Baseline</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Equipment Type</th>
                            <th>Qty</th>
                            <th>Timeline</th>
                            <th class="text-end pe-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($equipPlan)): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">No equipment plan defined.</td></tr>
                        <?php else: foreach ($equipPlan as $e): ?>
                        <tr>
                            <td class="ps-3 fw-semibold"><?= esc($e['equipment_type']) ?></td>
                            <td class="fw-bold text-info"><?= (int)$e['planned_count'] ?></td>
                            <td class="text-muted" style="font-size: 10px;">
                                <?= $e['start_date'] ? date('M y', strtotime($e['start_date'])) : '—' ?>
                                → <?= $e['end_date'] ? date('M y', strtotime($e['end_date'])) : '—' ?>
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-xs btn-light border" onclick='editEquip(<?= json_encode($e) ?>)'><i class="fa-solid fa-pencil"></i></button>
                                <button class="btn btn-xs btn-light border ms-1" onclick="deleteEquip(<?= $e['id'] ?>)"><i class="fa-solid fa-trash-can text-danger"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals for Staff and Equip -->
<div class="modal fade" id="staffModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
    <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="staffModalTitle">Staffing Baseline</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body p-4">
        <input type="hidden" id="staffId">
        <div class="row g-3">
            <div class="col-12"><label class="form-label small fw-bold">Role / Trade Group</label>
                <input type="text" id="staffRole" class="form-control" placeholder="e.g. Electrical Foreman, Site Admin...">
            </div>
            <div class="col-md-6"><label class="form-label small fw-bold">Planned Count</label>
                <input type="number" id="staffCount" class="form-control" value="0">
            </div>
            <div class="col-md-3 col-6"><label class="form-label small fw-bold">Start</label>
                <input type="date" id="staffStart" class="form-control">
            </div>
            <div class="col-md-3 col-6"><label class="form-label small fw-bold">End</label>
                <input type="date" id="staffEnd" class="form-control">
            </div>
            <div class="col-12"><label class="form-label small fw-bold">Notes</label>
                <textarea id="staffDesc" class="form-control" rows="2"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer border-0 p-4 pt-0">
        <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" onclick="saveStaff()">Save Staffing</button>
    </div>
</div>
</div>
</div>

<div class="modal fade" id="equipModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
    <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="equipModalTitle">Equipment Baseline</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body p-4">
        <input type="hidden" id="equipId">
        <div class="row g-3">
            <div class="col-12"><label class="form-label small fw-bold">Equipment Type</label>
                <input type="text" id="equipType" class="form-control" placeholder="e.g. Forklift, Generator, Temp Power...">
            </div>
            <div class="col-md-6"><label class="form-label small fw-bold">Planned Count</label>
                <input type="number" id="equipCount" class="form-control" value="0">
            </div>
            <div class="col-md-3 col-6"><label class="form-label small fw-bold">Start</label>
                <input type="date" id="equipStart" class="form-control">
            </div>
            <div class="col-md-3 col-6"><label class="form-label small fw-bold">End</label>
                <input type="date" id="equipEnd" class="form-control">
            </div>
            <div class="col-12"><label class="form-label small fw-bold">Notes</label>
                <textarea id="equipDesc" class="form-control" rows="2"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer border-0 p-4 pt-0">
        <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" onclick="saveEquip()">Save Equipment</button>
    </div>
</div>
</div>
</div>

<script>
function resetStaffModal() {
    document.getElementById('staffId').value = '';
    document.getElementById('staffRole').value = '';
    document.getElementById('staffCount').value = '1';
    document.getElementById('staffStart').value = '';
    document.getElementById('staffEnd').value = '';
    document.getElementById('staffDesc').value = '';
    document.getElementById('staffModalTitle').textContent = 'Plan Staffing Allocation';
}
function editStaff(data) {
    document.getElementById('staffId').value    = data.id;
    document.getElementById('staffRole').value  = data.role_or_trade;
    document.getElementById('staffCount').value = data.planned_count;
    document.getElementById('staffStart').value = data.start_date || '';
    document.getElementById('staffEnd').value   = data.end_date || '';
    document.getElementById('staffDesc').value  = data.description || '';
    document.getElementById('staffModalTitle').textContent = 'Edit Staffing Data';
    new bootstrap.Modal(document.getElementById('staffModal')).show();
}
function saveStaff(){
    const id = document.getElementById('staffId').value;
    const url = id 
        ? '<?= site_url("setup/staffing") ?>/' + id + '/update' 
        : `<?= site_url("projects/{$project['id']}/setup/staffing") ?>`;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('role_or_trade', document.getElementById('staffRole').value);
    fd.append('planned_count', document.getElementById('staffCount').value);
    fd.append('start_date',    document.getElementById('staffStart').value);
    fd.append('end_date',      document.getElementById('staffEnd').value);
    fd.append('description',   document.getElementById('staffDesc').value);
    fetch(url, { method:'POST', body: fd }).then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
}

function resetEquipModal() {
    document.getElementById('equipId').value = '';
    document.getElementById('equipType').value = '';
    document.getElementById('equipCount').value = '1';
    document.getElementById('equipStart').value = '';
    document.getElementById('equipEnd').value = '';
    document.getElementById('equipDesc').value = '';
    document.getElementById('equipModalTitle').textContent = 'Plan Equipment Allocation';
}
function editEquip(data) {
    document.getElementById('equipId').value    = data.id;
    document.getElementById('equipType').value  = data.equipment_type;
    document.getElementById('equipCount').value = data.planned_count;
    document.getElementById('equipStart').value = data.start_date || '';
    document.getElementById('equipEnd').value   = data.end_date || '';
    document.getElementById('equipDesc').value  = data.description || '';
    document.getElementById('equipModalTitle').textContent = 'Edit Equipment Data';
    new bootstrap.Modal(document.getElementById('equipModal')).show();
}
function saveEquip(){
    const id = document.getElementById('equipId').value;
    const url = id 
        ? '<?= site_url("setup/equipment") ?>/' + id + '/update' 
        : `<?= site_url("projects/{$project['id']}/setup/equipment") ?>`;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('equipment_type', document.getElementById('equipType').value);
    fd.append('planned_count',  document.getElementById('equipCount').value);
    fd.append('start_date',     document.getElementById('equipStart').value);
    fd.append('end_date',       document.getElementById('equipEnd').value);
    fd.append('description',    document.getElementById('equipDesc').value);
    fetch(url, { method:'POST', body: fd }).then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
}

function deleteStaff(id){ if(confirm('Delete staffing entry?')) {
    const fd = new FormData(); fd.append(CSRF_NAME, CSRF_TOKEN);
    fetch('<?= site_url("setup/staffing") ?>/' + id + '/delete', { method:'POST', body: fd }).then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
}}
function deleteEquip(id){ if(confirm('Delete equipment entry?')) {
    const fd = new FormData(); fd.append(CSRF_NAME, CSRF_TOKEN);
    fetch('<?= site_url("setup/equipment") ?>/' + id + '/delete', { method:'POST', body: fd }).then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
}}
</script>
<style>
.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; border-radius: 4px; }
</style>
