<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge bg-primary bg-opacity-10 text-primary">
        <i class="fa-solid fa-shield-halved fa-lg"></i>
    </div>
    <div>
        <h5 class="fw-bold mb-0">Cybersecurity Hub</h5>
        <small class="text-muted">Manage your identity protection and security settings</small>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 bg-light rounded-4 p-3 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="fa-solid fa-key text-warning"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Two-Factor Authentication (2FA)</h6>
                        <small class="text-muted">Add an extra layer of security to your account</small>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input ms-0" type="checkbox" id="mfaToggle" 
                           style="width: 40px; height: 20px;" 
                           <?= $user['mfa_enabled'] ? 'checked' : '' ?>>
                </div>
            </div>

            <?php if (!$user['mfa_enabled']): ?>
                <div id="mfaSetupSection" style="display: <?= $tempSecret ? 'block' : 'none' ?>;">
                    <hr class="my-3 opacity-10">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center">
                            <?php if ($qrCodeUrl): ?>
                                <img src="<?= $qrCodeUrl ?>" class="img-fluid rounded border" alt="QR Code">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <p class="small text-dark fw-semibold mb-2">1. Scan the QR code with your authenticator app</p>
                            <p class="small text-muted mb-3">Don't have an app? Download Google Authenticator or Microsoft Authenticator.</p>
                            
                            <p class="small text-dark fw-semibold mb-2">2. Enter the 6-digit code to verify</p>
                            <div class="d-flex gap-2">
                                <input type="text" id="mfaCodeInput" class="form-control" placeholder="000000" maxlength="6" style="letter-spacing: 2px;">
                                <button type="button" id="btnVerifyMfa" class="btn btn-primary px-4 rounded-3">Verify</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="mfaInactiveMsg" class="alert alert-info border-0 py-2 small mb-0" style="display: <?= $tempSecret ? 'none' : 'block' ?>;">
                    <i class="fa-solid fa-circle-info me-2"></i> Toggle the switch to begin setting up MFA.
                </div>
            <?php else: ?>
                <div class="alert alert-success border-0 py-2 small mb-0">
                    <i class="fa-solid fa-circle-check me-2"></i> MFA is currently active and protecting your account.
                </div>
            <?php endif; ?>
        </div>

        <div class="card border-0 bg-light rounded-4 p-3">
            <h6 class="fw-bold mb-3">Session Security</h6>
            <div class="toggle-list rounded-3 border-0">
                <div class="toggle-row py-2">
                    <div class="toggle-label">
                        <strong class="small">IP Binding</strong>
                        <small class="xsmall">Lock session to your current IP address</small>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" checked disabled>
                    </div>
                </div>
                <div class="toggle-row py-2 border-0">
                    <div class="toggle-label">
                        <strong class="small">Secure Browsing</strong>
                        <small class="xsmall">Force encrypted connection (HTTPS) and CSP</small>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" checked disabled>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                <span class="badge bg-success bg-opacity-10 text-success small fw-medium">
                    <i class="fa-solid fa-lock me-1"></i> System Hardening: Active
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold mb-0">Recent Security Activity</h6>
            </div>
            <div class="card-body px-4">
                <div id="securityLogTimeline" class="small">
                    <!-- Activity will be loaded here -->
                    <p class="text-muted text-center py-5">Loading activity...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .xsmall { font-size: 0.7rem; }
    .settings-icon-badge { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mfaToggle = document.getElementById('mfaToggle');
    const mfaSetup = document.getElementById('mfaSetupSection');
    const mfaInactive = document.getElementById('mfaInactiveMsg');
    
    mfaToggle.addEventListener('change', function() {
        if (this.checked) {
            <?php if (!$user['mfa_enabled']): ?>
            // Initial setup
            fetch('<?= site_url('settings/setup_mfa') ?>', { method: 'POST' })
            .then(r => r.json())
            .then(d => {
                if (d.success) location.reload();
            });
            <?php endif; ?>
        } else {
            if (confirm('Are you sure you want to disable MFA? This will reduce your account security.')) {
                fetch('<?= site_url('settings/disable_mfa') ?>', { method: 'POST' })
                .then(r => r.json())
                .then(d => {
                    settingsToast(d.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                });
            } else {
                this.checked = true;
            }
        }
    });

    const btnVerify = document.getElementById('btnVerifyMfa');
    if (btnVerify) {
        btnVerify.addEventListener('click', function() {
            const code = document.getElementById('mfaCodeInput').value;
            if (code.length !== 6) return settingsToast('Please enter a 6-digit code.', 'error');

            const fd = new FormData();
            fd.append('code', code);

            fetch('<?= site_url('settings/verify_mfa_setup') ?>', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    settingsToast(d.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    settingsToast(d.message, 'error');
                }
            });
        });
    }

    function loadSecurityLogs() {
        const container = document.getElementById('securityLogTimeline');
        fetch('<?= site_url('settings/security_log_data') ?>')
        .then(r => r.json())
        .then(d => {
            container.innerHTML = d.html;
        });
    }
    loadSecurityLogs();
});
</script>

<?= $this->endSection() ?>
