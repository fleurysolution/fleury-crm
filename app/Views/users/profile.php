<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<div class="content-wrapper" style="min-height:100vh;background:#f8f9fa;">

<div class="content-header px-4 pt-4 pb-0">
    <h1 class="h4 fw-bold mb-0"><i class="fa-solid fa-circle-user me-2 text-primary"></i>My Profile</h1>
    <p class="text-muted small mb-0 mt-1">Update your personal information</p>
</div>

<div class="content px-4 pt-3 pb-4">
<div class="row g-4">

    <!-- Avatar Card -->
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm text-center py-4" style="border-radius:12px;">
            <div class="mx-auto rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                 style="width:72px;height:72px;font-size:28px;">
                <?= strtoupper(substr($user['name']??'?',0,1)) ?>
            </div>
            <div class="fw-semibold mt-3"><?= esc($user['name']) ?></div>
            <div class="text-muted small"><?= esc($user['email']) ?></div>
            <div class="text-muted small mt-1">Member since <?= $user['created_at'] ? date('M Y', strtotime($user['created_at'])) : '—' ?></div>
        </div>
    </div>

    <!-- Profile form + Password form -->
    <div class="col-lg-9">

        <!-- Profile Info -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Personal Information</h6>
                <form id="profileForm">
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
                            <label class="form-label small fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= esc($user['phone']??'') ?>">
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2 align-items-center">
                        <button type="submit" class="btn btn-primary btn-sm">Save Profile</button>
                        <span id="profileMsg" class="small"></span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Change Password</h6>
                <form id="ownPwdForm">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">New Password</label>
                            <input type="password" name="new_password" class="form-control" minlength="6" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Confirm New</label>
                            <input type="password" id="pwdConf" class="form-control" minlength="6" required>
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2 align-items-center">
                        <button type="submit" class="btn btn-warning btn-sm">Update Password</button>
                        <span id="pwdMsg" class="small"></span>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
</div>
</div>

<script>
const CSRF_NAME = '<?= csrf_token() ?>';
let CSRF_HASH   = '<?= csrf_hash() ?>';

document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const r = await fetch('<?= site_url('profile/update') ?>', {
        method:'POST', body:new FormData(this), headers:{'X-Requested-With':'XMLHttpRequest'}
    });
    const d = await r.json();
    const el = document.getElementById('profileMsg');
    el.className = 'small text-' + (d.success ? 'success' : 'danger');
    el.textContent = d.message || (d.success ? 'Saved.' : 'Error.');
});

document.getElementById('ownPwdForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const newP = this.querySelector('[name=new_password]').value;
    if (newP !== document.getElementById('pwdConf').value) {
        return document.getElementById('pwdMsg').textContent = 'Passwords do not match.';
    }
    const r = await fetch('<?= site_url('profile/password') ?>', {
        method:'POST', body:new FormData(this), headers:{'X-Requested-With':'XMLHttpRequest'}
    });
    const d = await r.json();
    const el = document.getElementById('pwdMsg');
    el.className = 'small text-' + (d.success ? 'success' : 'danger');
    el.textContent = d.message;
    if (d.success) this.reset();
});
</script>

<?= $this->endSection() ?>
