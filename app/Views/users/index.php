<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<div class="content-wrapper" style="min-height:100vh;background:#f8f9fa;">

<div class="content-header px-4 pt-4 pb-0">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h1 class="h4 fw-bold mb-0"><i class="fa-solid fa-users me-2 text-primary"></i>User Management</h1>
            <p class="text-muted small mb-0 mt-1">Manage team members &amp; roles</p>
        </div>
        <a href="<?= site_url('users/create') ?>" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-user-plus me-1"></i>Add User
        </a>
    </div>
</div>

<div class="content px-4 pt-3 pb-4">
    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-0">
            <div class="p-3 border-bottom d-flex align-items-center gap-2">
                <input type="text" id="userSearch" class="form-control form-control-sm" style="max-width:260px;" placeholder="Search users…">
            </div>
            <div class="table-responsive">
            <table class="table table-hover align-middle small mb-0" id="userTable">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th style="width:120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $u):
                    $isActive = ($u['status'] ?? 'active') === 'active';
                ?>
                <tr class="user-row">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white flex-shrink-0"
                                  style="width:32px;height:32px;font-size:13px;">
                                <?= strtoupper(substr($u['name'] ?? '?', 0, 1)) ?>
                            </span>
                            <a href="<?= site_url('users/'.(int)$u['id']) ?>" class="text-dark fw-semibold text-decoration-none">
                                <?= esc($u['name']) ?>
                            </a>
                        </div>
                    </td>
                    <td><?= esc($u['email']) ?></td>
                    <td><?= esc($u['phone'] ?? '—') ?></td>
                    <td>
                        <?php if ($u['role_name']??null): ?>
                        <span class="badge bg-secondary-subtle text-secondary"><?= esc($u['role_name']) ?></span>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-<?= $isActive ? 'success' : 'secondary' ?>-subtle text-<?= $isActive ? 'success' : 'secondary' ?>">
                            <?= $isActive ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td class="text-muted" style="font-size:11px;">
                        <?= $u['last_login'] ? date('d M Y H:i', strtotime($u['last_login'])) : '—' ?>
                    </td>
                    <td>
                        <a href="<?= site_url('users/'.(int)$u['id']) ?>" class="btn btn-xs btn-outline-primary me-1" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <button class="btn btn-xs btn-outline-<?= $isActive ? 'warning' : 'success' ?> btn-toggle-status me-1"
                                data-id="<?= (int)$u['id'] ?>" title="<?= $isActive ? 'Deactivate' : 'Activate' ?>">
                            <i class="fa-solid fa-<?= $isActive ? 'ban' : 'check' ?>"></i>
                        </button>
                        <button class="btn btn-xs btn-outline-danger btn-del-user"
                                data-id="<?= (int)$u['id'] ?>" data-name="<?= esc($u['name']) ?>" title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No users found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
</div>

<style>.btn-xs{padding:2px 7px;font-size:11px;}</style>

<script>
const CSRF_NAME = '<?= csrf_token() ?>';
const CSRF_HASH = '<?= csrf_hash() ?>';

// Search filter
document.getElementById('userSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.user-row').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

// Toggle status
document.querySelectorAll('.btn-toggle-status').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch(`<?= site_url('users/') ?>${id}/toggle-status`, {
            method:'POST',
            headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
            body: JSON.stringify({[CSRF_NAME]:CSRF_HASH})
        }).then(r=>r.json()).then(d=>{
            if (d.success) location.reload();
        });
    });
});

// Delete user
document.querySelectorAll('.btn-del-user').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm(`Remove user "${this.dataset.name}"?`)) return;
        fetch(`<?= site_url('users/') ?>${this.dataset.id}/delete`, {
            method:'POST',
            headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
            body: JSON.stringify({[CSRF_NAME]:CSRF_HASH})
        }).then(r=>r.json()).then(d=>{
            if (d.success) this.closest('tr').remove();
        });
    });
});
</script>

<?= $this->endSection() ?>
