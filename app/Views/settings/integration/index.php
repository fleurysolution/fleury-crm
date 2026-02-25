<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-plug text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Integrations</h5>
        <small class="text-muted">Third-party API keys and webhook configuration</small>
    </div>
</div>

<?= form_open('settings/save_integration_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="integration">

<!-- Google -->
<div class="settings-section-hdr">Google</div>
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="google_recaptcha_site_key" class="form-label">reCAPTCHA Site Key</label>
        <input type="text" name="google_recaptcha_site_key" id="google_recaptcha_site_key"
               class="form-control font-monospace" value="<?= esc(setting('google_recaptcha_site_key','')) ?>"
               placeholder="6Le…">
    </div>
    <div class="col-md-6">
        <label for="google_recaptcha_secret_key" class="form-label">reCAPTCHA Secret Key</label>
        <input type="password" name="google_recaptcha_secret_key" id="google_recaptcha_secret_key"
               class="form-control font-monospace" value="<?= esc(setting('google_recaptcha_secret_key','')) ?>"
               placeholder="6Le… (secret)">
    </div>
    <div class="col-md-6">
        <div class="form-check form-switch">
            <input type="hidden" name="google_recaptcha_enabled" value="0">
            <input class="form-check-input" type="checkbox" name="google_recaptcha_enabled"
                   id="recaptcha_enabled" value="1"
                   <?= setting('google_recaptcha_enabled') ? 'checked':'' ?>>
            <label class="form-check-label" for="recaptcha_enabled">Enable reCAPTCHA on public forms</label>
        </div>
    </div>
</div>

<!-- Slack -->
<div class="settings-section-hdr">Slack</div>
<div class="row g-3 mb-3">
    <div class="col-12">
        <label for="slack_webhook_url" class="form-label">Webhook URL</label>
        <input type="url" name="slack_webhook_url" id="slack_webhook_url"
               class="form-control font-monospace" value="<?= esc(setting('slack_webhook_url','')) ?>"
               placeholder="https://hooks.slack.com/services/…">
    </div>
    <div class="col-md-4">
        <label for="slack_channel" class="form-label">Channel</label>
        <input type="text" name="slack_channel" id="slack_channel"
               class="form-control" value="<?= esc(setting('slack_channel','#general')) ?>"
               placeholder="#general">
    </div>
    <div class="col-md-4">
        <label for="slack_username" class="form-label">Bot Name</label>
        <input type="text" name="slack_username" id="slack_username"
               class="form-control" value="<?= esc(setting('slack_username','BPMS Bot')) ?>">
    </div>
</div>

<!-- Pusher -->
<div class="settings-section-hdr">Pusher (Real-time)</div>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="pusher_app_id" class="form-label">App ID</label>
        <input type="text" name="pusher_app_id" id="pusher_app_id"
               class="form-control" value="<?= esc(setting('pusher_app_id','')) ?>">
    </div>
    <div class="col-md-4">
        <label for="pusher_key" class="form-label">App Key</label>
        <input type="text" name="pusher_key" id="pusher_key"
               class="form-control font-monospace" value="<?= esc(setting('pusher_key','')) ?>">
    </div>
    <div class="col-md-4">
        <label for="pusher_secret" class="form-label">App Secret</label>
        <input type="password" name="pusher_secret" id="pusher_secret"
               class="form-control font-monospace" value="<?= esc(setting('pusher_secret','')) ?>">
    </div>
    <div class="col-md-4">
        <label for="pusher_cluster" class="form-label">Cluster</label>
        <input type="text" name="pusher_cluster" id="pusher_cluster"
               class="form-control" value="<?= esc(setting('pusher_cluster','mt1')) ?>"
               placeholder="mt1">
    </div>
</div>

<!-- Stripe -->
<div class="settings-section-hdr">Stripe</div>
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="stripe_public_key" class="form-label">Publishable Key</label>
        <input type="text" name="stripe_public_key" id="stripe_public_key"
               class="form-control font-monospace" value="<?= esc(setting('stripe_public_key','')) ?>"
               placeholder="pk_live_…">
    </div>
    <div class="col-md-6">
        <label for="stripe_secret_key" class="form-label">Secret Key</label>
        <input type="password" name="stripe_secret_key" id="stripe_secret_key"
               class="form-control font-monospace" value="<?= esc(setting('stripe_secret_key','')) ?>"
               placeholder="sk_live_…">
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input type="hidden" name="stripe_enabled" value="0">
            <input class="form-check-input" type="checkbox" name="stripe_enabled"
                   id="stripe_enabled" value="1"
                   <?= setting('stripe_enabled') ? 'checked':'' ?>>
            <label class="form-check-label" for="stripe_enabled">Enable Stripe payments</label>
        </div>
    </div>
</div>

<!-- PayPal -->
<div class="settings-section-hdr">PayPal</div>
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="paypal_client_id" class="form-label">Client ID</label>
        <input type="text" name="paypal_client_id" id="paypal_client_id"
               class="form-control font-monospace" value="<?= esc(setting('paypal_client_id','')) ?>">
    </div>
    <div class="col-md-6">
        <label for="paypal_secret" class="form-label">Secret</label>
        <input type="password" name="paypal_secret" id="paypal_secret"
               class="form-control font-monospace" value="<?= esc(setting('paypal_secret','')) ?>">
    </div>
    <div class="col-md-4">
        <label for="paypal_mode" class="form-label">Mode</label>
        <select name="paypal_mode" id="paypal_mode" class="form-select">
            <option value="sandbox" <?= setting('paypal_mode')=='sandbox' ? 'selected':'' ?>>Sandbox (Testing)</option>
            <option value="live"    <?= setting('paypal_mode')=='live'    ? 'selected':'' ?>>Live</option>
        </select>
    </div>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save Integration Settings
    </button>
</div>
<?= form_close() ?>

<style>.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;} .font-monospace{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;font-size:.82rem!important;}</style>
<?= $this->endSection() ?>
