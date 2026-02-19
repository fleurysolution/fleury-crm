<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">IP Restriction</h4>

<?= form_open('settings/save', ['class' => 'general-form']) ?>
<input type="hidden" name="setting_group" value="ip_restriction">

<div class="row">
     <div class="col-md-12 mb-3">
        <label class="form-label">Allowed IP Addresses</label>
        <textarea name="allowed_ip_addresses" class="form-control" rows="5" placeholder="Enter each IP address in a new line."><?= esc(setting('allowed_ip_addresses')) ?></textarea>
        <small class="text-muted">Enter each IP address in a new line. Keep it blank to allow all IPs.</small>
    </div>
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
    <button type="submit" class="btn btn-primary">Save Changes</button>
</div>

<?= form_close() ?>

<?= $this->endSection() ?>
