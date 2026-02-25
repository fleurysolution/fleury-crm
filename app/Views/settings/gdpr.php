<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-shield-halved text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">GDPR Settings</h5>
        <small class="text-muted">Data privacy and client data rights</small>
    </div>
</div>

<?= form_open('settings/save_gdpr_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="gdpr">

<div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius:10px;background:rgba(74,144,226,.08);">
    <i class="fa-solid fa-circle-info me-2 text-primary"></i>
    <strong>GDPR Compliance: </strong>
    Enable these options to give clients control over their data as required by GDPR regulations.
</div>

<div class="border rounded-3 px-3 py-1 bg-white">
    <?php $toggles = [
        ['name'=>'enable_gdpr',                               'label'=>'Enable GDPR Features',                  'desc'=>'Enable all GDPR-related features on the client portal'],
        ['name'=>'allow_clients_to_export_their_data',        'label'=>'Allow Clients to Export Their Data',    'desc'=>'Clients can download all data stored about them'],
        ['name'=>'clients_can_request_account_removal',       'label'=>'Allow Account Removal Requests',        'desc'=>'Clients can request their account and data to be deleted'],
        ['name'=>'show_terms_and_conditions_in_client_signup_page','label'=>'Show T&C on Client Signup',        'desc'=>'Show terms & conditions checkbox during client registration'],
    ]; ?>
    <?php foreach($toggles as $t): ?>
    <div class="toggle-row">
        <div class="toggle-label">
            <strong><?= $t['label'] ?></strong>
            <small><?= $t['desc'] ?></small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="<?= $t['name'] ?>" value="0">
            <input class="form-check-input" type="checkbox" name="<?= $t['name'] ?>"
                   id="gdpr_<?= $t['name'] ?>" value="1"
                   <?= setting($t['name']) ? 'checked' : '' ?>>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save GDPR Settings
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
