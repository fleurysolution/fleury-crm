<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<div class="content-wrapper" style="min-height:100vh;background:#f8f9fa;">

<div class="content-header px-4 pt-4 pb-0">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= site_url('users') ?>">Users</a></li>
            <li class="breadcrumb-item active"><?= esc($user['name'] ?? 'User') ?></li>
        </ol>
    </nav>
</div>

<div class="content px-4 pt-3 pb-4">
<div class="row g-4">

    <!-- Profile card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center py-4" style="border-radius:12px;">
            <div class="mx-auto rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                 style="width:72px;height:72px;font-size:28px;">
                <?= strtoupper(substr($user['name']??'?',0,1)) ?>
            </div>
            <div class="fw-semibold mt-3"><?= esc($user['name']) ?></div>
            <div class="text-muted small"><?= esc($user['email']) ?></div>
            <div class="mt-2">
                <span class="badge bg-<?= ($user['status']??'active')==='active' ? 'success' : 'secondary' ?>-subtle text-<?= ($user['status']??'active')==='active' ? 'success' : 'secondary' ?>">
                    <?= ucfirst($user['status']??'active') ?>
                </span>
                <?php if ($user['role_name']??null): ?>
                <span class="badge bg-secondary-subtle text-secondary ms-1"><?= esc($user['role_name']) ?></span>
                <?php endif; ?>
            </div>
            <div class="text-muted small mt-2">Last login: <?= $user['last_login'] ? date('d M Y H:i', strtotime($user['last_login'])) : 'Never' ?></div>
        </div>
    </div>

    <!-- Edit form -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Edit Profile</h6>
                <form id="editUserForm">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="<?= esc($user['first_name']??'') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="<?= esc($user['last_name']??'') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= esc($user['phone']??'') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Role</label>
                            <select name="role_id" class="form-select">
                                <option value="">— No Role —</option>
                                <?php foreach ($roles as $r): ?>
                                <option value="<?= (int)$r['id'] ?>" <?= ($user['role_id']??null)==$r['id'] ? 'selected':'' ?>>
                                    <?= esc($r['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="active"   <?= ($user['status']??'')==='active'   ? 'selected':'' ?>>Active</option>
                                <option value="inactive" <?= ($user['status']??'')==='inactive' ? 'selected':'' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Associated Client / Vendor</label>
                            <select name="client_id" class="form-select">
                                <option value="">— Internal Employee (None) —</option>
                                <?php if(isset($clients)): foreach ($clients as $c): ?>
                                <option value="<?= (int)$c['id'] ?>" <?= ($user['client_id']??null)==$c['id'] ? 'selected':'' ?>>
                                    <?= esc($c['company_name']) ?>
                                </option>
                                <?php endforeach; endif; ?>
                            </select>
                            <div class="form-text" style="font-size: 0.75rem;">Only required for external Portal Users.</div>
                        </div>

                        <div class="col-12 mt-4 mb-2">
                            <h6 class="fw-bold border-bottom pb-2">Organizational & ABAC Settings</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Branch <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">— Select Branch —</option>
                                <?php if(isset($branches)): foreach ($branches as $b): ?>
                                <option value="<?= (int)$b['id'] ?>" <?= ($user['branch_id']??null)==$b['id'] ? 'selected':'' ?>>
                                    <?= esc($b['name']) ?>
                                </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">— Select Department —</option>
                                <?php if(isset($departments)): foreach ($departments as $d): ?>
                                <option value="<?= (int)$d['id'] ?>" <?= ($user['department_id']??null)==$d['id'] ? 'selected':'' ?>>
                                    <?= esc($d['name']) ?>
                                </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Reporting Manager</label>
                            <select name="reporting_manager_id" class="form-select">
                                <option value="">— None —</option>
                                <?php if(isset($managers)): foreach ($managers as $m): ?>
                                <option value="<?= (int)$m['id'] ?>" <?= ($user['reporting_manager_id']??null)==$m['id'] ? 'selected':'' ?>>
                                    <?= esc($m['first_name'] . ' ' . $m['last_name']) ?>
                                </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Employment Type</label>
                            <select name="employment_type" class="form-select">
                                <option value="">— Select Type —</option>
                                <option value="Employee" <?= ($user['employment_type']??'')==='Employee' ? 'selected':'' ?>>Employee</option>
                                <option value="Contractor" <?= ($user['employment_type']??'')==='Contractor' ? 'selected':'' ?>>Contractor</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Approval Authority Level</label>
                            <input type="number" name="approval_authority_level" class="form-control" value="<?= esc($user['approval_authority_level']??0) ?>" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Geo Access Permission</label>
                            <select name="geo_access_permission" class="form-select">
                                <option value="branch" <?= ($user['geo_access_permission']??'')==='branch' ? 'selected':'' ?>>Branch Only</option>
                                <option value="region" <?= ($user['geo_access_permission']??'')==='region' ? 'selected':'' ?>>Region Wide</option>
                                <option value="global" <?= ($user['geo_access_permission']??'')==='global' ? 'selected':'' ?>>Global (All Regions)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Payroll Profile ID</label>
                            <input type="number" name="payroll_profile_id" class="form-control" value="<?= esc($user['payroll_profile_id']??'') ?>" min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Tax Profile ID</label>
                            <input type="number" name="tax_profile_id" class="form-control" value="<?= esc($user['tax_profile_id']??'') ?>" min="1">
                        </div>

                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                        <a href="<?= site_url('users') ?>" class="btn btn-outline-secondary btn-sm">Back</a>
                    </div>
                    <div id="editMsg" class="mt-2 small"></div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Change Password</h6>
                <form id="pwdForm">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">New Password</label>
                            <input type="password" name="new_password" class="form-control" minlength="6">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Confirm</label>
                            <input type="password" id="pwdConfirm" class="form-control" minlength="6">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm mt-3">Update Password</button>
                    <div id="pwdMsg" class="mt-2 small"></div>
                </form>
            </div>
        </div>
    </div>

</div><!-- .row -->
</div><!-- .content -->
</div>

<script>
const CSRF_NAME = '<?= csrf_token() ?>';
let   CSRF_HASH = '<?= csrf_hash() ?>';
const userId    = <?= (int)$user['id'] ?>;

// Edit form
document.getElementById('editUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    const r  = await fetch(`<?= site_url('users/') ?>${userId}/update`, {
        method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}
    });
    const d  = await r.json();
    const el = document.getElementById('editMsg');
    el.className = 'mt-2 small text-' + (d.success ? 'success' : 'danger');
    el.textContent = d.message || (d.success ? 'Saved.' : 'Error.');
    if (d._token) CSRF_HASH = d._token;
});

// Password form
document.getElementById('pwdForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const newP = this.querySelector('[name=new_password]').value;
    const conf = document.getElementById('pwdConfirm').value;
    if (newP !== conf) return (document.getElementById('pwdMsg').textContent = 'Passwords do not match.');
    const fd = new FormData(this);
    const r  = await fetch(`<?= site_url('users/') ?>${userId}/password`, {
        method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}
    });
    const d  = await r.json();
    const el = document.getElementById('pwdMsg');
    el.className = 'mt-2 small text-' + (d.success ? 'success' : 'danger');
    el.textContent = d.message;
    if (d.success) this.reset();
});
</script>

<?= $this->endSection() ?>
