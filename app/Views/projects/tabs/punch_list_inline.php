<?php
// app/Views/projects/tabs/punch_list_inline.php
$plModel = new \App\Models\PunchListItemModel();
$filter  = $_GET['pl_status'] ?? '';
$items   = $plModel->forProject($project['id'], $filter);
$counts  = $plModel->statusCounts($project['id']);
$aging   = $plModel->agingCounts($project['id']);
$areas   = (new \App\Models\AreaModel())->where('project_id', $project['id'])->findAll();
$users   = (new \App\Models\UserModel())->findAll();

$statusColors   = ['open'=>'danger','in_progress'=>'warning','resolved'=>'info','closed'=>'success','voided'=>'secondary'];
$priorityColors = ['low'=>'info','medium'=>'secondary','high'=>'warning','urgent'=>'danger'];
?>

<!-- Status filter bar + aging widget -->
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex gap-2 flex-wrap">
        <a href="?" class="badge bg-light text-dark text-decoration-none px-3 py-2 border <?= $filter===''?'border-primary':'' ?>">
            All (<?= array_sum($counts) ?>)
        </a>
        <?php foreach ($statusColors as $st => $col): $cnt = $counts[$st] ?? 0; ?>
        <a href="?pl_status=<?= $st ?>" class="badge bg-<?= $col ?>-subtle text-<?= $col ?> px-3 py-2 text-decoration-none <?= $filter===$st?'border border-'.$col:'' ?>">
            <?= ucfirst(str_replace('_',' ',$st)) ?> (<?= $cnt ?>)
        </a>
        <?php endforeach; ?>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url("projects/{$project['id']}/punch-list/export") ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-download me-1"></i>CSV
        </a>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newPunchModal">
            <i class="fa-solid fa-plus me-1"></i>Add Item
        </button>
    </div>
</div>

<!-- Aging widget -->
<?php if ($aging && (($aging['week0']??0)+($aging['week1']??0)+($aging['week2']??0)+($aging['older']??0)) > 0): ?>
<div class="card border-0 bg-light mb-3 px-3 py-2 d-flex flex-row gap-3 align-items-center small" style="border-radius:8px;">
    <span class="fw-semibold text-muted">Open items aging:</span>
    <span class="text-success">≤7 days: <?= $aging['week0']??0 ?></span>
    <span class="text-warning">8–14 days: <?= $aging['week1']??0 ?></span>
    <span class="text-orange fw-semibold">15–21 days: <?= $aging['week2']??0 ?></span>
    <span class="text-danger fw-bold">&gt;21 days: <?= $aging['older']??0 ?></span>
</div>
<?php endif; ?>

<!-- Punch list table -->
<?php if (empty($items)): ?>
<div class="text-center py-5 text-muted">
    <i class="fa-solid fa-clipboard-check fa-2x mb-2 opacity-25 d-block"></i>
    No punch list items<?= $filter ? " with status <strong>$filter</strong>" : '' ?>. Click <strong>Add Item</strong> to begin.
</div>
<?php else: ?>
<div class="card border-0 shadow-sm" style="border-radius:10px;overflow:hidden;">
<table class="table table-hover align-middle small mb-0">
    <thead class="table-light">
        <tr>
            <th style="width:90px;">#</th>
            <th>Title</th>
            <th>Area</th>
            <th>Trade</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Assigned</th>
            <th>Due</th>
            <th>Days Open</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $it):
        $daysOpen = (in_array($it['status'],['open','in_progress']) && $it['created_at'])
            ? (int)((time() - strtotime($it['created_at'])) / 86400) : null;
        $ageClass = $daysOpen !== null ? ($daysOpen > 21 ? 'text-danger fw-bold' : ($daysOpen > 14 ? 'text-warning' : '')) : '';
    ?>
    <tr>
        <td class="fw-semibold"><?= esc($it['item_number'] ?? '') ?></td>
        <td>
            <div class="fw-semibold"><?= esc($it['title']) ?></div>
            <?php if ($it['description']): ?>
            <div class="text-muted" style="font-size:.7rem;"><?= esc(substr($it['description'],0,60)) ?>…</div>
            <?php endif; ?>
        </td>
        <td class="text-muted"><?= esc($it['area_name'] ?? '—') ?></td>
        <td class="text-muted"><?= esc($it['trade'] ?? '—') ?></td>
        <td><span class="badge bg-<?= $priorityColors[$it['priority']] ?>-subtle text-<?= $priorityColors[$it['priority']] ?>"><?= ucfirst($it['priority']) ?></span></td>
        <td>
            <select class="form-select form-select-sm" style="width:120px;"
                    onchange="updatePunchStatus(<?= $it['id'] ?>, this.value)">
                <?php foreach ($statusColors as $st => $_): ?>
                <option value="<?= $st ?>" <?= $it['status']===$st?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$st)) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><?= esc($it['assignee_name'] ?? '—') ?></td>
        <td><?= $it['due_date'] ? date('d M', strtotime($it['due_date'])) : '—' ?></td>
        <td class="<?= $ageClass ?>"><?= $daysOpen !== null ? $daysOpen.'d' : '—' ?></td>
        <td>
            <button class="btn btn-sm btn-link text-danger p-0" onclick="deletePunchItem(<?= $it['id'] ?>)">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

