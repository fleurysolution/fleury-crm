<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">General Settings</h4>

<?= form_open_multipart('settings/save', ['class' => 'general-form']) ?>
<input type="hidden" name="setting_group" value="general">

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="app_title" class="form-label">App Title</label>
        <input type="text" name="app_title" id="app_title" class="form-control" value="<?= esc(setting('app_title', 'BPMS247')) ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label for="company_email" class="form-label">Company Email</label>
        <input type="email" name="company_email" id="company_email" class="form-control" value="<?= esc(setting('company_email')) ?>">
    </div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label">Site Logo</label>
        <div class="d-flex align-items-center gap-3">
            <?php if(setting('site_logo')): ?>
                <div class="p-2 border rounded bg-light">
                    <img src="<?= base_url('files/system/' . setting('site_logo')) ?>" alt="Site Logo" style="max-height: 50px;">
                </div>
            <?php endif; ?>
            <input type="file" name="site_logo" class="form-control">
        </div>
        <small class="text-muted">Upload a PNG or JPG image.</small>
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Favicon</label>
        <div class="d-flex align-items-center gap-3">
            <?php if(setting('favicon')): ?>
                <div class="p-2 border rounded bg-light">
                    <img src="<?= base_url('files/system/' . setting('favicon')) ?>" alt="Favicon" style="max-height: 32px;">
                </div>
            <?php endif; ?>
            <input type="file" name="favicon" class="form-control">
        </div>
        <small class="text-muted">Upload a 32x32 PNG image.</small>
    </div>

    <div class="col-md-6 mb-3">
        <label for="rows_per_page" class="form-label">Rows Per Page</label>
        <select name="rows_per_page" id="rows_per_page" class="form-select">
            <option value="10" <?= setting('rows_per_page') == '10' ? 'selected' : '' ?>>10</option>
            <option value="25" <?= setting('rows_per_page') == '25' ? 'selected' : '' ?>>25</option>
            <option value="50" <?= setting('rows_per_page') == '50' ? 'selected' : '' ?>>50</option>
            <option value="100" <?= setting('rows_per_page') == '100' ? 'selected' : '' ?>>100</option>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label for="default_language" class="form-label">Default Language</label>
        <select name="default_language" id="default_language" class="form-select">
            <option value="english" <?= setting('default_language') == 'english' ? 'selected' : '' ?>>English</option>
            <option value="spanish" <?= setting('default_language') == 'spanish' ? 'selected' : '' ?>>Spanish</option>
            <option value="french" <?= setting('default_language') == 'french' ? 'selected' : '' ?>>French</option>
        </select>
    </div>
    
    <div class="col-md-6 mb-3">
        <label for="timezone" class="form-label">Timezone</label>
        <select name="timezone" id="timezone" class="form-select">
            <option value="UTC" <?= setting('timezone') == 'UTC' ? 'selected' : '' ?>>UTC</option>
             <?php 
                $zones = timezone_identifiers_list();
                foreach($zones as $zone): 
            ?>
                <option value="<?= $zone ?>" <?= setting('timezone') == $zone ? 'selected' : '' ?>><?= $zone ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
     <div class="col-md-6 mb-3">
        <label for="date_format" class="form-label">Date Format</label>
        <select name="date_format" id="date_format" class="form-select">
            <option value="Y-m-d" <?= setting('date_format') == 'Y-m-d' ? 'selected' : '' ?>>Y-m-d (2024-12-31)</option>
            <option value="d-m-Y" <?= setting('date_format') == 'd-m-Y' ? 'selected' : '' ?>>d-m-Y (31-12-2024)</option>
            <option value="m/d/Y" <?= setting('date_format') == 'm/d/Y' ? 'selected' : '' ?>>m/d/Y (12/31/2024)</option>
        </select>
    </div>

    <div class="col-md-12 mb-3">
        <label for="accepted_file_formats" class="form-label">Accepted File Formats</label>
        <input type="text" name="accepted_file_formats" id="accepted_file_formats" class="form-control" value="<?= esc(setting('accepted_file_formats', 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,zip')) ?>">
        <small class="text-muted">Comma separated. Example: jpg,png,pdf</small>
    </div>
    
    <div class="col-md-12 mb-3">
        <div class="form-check form-switch">
             <input type="hidden" name="show_background_image_in_signin_page" value="0">
            <input class="form-check-input" type="checkbox" id="show_background_image_in_signin_page" name="show_background_image_in_signin_page" value="1" <?= setting('show_background_image_in_signin_page') ? 'checked' : '' ?>>
            <label class="form-check-label" for="show_background_image_in_signin_page">Show background image in Header?</label>
        </div>
        
         <div class="form-check form-switch mt-2">
             <input type="hidden" name="show_logo_in_signin_page" value="0">
            <input class="form-check-input" type="checkbox" id="show_logo_in_signin_page" name="show_logo_in_signin_page" value="1" <?= setting('show_logo_in_signin_page') ? 'checked' : '' ?>>
            <label class="form-check-label" for="show_logo_in_signin_page">Show logo in Signin Page?</label>
        </div>
        
        <div class="form-check form-switch mt-2">
             <input type="hidden" name="enable_rich_text_editor" value="0">
            <input class="form-check-input" type="checkbox" id="enable_rich_text_editor" name="enable_rich_text_editor" value="1" <?= setting('enable_rich_text_editor') ? 'checked' : '' ?>>
            <label class="form-check-label" for="enable_rich_text_editor">Enable Rich Text Editor?</label>
        </div>
    </div>
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
    <button type="submit" class="btn btn-primary">Save Changes</button>
</div>

<?= form_close() ?>

<?= $this->endSection() ?>
