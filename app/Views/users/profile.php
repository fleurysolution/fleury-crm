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

        <!-- Subscription & Billing (Admin Only) -->
        <?php if (session()->get('is_admin')): ?>
        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fa-solid fa-credit-card me-2 text-success"></i>Subscription & Billing</h6>
                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold">Current Plan</div>
                        <div class="h5 fw-bold mb-0 text-primary" id="currentPlanName">Loading...</div>
                    </div>
                    <div>
                        <span class="badge bg-success" id="subscriptionStatus">Active</span>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <a href="<?= site_url('subscriptions/upgrade') ?>" class="btn btn-outline-primary w-100">
                                <i class="fa-solid fa-arrow-up-right-dots me-2"></i>Upgrade Plan
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="cancelSubscription()">
                                <i class="fa-solid fa-ban me-2"></i>Cancel Subscription
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            async function loadSubscription() {
                const r = await fetch('<?= site_url('subscriptions/current') ?>');
                const d = await r.json();
                if (d.success) {
                    document.getElementById('currentPlanName').textContent = d.package.name;
                    document.getElementById('subscriptionStatus').textContent = d.subscription.status;
                }
            }
            async function cancelSubscription() {
                if (confirm('Are you sure you want to cancel your subscription? This will disable premium features.')) {
                    const r = await fetch('<?= site_url('subscriptions/cancel') ?>', { method: 'POST' });
                    const d = await r.json();
                    alert(d.message);
                    location.reload();
                }
            }
            loadSubscription();
        </script>
        <?php endif; ?>

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
