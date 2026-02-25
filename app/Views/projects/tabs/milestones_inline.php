<?php
// app/Views/projects/tabs/milestones_inline.php
$msModel = new \App\Models\MilestoneModel();
$milestones = $msModel->forProject($project['id']);
$statusColors = ['pending'=>'warning','achieved'=>'success','missed'=>'danger'];
?>

<!-- Add Milestone -->
<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
        <i class="fa-solid fa-plus me-1"></i>Add Milestone
    </button>
</div>

<?php if (empty($milestones)): ?>
<div class="text-center py-5 text-muted">
    <i class="fa-solid fa-flag fa-2x mb-2 opacity-25 d-block"></i>
    No milestones yet.
</div>
<?php else: ?>
<div class="card border-0 shadow-sm" style="border-radius:10px;overflow:hidden;">
<table class="table table-hover mb-0 align-middle small">
    <thead class="table-light">
        <tr>
            <th>Milestone</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Client Facing</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($milestones as $ms):
        $daysLeft = $ms['due_date'] ? (int)ceil((strtotime($ms['due_date'])-time())/86400) : null;
    ?>
    <tr>
        <td>
            <div class="fw-semibold"><?= esc($ms['title']) ?></div>
            <?php if ($ms['description']): ?>
            <div class="text-muted" style="font-size:.75rem;"><?= esc(substr($ms['description'],0,80)) ?></div>
            <?php endif; ?>
        </td>
        <td>
            <?= $ms['due_date'] ? date('d M Y', strtotime($ms['due_date'])) : '—' ?>
            <?php if ($daysLeft !== null && $ms['status'] === 'pending'): ?>
                <?php if ($daysLeft < 0): ?>
                    <span class="badge bg-danger-subtle text-danger ms-1"><?= abs($daysLeft) ?>d late</span>
                <?php elseif ($daysLeft <= 7): ?>
                    <span class="badge bg-warning-subtle text-warning ms-1"><?= $daysLeft ?>d left</span>
                <?php endif; ?>
            <?php endif; ?>
        </td>
        <td>
            <select class="form-select form-select-sm" style="width:130px;"
                    onchange="updateMilestone(<?= $ms['id'] ?>,this.value)">
                <?php foreach (['pending','achieved','missed'] as $s): ?>
                <option value="<?= $s ?>" <?= $ms['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><?= $ms['is_client_facing'] ? '<span class="badge bg-info-subtle text-info">Yes</span>' : '<span class="text-muted">No</span>' ?></td>
        <td></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

<!-- Add Milestone Modal -->
<div class="modal fade" id="addMilestoneModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content border-0 shadow">
    <form method="post" action="#">
    <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold">Add Milestone</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
            <input type="text" id="msTitle" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Due Date</label>
            <input type="date" id="msDue" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <textarea id="msDesc" class="form-control" rows="2"></textarea>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="msClientFacing">
            <label class="form-check-label" for="msClientFacing">Client-facing milestone</label>
        </div>
    </div>
    <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveMilestone()">Save Milestone</button>
    </div>
    </form>
</div>
</div>
</div>

<script>
function saveMilestone() {
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('title',            document.getElementById('msTitle').value);
    fd.append('due_date',         document.getElementById('msDue').value);
    fd.append('description',      document.getElementById('msDesc').value);
    fd.append('is_client_facing', document.getElementById('msClientFacing').checked ? 1 : 0);
    fetch(`/staging/public/projects/<?= $project['id'] ?>/milestones`, { method:'POST', body: fd })
        .then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
}
function updateMilestone(id, status) {
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('status', status);
    fetch(`/staging/public/milestones/${id}/update`, { method:'POST', body: fd });
}
</script>
