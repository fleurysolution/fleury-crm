<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-user-plus text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Lead Settings</h5>
        <small class="text-muted">Public forms and lead capture options</small>
    </div>
</div>

<?= form_open('settings/save_lead_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="lead">

<div class="settings-section-hdr">Public Lead Form</div>
<div class="border rounded-3 px-3 py-1 bg-white mb-3">
    <?php $toggles = [
        ['name'=>'can_create_lead_from_public_form',    'label'=>'Enable Public Lead Form',            'desc'=>'Allow anyone to submit a lead via a public URL'],
        ['name'=>'enable_embedded_form_to_get_leads',   'label'=>'Enable Embeddable Lead Form',        'desc'=>'Generate an embed code to place the form on external sites'],
    ]; foreach($toggles as $t): ?>
    <div class="toggle-row">
        <div class="toggle-label">
            <strong><?= $t['label'] ?></strong>
            <small><?= $t['desc'] ?></small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="<?= $t['name'] ?>" value="0">
            <input class="form-check-input" type="checkbox" name="<?= $t['name'] ?>"
                   id="lead_<?= $t['name'] ?>" value="1"
                   <?= setting($t['name']) ? 'checked':'' ?>>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="mb-3">
    <label for="after_submit_action_of_public_lead_form" class="form-label">After-submit Action</label>
    <select name="after_submit_action_of_public_lead_form"
            id="after_submit_action_of_public_lead_form" class="form-select">
        <option value="message"  <?= setting('after_submit_action_of_public_lead_form')=='message'  ? 'selected':'' ?>>Show Thank-you Message</option>
        <option value="redirect" <?= setting('after_submit_action_of_public_lead_form')=='redirect' ? 'selected':'' ?>>Redirect to URL</option>
    </select>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save Lead Settings
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
