<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-ban text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">IP Restriction</h5>
        <small class="text-muted">Restrict access to specific IP addresses</small>
    </div>
</div>

<?= form_open('settings/save_ip_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="security">

<div class="alert alert-warning border-0 shadow-sm mb-4" style="border-radius:10px;">
    <i class="fa-solid fa-triangle-exclamation me-2"></i>
    <strong>Caution:</strong> If you save an IP address that doesn't include your own current IP, you may lock yourself out.
</div>

<div class="mb-3">
    <label for="allowed_ip_addresses" class="form-label">Allowed IP Addresses</label>
    <textarea name="allowed_ip_addresses" id="allowed_ip_addresses"
              class="form-control font-monospace" rows="6"
              placeholder="One IP per line. Leave blank to allow all."><?= esc(setting('allowed_ip_addresses','')) ?></textarea>
    <div class="form-text">Enter one IP address per line. Leave empty to allow access from all IPs.</div>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save IP Settings
    </button>
</div>
<?= form_close() ?>

<style>.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;}</style>
<?= $this->endSection() ?>
