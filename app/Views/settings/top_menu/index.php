<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-bars text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Top Menu Settings</h5>
        <small class="text-muted">Control items displayed in the top navigation bar</small>
    </div>
</div>

<?= form_open('settings/save_top_menu_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="top_menu">

<div class="settings-section-hdr">Navigation Items</div>
<div class="border rounded-3 px-3 py-1 bg-white">
    <?php $items = [
        ['name'=>'show_notification_in_top_menu',   'label'=>'Notifications Bell',      'desc'=>'Show the notifications dropdown in the top bar'],
        ['name'=>'show_message_in_top_menu',        'label'=>'Messages Icon',           'desc'=>'Show the messaging shortcut in the top bar'],
        ['name'=>'show_calendar_in_top_menu',       'label'=>'Calendar Link',           'desc'=>'Show a quick link to the calendar in the top bar'],
        ['name'=>'show_search_in_top_menu',         'label'=>'Global Search',           'desc'=>'Show the global search bar in the top navigation'],
        ['name'=>'show_language_switcher_in_menu',  'label'=>'Language Switcher',       'desc'=>'Allow users to switch application language from the top bar'],
        ['name'=>'show_dark_mode_switch_in_menu',   'label'=>'Dark Mode Toggle',        'desc'=>'Show a light/dark theme toggle in the top bar'],
    ]; foreach($items as $t): ?>
    <div class="toggle-row">
        <div class="toggle-label">
            <strong><?= $t['label'] ?></strong>
            <small><?= $t['desc'] ?></small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="<?= $t['name'] ?>" value="0">
            <input class="form-check-input" type="checkbox" name="<?= $t['name'] ?>"
                   id="tm_<?= $t['name'] ?>" value="1"
                   <?= setting($t['name'], '1') ? 'checked':'' ?>>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save"><i class="fa-solid fa-floppy-disk me-2"></i>Save Top Menu Settings</button>
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
