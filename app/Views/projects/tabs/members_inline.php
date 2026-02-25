<?php
// app/Views/projects/tabs/members_inline.php
$memberModel = new \App\Models\ProjectMemberModel();
$userModel   = new \App\Models\UserModel();
$members     = $memberModel->getMembers($project['id']);
$allUsers    = $userModel->select('id, CONCAT(first_name, " ", last_name) AS name, email')->findAll();
$memberIds   = array_column($members,'user_id');
$roleBadge   = ['pm'=>'primary','member'=>'success','viewer'=>'secondary','client'=>'info','subcontractor'=>'warning'];
?>

<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
        <i class="fa-solid fa-user-plus me-1"></i>Add Member
    </button>
</div>

<div class="card border-0 shadow-sm" style="border-radius:10px;overflow:hidden;">
    <table class="table table-hover align-middle mb-0 small">
        <thead class="table-light">
            <tr><th>Member</th><th>Email</th><th>Role</th></tr>
        </thead>
        <tbody>
        <?php if (empty($members)): ?>
        <tr><td colspan="3" class="text-center text-muted py-3">No members yet.</td></tr>
        <?php else: foreach ($members as $m): ?>
        <tr>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <div class="user-avatar" style="width:32px;height:32px;font-size:.75rem;">
                        <?= strtoupper(substr($m['name']??'?',0,1)) ?>
                    </div>
                    <span class="fw-semibold"><?= esc($m['name']) ?></span>
                </div>
            </td>
            <td class="text-muted"><?= esc($m['email']) ?></td>
            <td><span class="badge bg-<?= $roleBadge[$m['role']]??'secondary' ?>-subtle text-<?= $roleBadge[$m['role']]??'secondary' ?>"><?= ucfirst($m['role']) ?></span></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content border-0 shadow">
    <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Add Team Member</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label fw-semibold">User</label>
            <select id="memberUserId" class="form-select">
                <?php foreach ($allUsers as $u): if (in_array($u['id'],$memberIds)) continue; ?>
                <option value="<?= $u['id'] ?>"><?= esc($u['name']) ?> (<?= esc($u['email']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Role</label>
            <select id="memberRole" class="form-select">
                <option value="member">Member</option>
                <option value="pm">Project Manager</option>
                <option value="viewer">Viewer</option>
                <option value="client">Client</option>
                <option value="subcontractor">Subcontractor</option>
            </select>
        </div>
    </div>
    <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="addMember()">Add</button>
    </div>
</div>
</div>
</div>

<script>
function addMember(){
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('user_id', document.getElementById('memberUserId').value);
    fd.append('role',    document.getElementById('memberRole').value);
    fetch(`/staging/public/projects/<?= $project['id'] ?>/members`, { method:'POST', body: fd })
        .then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
}
</script>
