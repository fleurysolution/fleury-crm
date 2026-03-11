<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<div class="content-wrapper" style="min-height:100vh;background:#f8f9fa;">

<div class="content-header px-4 pt-4 pb-0">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= site_url('users') ?>">Users</a></li>
            <li class="breadcrumb-item active">Add User</li>
        </ol>
    </nav>
    <h1 class="h4 fw-bold mb-0 mt-1"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Add New User</h1>
</div>

<div class="content px-4 pt-3 pb-4">
<div class="card border-0 shadow-sm" style="border-radius:12px;max-width:680px;">
    <div class="card-body p-4">
        <form id="createUserForm" action="<?= site_url('users/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" required autofocus>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" minlength="6" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Role</label>
                    <select name="role_id" class="form-select">
                        <option value="">— Select Role —</option>
                        <?php foreach ($roles as $r): ?>
                        <option value="<?= (int)$r['id'] ?>"><?= esc($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <!-- Vendor/Client Assignment -->
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Associated Client / Vendor</label>
                    <select name="client_id" class="form-select">
                        <option value="">— Internal Employee (None) —</option>
                        <?php if(isset($clients)): foreach ($clients as $c): ?>
                        <option value="<?= (int)$c['id'] ?>"><?= esc($c['company_name']) ?></option>
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
                        <option value="<?= (int)$b['id'] ?>"><?= esc($b['name']) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">— Select Department —</option>
                        <?php if(isset($departments)): foreach ($departments as $d): ?>
                        <option value="<?= (int)$d['id'] ?>"><?= esc($d['name']) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Reporting Manager</label>
                    <select name="reporting_manager_id" class="form-select">
                        <option value="">— None —</option>
                        <?php if(isset($managers)): foreach ($managers as $m): ?>
                        <option value="<?= (int)$m['id'] ?>"><?= esc($m['first_name'] . ' ' . $m['last_name']) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Employment Type</label>
                    <select name="employment_type" class="form-select">
                        <option value="">— Select Type —</option>
                        <option value="Employee">Employee</option>
                        <option value="Contractor">Contractor</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Approval Authority Level</label>
                    <input type="number" name="approval_authority_level" class="form-control" value="0" min="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Geo Access Permission</label>
                    <select name="geo_access_permission" class="form-select">
                        <option value="branch">Branch Only</option>
                        <option value="region">Region Wide</option>
                        <option value="global">Global (All Regions)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Payroll Profile ID</label>
                    <input type="number" name="payroll_profile_id" class="form-control" min="1">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Tax Profile ID</label>
                    <input type="number" name="tax_profile_id" class="form-control" min="1">
                </div>

            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Create User</button>
                <a href="<?= site_url('users') ?>" class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
            <div id="createMsg" class="mt-2 small"></div>
        </form>
    </div>
</div>
</div>
</div>

<script>
document.getElementById('createUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const r = await fetch('<?= site_url('users/store') ?>', {
        method:'POST', body:new FormData(this), headers:{'X-Requested-With':'XMLHttpRequest'}
    });
    const d = await r.json();
    if (d.success) {
        window.location.href = '<?= site_url('users') ?>';
    } else {
        const el = document.getElementById('createMsg');
        el.className = 'mt-2 small text-danger';
        el.textContent = d.message || 'Error creating user.';
    }
});
</script>

<?= $this->endSection() ?>
