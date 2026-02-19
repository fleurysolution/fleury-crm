<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">Email Settings</h4>

<?= form_open('settings/save', ['class' => 'general-form']) ?>
<input type="hidden" name="setting_group" value="email">

<div class="row">

    <div class="col-md-6 mb-3">
        <label class="form-label">Email Protocol</label>
        <select name="email_protocol" class="form-select">
            <option value="smtp" <?= setting('email_protocol') == 'smtp' ? 'selected' : '' ?>>SMTP</option>
            <option value="microsoft_outlook" <?= setting('email_protocol') == 'microsoft_outlook' ? 'selected' : '' ?>>Microsoft Outlook</option>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Email Sent From Address</label>
        <input type="email" name="email_sent_from_address" class="form-control" value="<?= esc(setting('email_sent_from_address')) ?>" placeholder="noreply@example.com">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Email Sent From Name</label>
        <input type="text" name="email_sent_from_name" class="form-control" value="<?= esc(setting('email_sent_from_name')) ?>" placeholder="Company Name">
    </div>

    <div class="col-md-12">
        <div class="card bg-light border-0 mb-3">
            <div class="card-body">
                <h6 class="card-title text-muted mb-3">SMTP Configuration</h6>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">SMTP Host</label>
                        <input type="text" name="email_smtp_host" class="form-control" value="<?= esc(setting('email_smtp_host')) ?>" placeholder="smtp.mailtrap.io">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">SMTP Port</label>
                        <input type="number" name="email_smtp_port" class="form-control" value="<?= esc(setting('email_smtp_port', '587')) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">SMTP Username</label>
                        <input type="text" name="email_smtp_user" class="form-control" value="<?= esc(setting('email_smtp_user')) ?>">
                    </div>
                     <div class="col-md-6 mb-3">
                        <label class="form-label">SMTP Password</label>
                        <input type="password" name="email_smtp_pass" class="form-control" value="<?= esc(setting('email_smtp_pass')) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Security Type</label>
                        <select name="email_smtp_security_type" class="form-select">
                            <option value="tls" <?= setting('email_smtp_security_type') == 'tls' ? 'selected' : '' ?>>TLS</option>
                            <option value="ssl" <?= setting('email_smtp_security_type') == 'ssl' ? 'selected' : '' ?>>SSL</option>
                            <option value="" <?= setting('email_smtp_security_type') == '' ? 'selected' : '' ?>>None</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
     <div class="col-md-12">
        <div class="card bg-light border-0 mb-3">
            <div class="card-body">
                <h6 class="card-title text-muted mb-3">Microsoft Outlook Configuration</h6>
                 <div class="mb-3">
                    <label class="form-label">Client ID</label>
                    <input type="text" name="outlook_smtp_client_id" class="form-control" value="<?= esc(setting('outlook_smtp_client_id')) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Client Secret</label>
                    <input type="text" name="outlook_smtp_client_secret" class="form-control" value="<?= esc(setting('outlook_smtp_client_secret')) ?>">
                </div>
            </div>
        </div>
    </div>

</div>

<div class="d-flex justify-content-between mt-3">
    <button type="button" class="btn btn-outline-secondary">Send Test Email</button>
    <button type="submit" class="btn btn-primary">Save Changes</button>
</div>

<?= form_close() ?>

<?= $this->endSection() ?>
