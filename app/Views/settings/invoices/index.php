<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">Invoice Settings</h4>

<?= form_open('settings/save', ['class' => 'general-form']) ?>
<input type="hidden" name="setting_group" value="invoice">

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="invoice_prefix" class="form-label">Invoice Prefix</label>
        <input type="text" name="invoice_prefix" id="invoice_prefix" class="form-control" value="<?= esc(setting('invoice_prefix', 'INV')) ?>">
    </div>
    
    <div class="col-md-6 mb-3">
        <label for="invoice_color" class="form-label">Invoice Hex Color</label>
        <input type="color" name="invoice_color" id="invoice_color" class="form-control form-control-color" value="<?= esc(setting('invoice_color', '#000000')) ?>" title="Choose your color">
    </div>
    
    <div class="col-md-6 mb-3">
        <label for="invoice_footer" class="form-label">Invoice Footer</label>
        <textarea name="invoice_footer" id="invoice_footer" class="form-control" rows="3"><?= esc(setting('invoice_footer')) ?></textarea>
    </div>
    
     <div class="col-md-6 mb-3">
        <label class="form-label">Reset Invoice Number Every Year</label>
        <select name="reset_invoice_number_every_year" class="form-select">
            <option value="1" <?= setting('reset_invoice_number_every_year') == '1' ? 'selected' : '' ?>>Yes</option>
            <option value="0" <?= setting('reset_invoice_number_every_year') == '0' ? 'selected' : '' ?>>No</option>
        </select>
    </div>

    <div class="col-md-12 mb-3">
        <label for="initial_number_of_the_invoice" class="form-label">Initial Number of Invoice</label>
        <input type="number" name="initial_number_of_the_invoice" id="initial_number_of_the_invoice" class="form-control" value="<?= esc(setting('initial_number_of_the_invoice', '1')) ?>">
        <small class="text-muted">Only applies to new invoices if auto-increment is consistent.</small>
    </div>
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
    <button type="submit" class="btn btn-primary">Save Changes</button>
</div>

<?= form_close() ?>

<?= $this->endSection() ?>
