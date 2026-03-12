<?php
// app/Views/projects/tabs/areas_inline.php
$areaModel = new \App\Models\AreaModel();
$tree = $areaModel->getTree($project['id']);
$typeIcons = [
    'building'=>['fa-building','primary'],
    'floor'   =>['fa-layer-group','info'],
    'zone'    =>['fa-draw-polygon','warning'],
    'unit'    =>['fa-door-open','success'],
    'other'   =>['fa-cube','secondary'],
];
$statusColors = [
    'planning' => 'secondary',
    'active'   => 'primary',
    'turnover' => 'warning',
    'completed'=> 'success'
];

function renderAreaTree(array $nodes, array $typeIcons, array $statusColors, int $depth = 0): void {
    foreach ($nodes as $n):
        [$icon, $color] = $typeIcons[$n['type']] ?? ['fa-cube','secondary'];
        $stColor = $statusColors[$n['status']] ?? 'secondary';
?>
<div class="area-node" style="margin-left:<?= $depth*24 ?>px;">
    <div class="d-flex align-items-center gap-2 py-3 border-bottom area-row" data-id="<?= $n['id'] ?>" data-json='<?= json_encode($n) ?>'>
        <div class="d-flex align-items-center" style="width: 30%;">
            <i class="fa-solid <?= $icon ?> text-<?= $color ?> me-2" style="width:20px;text-align:center;"></i>
            <div>
                <div class="fw-bold small"><?= esc($n['name']) ?></div>
                <div class="text-muted" style="font-size: 10px;"><?= ucfirst($n['type']) ?></div>
            </div>
        </div>
        
        <div style="width: 15%;">
            <span class="badge bg-<?= $stColor ?>-subtle text-<?= $stColor ?> border border-<?= $stColor ?>-subtle px-2 py-1" style="font-size: 10px;">
                <?= ucfirst($n['status']) ?>
            </span>
        </div>

        <div class="small text-muted" style="width: 35%;">
            <?php if ($n['start_date'] || $n['end_date']): ?>
                <i class="fa-regular fa-calendar-check me-1"></i>
                <?= $n['start_date'] ? date('M d', strtotime($n['start_date'])) : '—' ?> 
                <i class="fa-solid fa-arrow-right mx-1 opacity-50" style="font-size: 8px;"></i>
                <?= $n['end_date'] ? date('M d', strtotime($n['end_date'])) : '—' ?>
            <?php endif; ?>
            <?php if ($n['turnover_date']): ?>
                <span class="ms-2 text-warning fw-semibold" title="Handover Date">
                    <i class="fa-solid fa-hand-holding-heart me-1"></i><?= date('M d', strtotime($n['turnover_date'])) ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="ms-auto d-flex gap-2">
            <button class="btn btn-xs btn-light border" onclick="addChildArea(<?= $n['id'] ?>)" title="Add child">
                <i class="fa-solid fa-plus text-primary"></i>
            </button>
            <button class="btn btn-xs btn-light border" onclick='editArea(<?= json_encode($n) ?>)' title="Edit">
                <i class="fa-solid fa-pencil text-muted"></i>
            </button>
            <button class="btn btn-xs btn-light border" onclick="deleteArea(<?= $n['id'] ?>)" title="Delete">
                <i class="fa-solid fa-trash-can text-danger"></i>
            </button>
        </div>
    </div>
    <?php if (!empty($n['children'])) renderAreaTree($n['children'], $typeIcons, $statusColors, $depth+1); ?>
</div>
<?php endforeach;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h6 class="fw-bold mb-0">Project Areas & Infrastructure</h6>
        <p class="text-muted small mb-0">Define buildings, zones, and commissioning turnover packages.</p>
    </div>
    <button class="btn btn-primary shadow-sm" onclick="addRootArea()">
        <i class="fa-solid fa-plus me-1"></i>Add Root Area
    </button>
</div>

<div class="card border-0 shadow-sm p-0 overflow-hidden" style="border-radius:15px;">
    <div class="bg-light px-3 py-2 border-bottom d-flex small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">
        <div style="width: 30%;">Area / Zone Name</div>
        <div style="width: 15%;">Status</div>
        <div style="width: 35%;">Timeline & Turnover</div>
        <div class="ms-auto">Actions</div>
    </div>
    <div id="areaTree" class="px-3 pb-3">
    <?php if (empty($tree)): ?>
        <div class="text-center py-5">
            <i class="fa-solid fa-layer-group fa-3x text-light mb-3"></i>
            <p class="text-muted">No areas defined yet. Create your first building or site area to begin tracking.</p>
        </div>
    <?php else: renderAreaTree($tree, $typeIcons, $statusColors); endif; ?>
    </div>
</div>

<!-- Area Modal -->
<div class="modal fade" id="addAreaModal" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
    <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="addAreaModalTitle">Area Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body p-4">
        <input type="hidden" id="areaId">
        <input type="hidden" id="areaParentId">
        
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold small">Area / Section Name <span class="text-danger">*</span></label>
                <input type="text" id="areaName" class="form-control" placeholder="e.g. Data Hall A, Building 100...">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Classification</label>
                <select id="areaType" class="form-select">
                    <option value="building">Building</option>
                    <option value="floor">Floor / Level</option>
                    <option value="zone">Zone / Phase</option>
                    <option value="unit">Unit / Room</option>
                    <option value="other">Other Infrastructure</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold small">Production Status</label>
                <select id="areaStatus" class="form-select">
                    <option value="planning">Pre-Con / Planning</option>
                    <option value="active">Active Execution</option>
                    <option value="turnover">Startup / Turnover</option>
                    <option value="completed">Operational / Handover</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Start Date</label>
                <input type="date" id="areaStart" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Finish Date</label>
                <input type="date" id="areaEnd" class="form-control">
            </div>

            <div class="col-md-12">
                <div class="p-3 bg-warning-subtle rounded-3 border border-warning-subtle">
                    <label class="form-label fw-bold small text-warning-emphasis mb-1">
                        <i class="fa-solid fa-hand-holding-heart me-1"></i>Turnover / Commissioning Target
                    </label>
                    <input type="date" id="areaTurnover" class="form-control border-warning-subtle">
                    <div class="form-text text-warning-emphasis" style="font-size: 11px;">Target date for system energization or client handover.</div>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold small">Description / Notes</label>
                <textarea id="areaDesc" class="form-control" rows="2" placeholder="CSI division focus, lead-time items..."></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer border-0 pt-0 p-4">
        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" onclick="saveArea()">Save Parameters</button>
    </div>
</div>
</div>
</div>

<style>
.area-row { transition: all 0.2s; }
.area-row:hover { background: rgba(0,0,0,0.02); }
.btn-xs { padding: 0.25rem 0.4rem; font-size: 0.75rem; border-radius: 0.3rem; }
</style>

<script>
let _areaModal;
document.addEventListener('DOMContentLoaded', () => {
    _areaModal = new bootstrap.Modal(document.getElementById('addAreaModal'));
});

function resetModal() {
    document.getElementById('areaId').value = '';
    document.getElementById('areaParentId').value = '';
    document.getElementById('areaName').value = '';
    document.getElementById('areaType').value = 'building';
    document.getElementById('areaStatus').value = 'planning';
    document.getElementById('areaStart').value = '';
    document.getElementById('areaEnd').value = '';
    document.getElementById('areaTurnover').value = '';
    document.getElementById('areaDesc').value = '';
}

function addRootArea(){
    resetModal();
    document.getElementById('addAreaModalTitle').textContent = 'Create Root Project Area';
    _areaModal.show();
}

function addChildArea(parentId){
    resetModal();
    document.getElementById('areaParentId').value = parentId;
    document.getElementById('addAreaModalTitle').textContent = 'Add Sub-Area / Phase';
    _areaModal.show();
}

function editArea(data){
    resetModal();
    document.getElementById('areaId').value         = data.id;
    document.getElementById('areaParentId').value   = data.parent_id || '';
    document.getElementById('areaName').value       = data.name;
    document.getElementById('areaType').value       = data.type;
    document.getElementById('areaStatus').value     = data.status;
    document.getElementById('areaStart').value      = data.start_date || '';
    document.getElementById('areaEnd').value        = data.end_date || '';
    document.getElementById('areaTurnover').value   = data.turnover_date || '';
    document.getElementById('areaDesc').value       = data.description || '';
    document.getElementById('addAreaModalTitle').textContent = 'Edit Area Parameters';
    _areaModal.show();
}

function saveArea(){
    const id = document.getElementById('areaId').value;
    const pid = document.getElementById('areaParentId').value;
    const url = id 
        ? '<?= site_url("areas") ?>/' + id + '/update' 
        : `<?= site_url("projects/{$project['id']}/areas") ?>`;

    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    if (!id) fd.append('parent_id', pid);
    fd.append('name',          document.getElementById('areaName').value);
    fd.append('type',          document.getElementById('areaType').value);
    fd.append('status',        document.getElementById('areaStatus').value);
    fd.append('start_date',    document.getElementById('areaStart').value);
    fd.append('end_date',      document.getElementById('areaEnd').value);
    fd.append('turnover_date', document.getElementById('areaTurnover').value);
    fd.append('description',   document.getElementById('areaDesc').value);

    fetch(url, { method:'POST', body: fd })
        .then(r=>r.json())
        .then(d=>{ if(d.success) location.reload(); else alert('Error saving area.'); });
    _areaModal.hide();
}

function deleteArea(id){
    if(!confirm('DANGER: Delete this area and all nested phase/sub-areas? This action is permanent.')) return;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fetch('<?= site_url("areas") ?>/' + id + '/delete', { method:'POST', body: fd })
        .then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
}
</script>