<!-- New Punch Item Modal -->
<div class="modal fade" id="newPunchModal" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content border-0 shadow">
    <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold"><i class="fa-solid fa-clipboard-check me-2 text-primary"></i>Add Punch List Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label small fw-semibold">Title <span class="text-danger">*</span></label>
                <input type="text" id="plTitle" class="form-control" placeholder="Describe the deficiency…">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Trade</label>
                <input type="text" id="plTrade" class="form-control" placeholder="e.g. Electrical, Plumbing">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Priority</label>
                <select id="plPriority" class="form-select">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Due Date</label>
                <input type="date" id="plDue" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Area</label>
                <select id="plArea" class="form-select">
                    <option value="">None</option>
                    <?php foreach ($areas as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= esc($a['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Assign To</label>
                <select id="plAssignee" class="form-select">
                    <option value="">Unassigned</option>
                    <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= esc($u['first_name'] . ' ' . $u['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Description</label>
                <textarea id="plDesc" class="form-control" rows="2"></textarea>
            </div>
            
            <!-- Smart Features -->
            <div class="col-md-6">
                <div class="card bg-light border-0">
                    <div class="card-body p-3">
                        <label class="form-label small fw-bold d-block mb-2"><i class="fa-solid fa-location-dot me-1"></i>Geotagging</label>
                        <button type="button" class="btn btn-sm btn-outline-primary w-100" onclick="pinLocation()">
                            <i class="fa-solid fa-crosshairs me-1"></i> Pin Current Location
                        </button>
                        <div id="gpsStatus" class="small text-muted mt-2" style="font-size:0.7rem;">No GPS data pinned.</div>
                        <input type="hidden" id="plLat">
                        <input type="hidden" id="plLng">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light border-0">
                    <div class="card-body p-3 text-center">
                        <label class="form-label small fw-bold d-block mb-2 text-start"><i class="fa-solid fa-camera me-1"></i>Photo Verification</label>
                        <div id="cameraPreview" class="bg-dark rounded mb-2 d-none" style="height:100px; display:flex; align-items:center; justify-content:center;">
                            <video id="video" width="100%" height="100%" autoplay playsinline></video>
                        </div>
                        <img id="photoThumb" class="img-thumbnail mb-2 d-none" style="height:100px;">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary flex-grow-1" onclick="startCamera()">
                                <i class="fa-solid fa-video me-1"></i> Camera
                            </button>
                            <input type="file" id="plFile" class="d-none" accept="image/*" onchange="previewFile(this)">
                            <button type="button" class="btn btn-sm btn-outline-secondary flex-grow-1" onclick="document.getElementById('plFile').click()">
                                <i class="fa-solid fa-upload me-1"></i> Upload
                            </button>
                        </div>
                        <canvas id="canvas" class="d-none"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="submitPunchItem()">Add Item</button>
    </div>
</div>
</div>
</div>

<script>
function updatePunchStatus(id, status) {
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('status', status);
    fetch(`/staging/public/punch-list/${id}/status`, {method:'POST', body: fd});
}

function deletePunchItem(id) {
    if (!confirm('Delete this punch list item?')) return;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fetch(`/staging/public/punch-list/${id}/delete`, {method:'POST', body: fd})
        .then(() => location.reload());
}

let capturedBlob = null;

function pinLocation() {
    if (!navigator.geolocation) { alert('Geolocation not supported.'); return; }
    const status = document.getElementById('gpsStatus');
    status.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Locating...';
    navigator.geolocation.getCurrentPosition(pos => {
        document.getElementById('plLat').value = pos.coords.latitude;
        document.getElementById('plLng').value = pos.coords.longitude;
        status.innerHTML = `<i class="fa-solid fa-check text-success me-1"></i> Pinned: ${pos.coords.latitude.toFixed(5)}, ${pos.coords.longitude.toFixed(5)}`;
    }, err => {
        status.innerHTML = `<i class="fa-solid fa-triangle-exclamation text-danger me-1"></i> Error: ${err.message}`;
    });
}

function startCamera() {
    const video = document.getElementById('video');
    const preview = document.getElementById('cameraPreview');
    preview.classList.remove('d-none');
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(stream => {
            video.srcObject = stream;
            // Add click to capture
            video.onclick = () => {
                const canvas = document.getElementById('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);
                canvas.toBlob(blob => {
                    capturedBlob = blob;
                    const thumb = document.getElementById('photoThumb');
                    thumb.src = URL.createObjectURL(blob);
                    thumb.classList.remove('d-none');
                    preview.classList.add('d-none');
                    stream.getTracks().forEach(t => t.stop());
                }, 'image/jpeg', 0.8);
            };
        });
}

function previewFile(input) {
    if (input.files && input.files[0]) {
        capturedBlob = input.files[0];
        const thumb = document.getElementById('photoThumb');
        thumb.src = URL.createObjectURL(capturedBlob);
        thumb.classList.remove('d-none');
    }
}

function submitPunchItem() {
    const title = document.getElementById('plTitle').value.trim();
    if (!title) { alert('Please enter a title.'); return; }
    
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('title',       title);
    fd.append('description', document.getElementById('plDesc').value);
    fd.append('trade',       document.getElementById('plTrade').value);
    fd.append('priority',    document.getElementById('plPriority').value);
    fd.append('due_date',    document.getElementById('plDue').value);
    fd.append('area_id',     document.getElementById('plArea').value);
    fd.append('assigned_to', document.getElementById('plAssignee').value);
    fd.append('latitude',    document.getElementById('plLat').value);
    fd.append('longitude',   document.getElementById('plLng').value);
    
    if (capturedBlob) {
        fd.append('photo', capturedBlob, 'punch.jpg');
    }

    fetch(`/staging/public/projects/<?= $project['id'] ?>/punch-list`, {method:'POST', body: fd})
        .then(r=>r.json()).then(d=>{
            if (d.success) location.reload();
            else alert('Could not add item.');
        });
}
</script>
