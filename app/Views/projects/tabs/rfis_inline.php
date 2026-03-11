<?php
// app/Views/projects/tabs/rfis_inline.php
// Included inside show.php — has access to $project
$rfiModel = new \App\Models\RfiModel();
$rfis   = $rfiModel->forProject($project['id']);
$counts = $rfiModel->statusCounts($project['id']);

$statusColors = [
    'draft'        => 'secondary',
    'submitted'    => 'primary',
    'under_review' => 'warning',
    'answered'     => 'info',
    'closed'       => 'success',
];
$priorityColors = ['low'=>'info','medium'=>'secondary','high'=>'warning','urgent'=>'danger'];
$members = (new \App\Models\ProjectMemberModel())->getMembers($project['id']);
$users   = (new \App\Models\UserModel())->findAll();
$areas   = (new \App\Models\AreaModel())->where('project_id', $project['id'])->findAll();
?>

<!-- Toolbar -->
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex gap-2 flex-wrap">
        <?php
        $allStatuses = ['submitted','under_review','answered','closed','draft'];
        foreach ($allStatuses as $st): $cnt = $counts[$st] ?? 0; ?>
        <span class="badge bg-<?= $statusColors[$st] ?>-subtle text-<?= $statusColors[$st] ?> px-3 py-2">
            <?= ucfirst(str_replace('_',' ',$st)) ?> (<?= $cnt ?>)
        </span>
        <?php endforeach; ?>
    </div>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newRfiModal">
        <i class="fa-solid fa-plus me-1"></i>New RFI
    </button>
</div>

<!-- RFI List -->
<?php if (empty($rfis)): ?>
<div class="text-center py-5 text-muted">
    <i class="fa-solid fa-circle-question fa-2x mb-2 opacity-25 d-block"></i>
    No RFIs yet. Click <strong>New RFI</strong> to submit the first one.
</div>
<?php else: ?>
<div class="card border-0 shadow-sm" style="border-radius:10px;overflow:hidden;">
<table class="table table-hover align-middle small mb-0">
    <thead class="table-light">
        <tr>
            <th style="width:100px;">Number</th>
            <th>Title</th>
            <th>Discipline</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Assigned</th>
            <th>Due</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($rfis as $r):
        $daysLeft = $r['due_date'] ? (int)((strtotime($r['due_date']) - time()) / 86400) : null;
    ?>
    <tr>
        <td><a href="<?= site_url("rfis/{$r['id']}") ?>" class="fw-semibold text-decoration-none"><?= esc($r['rfi_number']) ?></a></td>
        <td>
            <a href="<?= site_url("rfis/{$r['id']}") ?>" class="text-decoration-none text-dark"><?= esc($r['title']) ?></a>
            <?php if ($r['description']): ?>
            <div class="text-muted" style="font-size:.72rem;"><?= esc(substr($r['description'],0,70)) ?>…</div>
            <?php endif; ?>
        </td>
        <td class="text-muted"><?= esc($r['discipline'] ?? '—') ?></td>
        <td><span class="badge bg-<?= $priorityColors[$r['priority']] ?>-subtle text-<?= $priorityColors[$r['priority']] ?>"><?= ucfirst($r['priority']) ?></span></td>
        <td>
            <select class="form-select form-select-sm" style="width:130px;"
                    onchange="updateRfiStatus(<?= $r['id'] ?>, this.value)">
                <?php foreach ($statusColors as $st => $_): ?>
                <option value="<?= $st ?>" <?= $r['status']===$st?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$st)) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><?= esc($r['assignee_name'] ?? '—') ?></td>
        <td>
            <?php if ($daysLeft !== null):
                $cls = $daysLeft < 0 ? 'text-danger fw-bold' : ($daysLeft <= 3 ? 'text-warning fw-semibold' : '');
            ?>
            <span class="<?= $cls ?>"><?= date('d M', strtotime($r['due_date'])) ?></span>
            <?php else: ?>—<?php endif; ?>
        </td>
        <td>
            <a href="<?= site_url("rfis/{$r['id']}") ?>" class="btn btn-sm btn-outline-primary">View</a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

<!-- New RFI Modal -->
<div class="modal fade" id="newRfiModal" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content border-0 shadow">
    <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold"><i class="fa-solid fa-circle-question me-2 text-primary"></i>Submit New RFI</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">RFI Number <span class="text-danger">*</span></label>
                <input type="text" id="rfiNumber" class="form-control" placeholder="e.g. RFI-001" required>
            </div>
            <div class="col-md-9">
                <label class="form-label small fw-semibold">Title <span class="text-danger">*</span></label>
                <input type="text" id="rfiTitle" class="form-control" placeholder="What is the question or issue?">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Discipline</label>
                <input type="text" id="rfiDiscipline" class="form-control" placeholder="e.g. Structural, MEP…">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Priority</label>
                <select id="rfiPriority" class="form-select">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Due Date</label>
                <input type="date" id="rfiDue" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Area</label>
                <select id="rfiArea" class="form-select">
                    <option value="">None</option>
                    <?php foreach ($areas as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= esc($a['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Assign To</label>
                <select id="rfiAssignee" class="form-select">
                    <option value="">Unassigned</option>
                    <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= esc($u['first_name'] . ' ' . $u['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Description</label>
                <textarea id="rfiDesc" class="form-control" rows="4" placeholder="Provide full context, drawings reference, etc."></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="submitNewRfi()">
            <i class="fa-solid fa-paper-plane me-1"></i>Submit RFI
        </button>
    </div>
</div>
</div>
</div>

<script>
function updateRfiStatus(id, status) {
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('status', status);
    fetch(`/staging/public/rfis/${id}/status`, {method:'POST', body: fd});
}

function submitNewRfi() {
    const title = document.getElementById('rfiTitle').value.trim();
    const number = document.getElementById('rfiNumber').value.trim();
    if (!number) { alert('Please enter an RFI Number.'); return; }
    if (!title) { alert('Please enter a title.'); return; }
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('rfi_number',  document.getElementById('rfiNumber').value.trim());
    fd.append('title',       title);
    fd.append('description', document.getElementById('rfiDesc').value);
    fd.append('discipline',  document.getElementById('rfiDiscipline').value);
    fd.append('priority',    document.getElementById('rfiPriority').value);
    fd.append('due_date',    document.getElementById('rfiDue').value);
    fd.append('area_id',     document.getElementById('rfiArea').value);
    fd.append('assigned_to', document.getElementById('rfiAssignee').value);
    fetch(`/staging/public/projects/<?= $project['id'] ?>/rfis`, {
        method:'POST', 
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(r=>r.json()).then(d=>{
            if (d.success) location.reload();
            else alert('Could not create RFI.');
        });
}
</script>
