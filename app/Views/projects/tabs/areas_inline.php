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

function renderAreaTree(array $nodes, array $typeIcons, int $depth = 0): void {
    foreach ($nodes as $n):
        [$icon, $color] = $typeIcons[$n['type']] ?? ['fa-cube','secondary'];
?>
<div class="area-node" style="margin-left:<?= $depth*24 ?>px;">
    <div class="d-flex align-items-center gap-2 py-2 border-bottom area-row" data-id="<?= $n['id'] ?>">
        <i class="fa-solid <?= $icon ?> text-<?= $color ?>" style="width:18px;text-align:center;"></i>
        <span class="fw-semibold small"><?= esc($n['name']) ?></span>
        <span class="badge bg-<?= $color ?>-subtle text-<?= $color ?> ms-1"><?= ucfirst($n['type']) ?></span>
        <?php if ($n['description']): ?>
            <span class="text-muted small ms-2"><?= esc($n['description']) ?></span>
        <?php endif; ?>
        <button class="btn btn-sm btn-link p-0 ms-auto text-muted" onclick="addChildArea(<?= $n['id'] ?>)" title="Add child">
            <i class="fa-solid fa-plus"></i>
        </button>
        <button class="btn btn-sm btn-link p-0 text-danger" onclick="deleteArea(<?= $n['id'] ?>)" title="Delete">
            <i class="fa-solid fa-trash-can"></i>
        </button>
    </div>
    <?php if (!empty($n['children'])) renderAreaTree($n['children'], $typeIcons, $depth+1); ?>
</div>
<?php endforeach;
}
?>

<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-sm btn-primary" onclick="addRootArea()">
        <i class="fa-solid fa-plus me-1"></i>Add Area
    </button>
</div>

<div class="card border-0 shadow-sm p-3" style="border-radius:10px;">
    <div id="areaTree">
    <?php if (empty($tree)): ?>
        <p class="text-muted small text-center py-3">No areas defined. Add a Building to start.</p>
    <?php else: renderAreaTree($tree, $typeIcons); endif; ?>
    </div>
</div>

<!-- Add Area Modal -->
<div class="modal fade" id="addAreaModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content border-0 shadow">
    <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold" id="addAreaModalTitle">Add Area</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <input type="hidden" id="areaParentId">
        <div class="mb-3">
            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
            <input type="text" id="areaName" class="form-control" placeholder="e.g. Block A, Floor 2, Unit 301">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Type</label>
            <select id="areaType" class="form-select">
                <option value="building">Building</option>
                <option value="floor">Floor</option>
                <option value="zone">Zone</option>
                <option value="unit">Unit / Room</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <input type="text" id="areaDesc" class="form-control" placeholder="Optional">
        </div>
    </div>
    <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveArea()">Save</button>
    </div>
</div>
</div>
</div>

<script>
let _addAreaModal;
document.addEventListener('DOMContentLoaded', () => {
    _addAreaModal = new bootstrap.Modal(document.getElementById('addAreaModal'));
});
function addRootArea(){
    document.getElementById('areaParentId').value = '';
    document.getElementById('addAreaModalTitle').textContent = 'Add Root Area';
    document.getElementById('areaName').value = '';
    _addAreaModal.show();
}
function addChildArea(parentId){
    document.getElementById('areaParentId').value = parentId;
    document.getElementById('addAreaModalTitle').textContent = 'Add Child Area';
    document.getElementById('areaName').value = '';
    _addAreaModal.show();
}
function saveArea(){
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('parent_id',   document.getElementById('areaParentId').value);
    fd.append('name',        document.getElementById('areaName').value);
    fd.append('type',        document.getElementById('areaType').value);
    fd.append('description', document.getElementById('areaDesc').value);
    fetch(`/staging/public/projects/<?= $project['id'] ?>/areas`, { method:'POST', body: fd })
        .then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
    _addAreaModal.hide();
}
function deleteArea(id){
    if(!confirm('Delete this area and all its children?')) return;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fetch(`/staging/public/areas/${id}/delete`, { method:'POST', body: fd })
        .then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
}
</script>
