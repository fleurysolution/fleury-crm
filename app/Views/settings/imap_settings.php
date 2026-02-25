<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-inbox text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">IMAP / Email Piping</h5>
        <small class="text-muted">Automatically create tickets from incoming emails</small>
    </div>
</div>

<?= form_open('settings/save_imap_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="imap">

<div class="settings-section-hdr">IMAP Connection</div>
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="imap_host" class="form-label">IMAP Server</label>
        <input type="text" name="imap_host" id="imap_host" class="form-control"
               value="<?= esc(setting('imap_host','')) ?>" placeholder="mail.example.com">
    </div>
    <div class="col-md-3">
        <label for="imap_port" class="form-label">Port</label>
        <input type="number" name="imap_port" id="imap_port" class="form-control"
               value="<?= esc(setting('imap_port','993')) ?>" placeholder="993">
    </div>
    <div class="col-md-3">
        <label for="imap_encryption" class="form-label">Encryption</label>
        <select name="imap_encryption" id="imap_encryption" class="form-select">
            <option value="ssl"  <?= setting('imap_encryption')=='ssl'  ? 'selected':'' ?>>SSL</option>
            <option value="tls"  <?= setting('imap_encryption')=='tls'  ? 'selected':'' ?>>TLS</option>
            <option value="none" <?= setting('imap_encryption')=='none' ? 'selected':'' ?>>None</option>
        </select>
    </div>
    <div class="col-md-6">
        <label for="imap_username" class="form-label">Username / Email</label>
        <input type="text" name="imap_username" id="imap_username" class="form-control"
               value="<?= esc(setting('imap_username','')) ?>" placeholder="support@example.com">
    </div>
    <div class="col-md-6">
        <label for="imap_password" class="form-label">Password</label>
        <input type="password" name="imap_password" id="imap_password" class="form-control"
               value="<?= esc(setting('imap_password','')) ?>">
    </div>
    <div class="col-md-6">
        <label for="imap_folder" class="form-label">Mailbox Folder</label>
        <input type="text" name="imap_folder" id="imap_folder" class="form-control"
               value="<?= esc(setting('imap_folder','INBOX')) ?>" placeholder="INBOX">
    </div>
</div>

<div class="settings-section-hdr">Options</div>
<div class="border rounded-3 px-3 py-1 bg-white mb-3">
    <div class="toggle-row">
        <div class="toggle-label">
            <strong>Enable Email Piping</strong>
            <small>Automatically create support tickets from incoming emails</small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="enable_email_piping" value="0">
            <input class="form-check-input" type="checkbox" name="enable_email_piping"
                   id="email_piping" value="1"
                   <?= setting('enable_email_piping') ? 'checked':'' ?>>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save"><i class="fa-solid fa-floppy-disk me-2"></i>Save IMAP Settings</button>
</div>
<?= form_close() ?>

<style>
.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.toggle-row{display:flex;align-items:center;justify-content:space-between;padding:.8rem 0;border-bottom:1px solid #f3f4f6;}
.toggle-row:last-child{border-bottom:none;}
.toggle-label strong{font-size:.875rem;color:#374151;display:block;}
.toggle-label small{font-size:.775rem;color:#6b7280;}
.form-switch .form-check-input{width:2.5em;height:1.35em;cursor:pointer;}
</style>
<?= $this->endSection() ?>
