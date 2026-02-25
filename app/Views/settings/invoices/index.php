<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-file-invoice text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Invoice Settings</h5>
        <small class="text-muted">Numbering, colours and invoice defaults</small>
    </div>
</div>

<?= form_open('settings/save_invoice_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="invoice">

<div class="settings-section-hdr">Numbering</div>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="invoice_prefix" class="form-label">Prefix</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
            <input type="text" name="invoice_prefix" id="invoice_prefix" class="form-control"
                   value="<?= esc(setting('invoice_prefix','INV-')) ?>" placeholder="INV-">
        </div>
    </div>
    <div class="col-md-4">
        <label for="initial_number_of_the_invoice" class="form-label">Starting Number</label>
        <input type="number" name="initial_number_of_the_invoice" id="initial_number_of_the_invoice"
               class="form-control" value="<?= esc(setting('initial_number_of_the_invoice','1001')) ?>" min="1">
        <div class="form-text">Applied to new invoices only.</div>
    </div>
    <div class="col-md-4">
        <label for="default_due_date_after_billing_date" class="form-label">Default Due (days)</label>
        <input type="number" name="default_due_date_after_billing_date"
               id="default_due_date_after_billing_date" class="form-control"
               value="<?= esc(setting('default_due_date_after_billing_date','15')) ?>" min="0">
        <div class="form-text">Days after billing date.</div>
    </div>
</div>

<div class="settings-section-hdr">Appearance</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label for="invoice_color" class="form-label">Invoice Accent Colour</label>
        <div class="d-flex align-items-center gap-2">
            <input type="color" name="invoice_color" id="invoice_color"
                   class="form-control form-control-color"
                   value="<?= esc(setting('invoice_color','#4a90e2')) ?>">
            <span class="form-text mb-0">Pick a colour for invoice headers.</span>
        </div>
    </div>
    <div class="col-md-4">
        <label for="invoice_style" class="form-label">Invoice Template Style</label>
        <select name="invoice_style" id="invoice_style" class="form-select">
            <option value="style1" <?= setting('invoice_style')=='style1' ? 'selected':'' ?>>Style 1 – Classic</option>
            <option value="style2" <?= setting('invoice_style')=='style2' ? 'selected':'' ?>>Style 2 – Modern</option>
            <option value="style3" <?= setting('invoice_style')=='style3' ? 'selected':'' ?>>Style 3 – Minimal</option>
        </select>
    </div>
</div>

<div class="settings-section-hdr">Footer Text</div>
<div class="mb-3">
    <label for="invoice_footer" class="form-label">Invoice Footer Note</label>
    <textarea name="invoice_footer" id="invoice_footer" class="form-control" rows="3"
              placeholder="Thank you for your business."><?= esc(setting('invoice_footer','')) ?></textarea>
</div>

<div class="settings-section-hdr">Options</div>
<div class="border rounded-3 px-3 py-1 mb-3 bg-white">
    <div class="toggle-row">
        <div class="toggle-label">
            <strong>Reset Invoice Number Every Year</strong>
            <small>Restart numbering from the starting number each new year</small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="reset_invoice_number_every_year" value="0">
            <input class="form-check-input" type="checkbox" id="reset_inv"
                   name="reset_invoice_number_every_year" value="1"
                   <?= setting('reset_invoice_number_every_year') ? 'checked':'' ?>>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save Invoice Settings
    </button>
</div>
<?= form_close() ?>

<style>.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;} .toggle-row{display:flex;align-items:center;justify-content:space-between;padding:.7rem 0;border-bottom:1px solid #f3f4f6;} .toggle-row:last-child{border-bottom:none;}</style>
<?= $this->endSection() ?>
