<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-align-center text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Footer Settings</h5>
        <small class="text-muted">Customise the application's footer area</small>
    </div>
</div>

<?= form_open('settings/save_footer_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="footer">

<div class="settings-section-hdr">Footer Content</div>
<div class="mb-3">
    <label for="footer_text" class="form-label">Footer Text / Copyright</label>
    <input type="text" name="footer_text" id="footer_text" class="form-control"
           value="<?= esc(setting('footer_text','© 2024 BPMS247. All rights reserved.')) ?>"
           placeholder="© 2024 Your Company Name">
</div>
<div class="mb-3">
    <label for="footer_links" class="form-label">Footer Links (HTML allowed)</label>
    <textarea name="footer_links" id="footer_links" class="form-control font-monospace" rows="4"><?= esc(setting('footer_links','')) ?></textarea>
    <div class="form-text">Example: <code>&lt;a href="https://example.com/privacy"&gt;Privacy Policy&lt;/a&gt;</code></div>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save"><i class="fa-solid fa-floppy-disk me-2"></i>Save Footer Settings</button>
</div>
<?= form_close() ?>

<style>.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;}</style>
<?= $this->endSection() ?>
