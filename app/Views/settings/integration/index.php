<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">Integration Settings</h4>

<?= form_open('settings/save', ['class' => 'general-form']) ?>
<input type="hidden" name="setting_group" value="integration">

<ul class="nav nav-tabs mb-4" id="integrationTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="google-tab" data-bs-toggle="tab" data-bs-target="#google" type="button" role="tab">Google Drive</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pusher-tab" data-bs-toggle="tab" data-bs-target="#pusher" type="button" role="tab">Pusher</button>
    </li>
     <li class="nav-item" role="presentation">
        <button class="nav-link" id="recaptcha-tab" data-bs-toggle="tab" data-bs-target="#recaptcha" type="button" role="tab">ReCAPTCHA</button>
    </li>
</ul>

<div class="tab-content" id="integrationTabsContent">
    <!-- Google Drive -->
    <div class="tab-pane fade show active" id="google" role="tabpanel">
        <div class="form-check form-switch mb-3">
            <input type="hidden" name="enable_google_drive_api_to_upload_file" value="0">
            <input class="form-check-input" type="checkbox" id="enable_google_drive_api_to_upload_file" name="enable_google_drive_api_to_upload_file" value="1" <?= setting('enable_google_drive_api_to_upload_file') ? 'checked' : '' ?>>
            <label class="form-check-label fw-bold" for="enable_google_drive_api_to_upload_file">Enable Google Drive API</label>
        </div>
        <div class="mb-3">
            <label class="form-label">Client ID</label>
            <input type="text" name="google_drive_client_id" class="form-control" value="<?= esc(setting('google_drive_client_id')) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Client Secret</label>
             <input type="text" name="google_drive_client_secret" class="form-control" value="<?= esc(setting('google_drive_client_secret')) ?>">
        </div>
    </div>

    <!-- Pusher -->
    <div class="tab-pane fade" id="pusher" role="tabpanel">
        <div class="form-check form-switch mb-3">
             <input type="hidden" name="enable_push_notification" value="0">
            <input class="form-check-input" type="checkbox" id="enable_push_notification" name="enable_push_notification" value="1" <?= setting('enable_push_notification') ? 'checked' : '' ?>>
            <label class="form-check-label fw-bold" for="enable_push_notification">Enable Push Notification</label>
        </div>
        <div class="mb-3">
            <label class="form-label">App ID</label>
            <input type="text" name="pusher_app_id" class="form-control" value="<?= esc(setting('pusher_app_id')) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Key</label>
            <input type="text" name="pusher_key" class="form-control" value="<?= esc(setting('pusher_key')) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Secret</label>
            <input type="text" name="pusher_secret" class="form-control" value="<?= esc(setting('pusher_secret')) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Cluster</label>
            <input type="text" name="pusher_cluster" class="form-control" value="<?= esc(setting('pusher_cluster')) ?>">
        </div>
    </div>
    
    <!-- ReCAPTCHA -->
    <div class="tab-pane fade" id="recaptcha" role="tabpanel">
         <div class="mb-3">
            <label class="form-label">Site Key</label>
            <input type="text" name="re_captcha_site_key" class="form-control" value="<?= esc(setting('re_captcha_site_key')) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Secret Key</label>
            <input type="text" name="re_captcha_secret_key" class="form-control" value="<?= esc(setting('re_captcha_secret_key')) ?>">
        </div>
    </div>
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
    <button type="submit" class="btn btn-primary">Save Changes</button>
</div>

<?= form_close() ?>

<?= $this->endSection() ?>
