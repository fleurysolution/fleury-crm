<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">Notification Settings</h4>

<div class="alert alert-info">
    <i class="fa-solid fa-info-circle me-2"></i> Advanced notification settings (per specific event) will be available in the next update.
</div>

<?= form_open('settings/save', ['class' => 'general-form']) ?>
<input type="hidden" name="setting_group" value="notification">

<div class="row">
    <div class="col-md-12 mb-3">
        <label class="form-label mb-3 fw-bold">Global Settings</label>
        
        <div class="form-check form-switch mb-3">
             <input type="hidden" name="enable_email_notification" value="0">
            <input class="form-check-input" type="checkbox" id="enable_email_notification" name="enable_email_notification" value="1" <?= setting('enable_email_notification') ? 'checked' : '' ?>>
            <label class="form-check-label" for="enable_email_notification">Enable Email Notifications</label>
        </div>
        
         <div class="form-check form-switch mb-3">
             <input type="hidden" name="enable_web_notification" value="0">
            <input class="form-check-input" type="checkbox" id="enable_web_notification" name="enable_web_notification" value="1" <?= setting('enable_web_notification') ? 'checked' : '' ?>>
            <label class="form-check-label" for="enable_web_notification">Enable Web Notifications</label>
        </div>
        
        <div class="form-check form-switch mb-3">
             <input type="hidden" name="enable_push_notification" value="0">
            <input class="form-check-input" type="checkbox" id="enable_push_notification" name="enable_push_notification" value="1" <?= setting('enable_push_notification') ? 'checked' : '' ?>>
            <label class="form-check-label" for="enable_push_notification">Enable Push Notifications (Pusher)</label>
        </div>
    </div>
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
    <button type="submit" class="btn btn-primary">Save Changes</button>
</div>

<?= form_close() ?>

<?= $this->endSection() ?>
