<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-store text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Store Settings</h5>
        <small class="text-muted">Online store access and checkout options</small>
    </div>
</div>

<?= form_open('settings/save_store_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="store">

<div class="settings-section-hdr">Access & Visibility</div>
<div class="border rounded-3 px-3 py-1 bg-white">
    <?php $toggles = [
        ['name'=>'visitors_can_see_store_before_login',            'label'=>'Public Store (No Login Required)',       'desc'=>'Allow visitors to browse the store without logging in'],
        ['name'=>'show_payment_option_after_submitting_the_order', 'label'=>'Show Payment After Order Submission',   'desc'=>'Redirect to payment immediately after an order is placed'],
        ['name'=>'accept_order_before_login',                      'label'=>'Accept Orders Before Login',            'desc'=>'Complete order checkout without requiring an account'],
    ]; foreach($toggles as $t): ?>
    <div class="toggle-row">
        <div class="toggle-label">
            <strong><?= $t['label'] ?></strong>
            <small><?= $t['desc'] ?></small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="<?= $t['name'] ?>" value="0">
            <input class="form-check-input" type="checkbox" name="<?= $t['name'] ?>"
                   id="store_<?= $t['name'] ?>" value="1"
                   <?= setting($t['name']) ? 'checked':'' ?>>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save Store Settings
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
