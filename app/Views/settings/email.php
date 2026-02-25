<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-envelope text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Email / SMTP Settings</h5>
        <small class="text-muted">Configure outgoing email delivery</small>
    </div>
</div>

<?= form_open('settings/save_email_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="email">

<div class="settings-section-hdr">Sender Identity</div>
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="email_sent_from_address" class="form-label">From Email Address</label>
        <input type="email" name="email_sent_from_address" id="email_sent_from_address"
               class="form-control" value="<?= esc(setting('email_sent_from_address', '')) ?>"
               placeholder="no-reply@example.com">
    </div>
    <div class="col-md-6">
        <label for="email_sent_from_name" class="form-label">From Name</label>
        <input type="text" name="email_sent_from_name" id="email_sent_from_name"
               class="form-control" value="<?= esc(setting('email_sent_from_name', '')) ?>"
               placeholder="My Application">
    </div>
</div>

<!-- Protocol tabs -->
<div class="settings-section-hdr">Protocol</div>
<div class="mb-3">
    <div class="btn-group" role="group" id="protocolGroup">
        <?php foreach(['smtp'=>'SMTP','sendmail'=>'Sendmail','mail'=>'PHP Mail'] as $val=>$lbl): ?>
        <input type="radio" class="btn-check" name="email_protocol" id="proto_<?= $val ?>"
               value="<?= $val ?>" <?= setting('email_protocol')==$val ? 'checked' : '' ?>>
        <label class="btn btn-outline-primary btn-sm" for="proto_<?= $val ?>"><?= $lbl ?></label>
        <?php endforeach; ?>
    </div>
</div>

<div class="row g-3 mb-3" id="smtpFields">
    <div class="col-md-6">
        <label for="email_smtp_host" class="form-label">SMTP Host</label>
        <input type="text" name="email_smtp_host" id="email_smtp_host"
               class="form-control" value="<?= esc(setting('email_smtp_host', '')) ?>"
               placeholder="smtp.gmail.com">
    </div>
    <div class="col-md-3">
        <label for="email_smtp_port" class="form-label">SMTP Port</label>
        <input type="number" name="email_smtp_port" id="email_smtp_port"
               class="form-control" value="<?= esc(setting('email_smtp_port', '587')) ?>"
               placeholder="587">
    </div>
    <div class="col-md-3">
        <label for="email_smtp_security_type" class="form-label">Encryption</label>
        <select name="email_smtp_security_type" id="email_smtp_security_type" class="form-select">
            <option value=""    <?= setting('email_smtp_security_type')==''     ? 'selected':'' ?>>None</option>
            <option value="tls" <?= setting('email_smtp_security_type')=='tls'  ? 'selected':'' ?>>TLS</option>
            <option value="ssl" <?= setting('email_smtp_security_type')=='ssl'  ? 'selected':'' ?>>SSL</option>
        </select>
    </div>
    <div class="col-md-6">
        <label for="email_smtp_user" class="form-label">SMTP Username</label>
        <input type="text" name="email_smtp_user" id="email_smtp_user"
               class="form-control" value="<?= esc(setting('email_smtp_user', '')) ?>"
               autocomplete="username" placeholder="user@example.com">
    </div>
    <div class="col-md-6">
        <label for="email_smtp_pass" class="form-label">SMTP Password</label>
        <div class="input-group">
            <input type="password" name="email_smtp_pass" id="email_smtp_pass"
                   class="form-control" value="<?= esc(setting('email_smtp_pass', '')) ?>"
                   autocomplete="current-password" placeholder="••••••••">
            <button class="btn btn-outline-secondary" type="button" id="toggleSmtpPass"
                    onclick="const i=document.getElementById('email_smtp_pass');i.type=i.type==='password'?'text':'password'">
                <i class="fa-solid fa-eye"></i>
            </button>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save Email Settings
    </button>
</div>
<?= form_close() ?>

<style>
.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
</style>
<?= $this->endSection() ?>
