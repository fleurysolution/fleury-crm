<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-calendar-days text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Event Settings</h5>
        <small class="text-muted">Calendar and event display options</small>
    </div>
</div>

<?= form_open('settings/save_event_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="event">

<div class="settings-section-hdr">Calendar View</div>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="default_calendar_view" class="form-label">Default View</label>
        <select name="default_calendar_view" id="default_calendar_view" class="form-select">
            <option value="month" <?= setting('default_calendar_view')=='month' ? 'selected':'' ?>>Month</option>
            <option value="week"  <?= setting('default_calendar_view')=='week'  ? 'selected':'' ?>>Week</option>
            <option value="day"   <?= setting('default_calendar_view')=='day'   ? 'selected':'' ?>>Day</option>
            <option value="list"  <?= setting('default_calendar_view')=='list'  ? 'selected':'' ?>>List</option>
        </select>
    </div>
</div>

<div class="settings-section-hdr">Options</div>
<div class="border rounded-3 px-3 py-1 bg-white">
    <div class="toggle-row">
        <div class="toggle-label">
            <strong>Show Tasks on Calendar</strong>
            <small>Display project task due dates on the event calendar</small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="show_tasks_on_calendar" value="0">
            <input class="form-check-input" type="checkbox" name="show_tasks_on_calendar"
                   id="eve_tasks" value="1"
                   <?= setting('show_tasks_on_calendar') ? 'checked':'' ?>>
        </div>
    </div>
    <div class="toggle-row">
        <div class="toggle-label">
            <strong>Show Holidays</strong>
            <small>Display public holidays on the calendar</small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="show_holidays_on_calendar" value="0">
            <input class="form-check-input" type="checkbox" name="show_holidays_on_calendar"
                   id="eve_holidays" value="1"
                   <?= setting('show_holidays_on_calendar') ? 'checked':'' ?>>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save"><i class="fa-solid fa-floppy-disk me-2"></i>Save Event Settings</button>
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
