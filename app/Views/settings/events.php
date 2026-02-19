<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">Event Settings</h4>

<?= form_open('settings/save', ['class' => 'general-form']) ?>
<input type="hidden" name="setting_group" value="event">

<div class="row">
   <div class="col-md-12 mb-3">
        <div class="form-check form-switch">
             <input type="hidden" name="enable_google_calendar_api" value="0">
            <input class="form-check-input" type="checkbox" id="enable_google_calendar_api" name="enable_google_calendar_api" value="1" <?= setting('enable_google_calendar_api') ? 'checked' : '' ?>>
            <label class="form-check-label fw-bold" for="enable_google_calendar_api">Enable Google Calendar API?</label>
        </div>
    </div>
    
     <div class="col-md-12 mb-3">
        <label class="form-label">Google Calendar Client ID</label>
        <input type="text" name="google_calendar_client_id" class="form-control" value="<?= esc(setting('google_calendar_client_id')) ?>">
    </div>
    
     <div class="col-md-12 mb-3">
        <label class="form-label">Google Calendar Client Secret</label>
        <input type="text" name="google_calendar_client_secret" class="form-control" value="<?= esc(setting('google_calendar_client_secret')) ?>">
    </div>
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
    <button type="submit" class="btn btn-primary">Save Changes</button>
</div>

<?= form_close() ?>

<?= $this->endSection() ?>
