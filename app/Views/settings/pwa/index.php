<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-mobile-screen text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Progressive Web App (PWA)</h5>
        <small class="text-muted">Theme colours for the installable app experience</small>
    </div>
</div>

<?= form_open('settings/save_pwa_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="pwa">

<div class="settings-section-hdr">PWA Theme</div>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="pwa_theme_color" class="form-label">Theme Colour</label>
        <div class="d-flex align-items-center gap-2">
            <input type="color" name="pwa_theme_color" id="pwa_theme_color"
                   class="form-control form-control-color"
                   value="<?= esc(setting('pwa_theme_color','#4a90e2')) ?>">
            <span class="form-text mb-0">Used in the browser UI on mobile.</span>
        </div>
    </div>
    <div class="col-md-4">
        <label for="pwa_bg_color" class="form-label">Background Colour</label>
        <div class="d-flex align-items-center gap-2">
            <input type="color" name="pwa_bg_color" id="pwa_bg_color"
                   class="form-control form-control-color"
                   value="<?= esc(setting('pwa_bg_color','#ffffff')) ?>">
            <span class="form-text mb-0">Splash screen background.</span>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save"><i class="fa-solid fa-floppy-disk me-2"></i>Save PWA Settings</button>
</div>
<?= form_close() ?>

<style>.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;}</style>
<?= $this->endSection() ?>
