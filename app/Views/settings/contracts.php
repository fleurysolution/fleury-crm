<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-file-contract text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Contract Settings</h5>
        <small class="text-muted">Contract numbering and appearance</small>
    </div>
</div>

<?= form_open('settings/save_contract_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="contract">

<div class="settings-section-hdr">Numbering</div>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="contract_prefix" class="form-label">Prefix</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
            <input type="text" name="contract_prefix" id="contract_prefix" class="form-control"
                   value="<?= esc(setting('contract_prefix','CTR-')) ?>">
        </div>
    </div>
    <div class="col-md-4">
        <label for="initial_number_of_the_contract" class="form-label">Starting Number</label>
        <input type="number" name="initial_number_of_the_contract"
               id="initial_number_of_the_contract" class="form-control"
               value="<?= esc(setting('initial_number_of_the_contract','1001')) ?>" min="1">
    </div>
    <div class="col-md-4">
        <label for="contract_color" class="form-label">Accent Colour</label>
        <input type="color" name="contract_color" id="contract_color"
               class="form-control form-control-color"
               value="<?= esc(setting('contract_color','#8e44ad')) ?>">
    </div>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save Contract Settings
    </button>
</div>
<?= form_close() ?>

<style>.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;}</style>
<?= $this->endSection() ?>
