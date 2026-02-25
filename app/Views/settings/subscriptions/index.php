<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-rotate text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Subscription Settings</h5>
        <small class="text-muted">Subscription numbering and payment gateways</small>
    </div>
</div>

<?= form_open('settings/save_subscription_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="subscription">

<div class="settings-section-hdr">Numbering</div>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="subscription_prefix" class="form-label">Prefix</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
            <input type="text" name="subscription_prefix" id="subscription_prefix" class="form-control"
                   value="<?= esc(setting('subscription_prefix','SUB-')) ?>">
        </div>
    </div>
    <div class="col-md-4">
        <label for="initial_number_of_the_subscription" class="form-label">Starting Number</label>
        <input type="number" name="initial_number_of_the_subscription"
               id="initial_number_of_the_subscription" class="form-control"
               value="<?= esc(setting('initial_number_of_the_subscription','1001')) ?>" min="1">
    </div>
</div>

<div class="settings-section-hdr">Payment</div>
<div class="border rounded-3 px-3 py-1 bg-white">
    <div class="toggle-row">
        <div class="toggle-label">
            <strong>Enable Stripe Subscriptions</strong>
            <small>Allow automatic recurring billing via Stripe</small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="enable_stripe_subscription" value="0">
            <input class="form-check-input" type="checkbox" name="enable_stripe_subscription"
                   id="stripe_sub" value="1"
                   <?= setting('enable_stripe_subscription') ? 'checked':'' ?>>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save Subscription Settings
    </button>
</div>
<?= form_close() ?>

<style>
.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.toggle-row{display:flex;align-items:center;justify-content:space-between;padding:.8rem 0;border-bottom:1px solid #f3f4f6;}
.toggle-row:last-child{border-bottom:none;}
.toggle-label strong{font-size:.875rem;color:#374151;display:block;}
.toggle-label small{font-size:.775rem;color:#6b7280;}
.form-switch .form-check-input{width:2.5em;height:1.35em;cursor:pointer;}
</style>
<?= $this->endSection() ?>
