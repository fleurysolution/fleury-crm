<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-building text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">General Settings</h5>
        <small class="text-muted">Configure your application's core settings</small>
    </div>
</div>

<?= form_open_multipart('settings/save_general_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="general">

<div class="settings-section-hdr">Application Identity</div>
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="app_title" class="form-label">Application Title</label>
        <input type="text" name="app_title" id="app_title" class="form-control"
               value="<?= esc(setting('app_title', 'BPMS247')) ?>"
               placeholder="e.g. My CRM">
    </div>
    <div class="col-md-6">
        <label for="company_email" class="form-label">Company Email</label>
        <input type="email" name="company_email" id="company_email" class="form-control"
               value="<?= esc(setting('company_email', '')) ?>"
               placeholder="info@example.com">
    </div>
    <div class="col-md-6">
        <label class="form-label">Site Logo</label>
        <?php if(setting('site_logo')): ?>
        <div class="mb-2 p-2 border rounded bg-light d-inline-block">
            <img src="<?= base_url('files/system/' . setting('site_logo')) ?>" alt="Logo" style="max-height:48px;">
        </div>
        <?php endif; ?>
        <input type="file" name="site_logo" class="form-control" accept="image/*">
        <div class="form-text">PNG or JPG recommended.</div>
    </div>
    <div class="col-md-6">
        <label class="form-label">Favicon</label>
        <?php if(setting('favicon')): ?>
        <div class="mb-2 p-2 border rounded bg-light d-inline-block">
            <img src="<?= base_url('files/system/' . setting('favicon')) ?>" alt="Favicon" style="max-height:32px;">
        </div>
        <?php endif; ?>
        <input type="file" name="favicon" class="form-control" accept="image/*">
        <div class="form-text">Recommended: 32×32 PNG.</div>
    </div>
</div>

<div class="settings-section-hdr">Display Preferences</div>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="rows_per_page" class="form-label">Rows Per Page</label>
        <select name="rows_per_page" id="rows_per_page" class="form-select">
            <?php foreach ([10,25,50,100] as $n): ?>
            <option value="<?= $n ?>" <?= setting('rows_per_page')==$n ? 'selected' : '' ?>><?= $n ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label for="default_language" class="form-label">Default Language</label>
        <select name="default_language" id="default_language" class="form-select">
            <option value="english" <?= setting('default_language')=='english' ? 'selected' : '' ?>>English</option>
            <option value="spanish" <?= setting('default_language')=='spanish' ? 'selected' : '' ?>>Spanish</option>
            <option value="french"  <?= setting('default_language')=='french'  ? 'selected' : '' ?>>French</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="accepted_file_formats" class="form-label">Accepted File Formats</label>
        <input type="text" name="accepted_file_formats" id="accepted_file_formats" class="form-control"
               value="<?= esc(setting('accepted_file_formats', 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,zip')) ?>">
        <div class="form-text">Comma-separated extensions.</div>
    </div>
</div>

<div class="settings-section-hdr">UI Options</div>
<div class="toggle-list">
    <div class="toggle-row">
        <div class="toggle-label">
            <strong>Show Background Image on Sign-in</strong>
            <small>Display a background image on the login page</small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="show_background_image_in_signin_page" value="0">
            <input class="form-check-input" type="checkbox" id="show_bg"
                   name="show_background_image_in_signin_page" value="1"
                   <?= setting('show_background_image_in_signin_page') ? 'checked' : '' ?>>
        </div>
    </div>
    <div class="toggle-row">
        <div class="toggle-label">
            <strong>Show Logo on Sign-in Page</strong>
            <small>Display your logo above the login form</small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="show_logo_in_signin_page" value="0">
            <input class="form-check-input" type="checkbox" id="show_logo"
                   name="show_logo_in_signin_page" value="1"
                   <?= setting('show_logo_in_signin_page') ? 'checked' : '' ?>>
        </div>
    </div>
    <div class="toggle-row">
        <div class="toggle-label">
            <strong>Enable Rich Text Editor</strong>
            <small>Use TinyMCE/WYSIWYG editors in text areas</small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="enable_rich_text_editor" value="0">
            <input class="form-check-input" type="checkbox" id="rich_text"
                   name="enable_rich_text_editor" value="1"
                   <?= setting('enable_rich_text_editor') ? 'checked' : '' ?>>
        </div>
    </div>
    <div class="toggle-row">
        <div class="toggle-label">
            <strong>Custom Scrollbar</strong>
            <small>Enable styled scrollbars throughout the app</small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="scrollbar" value="0">
            <input class="form-check-input" type="checkbox" id="scrollbar"
                   name="scrollbar" value="1"
                   <?= setting('scrollbar') ? 'checked' : '' ?>>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save General Settings
    </button>
</div>
<?= form_close() ?>

<style>
.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.toggle-list{border:1.5px solid #e5e7eb;border-radius:10px;padding:0 1rem;background:#fff;}
</style>
<?= $this->endSection() ?>
