<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-cart-shopping text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Order Settings</h5>
        <small class="text-muted">Order numbering and client behaviour</small>
    </div>
</div>

<?= form_open('settings/save_order_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="order">

<div class="settings-section-hdr">Numbering</div>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="order_prefix" class="form-label">Prefix</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
            <input type="text" name="order_prefix" id="order_prefix" class="form-control"
                   value="<?= esc(setting('order_prefix','ORD-')) ?>">
        </div>
    </div>
    <div class="col-md-4">
        <label for="initial_number_of_the_order" class="form-label">Starting Number</label>
        <input type="number" name="initial_number_of_the_order"
               id="initial_number_of_the_order" class="form-control"
               value="<?= esc(setting('initial_number_of_the_order','1001')) ?>" min="1">
    </div>
    <div class="col-md-4">
        <label for="order_color" class="form-label">Accent Colour</label>
        <input type="color" name="order_color" id="order_color"
               class="form-control form-control-color"
               value="<?= esc(setting('order_color','#2980b9')) ?>">
    </div>
</div>

<div class="settings-section-hdr">Checkout Options</div>
<div class="border rounded-3 px-3 py-1 bg-white">
    <?php $toggles = [
        ['name'=>'show_payment_option_after_submitting_the_order','label'=>'Show Payment on Checkout',     'desc'=>'Display payment options immediately after an order is submitted'],
        ['name'=>'accept_order_before_login',                     'label'=>'Accept Orders Before Login',  'desc'=>'Allow guests to submit orders without registering'],
    ]; foreach($toggles as $t): ?>
    <div class="toggle-row">
        <div class="toggle-label">
            <strong><?= $t['label'] ?></strong>
            <small><?= $t['desc'] ?></small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="<?= $t['name'] ?>" value="0">
            <input class="form-check-input" type="checkbox" name="<?= $t['name'] ?>"
                   id="ord_<?= $t['name'] ?>" value="1"
                   <?= setting($t['name']) ? 'checked':'' ?>>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save Order Settings
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
