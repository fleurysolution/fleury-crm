<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-list-check text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Task Settings</h5>
        <small class="text-muted">Task reminders and recurring options</small>
    </div>
</div>

<?= form_open('settings/save_task_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="task">

<div class="settings-section-hdr">Reminder Timing</div>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="project_task_deadline_pre_reminder" class="form-label">Pre-Deadline Reminder</label>
        <div class="input-group">
            <input type="number" name="project_task_deadline_pre_reminder"
                   id="project_task_deadline_pre_reminder" class="form-control"
                   value="<?= esc(setting('project_task_deadline_pre_reminder','2')) ?>" min="0">
            <span class="input-group-text">days before</span>
        </div>
    </div>
    <div class="col-md-4">
        <label for="project_task_deadline_overdue_reminder" class="form-label">Overdue Reminder</label>
        <div class="input-group">
            <input type="number" name="project_task_deadline_overdue_reminder"
                   id="project_task_deadline_overdue_reminder" class="form-control"
                   value="<?= esc(setting('project_task_deadline_overdue_reminder','1')) ?>" min="0">
            <span class="input-group-text">days after due</span>
        </div>
    </div>
</div>

<div class="settings-section-hdr">Behaviour</div>
<div class="border rounded-3 px-3 py-1 bg-white">
    <?php $toggles = [
        ['name'=>'project_task_reminder_on_the_day_of_deadline', 'label'=>'Remind on Deadline Day',          'desc'=>'Send a reminder notification on the actual due date'],
        ['name'=>'enable_recurring_option_for_tasks',            'label'=>'Enable Recurring Tasks',           'desc'=>'Allow tasks to repeat on a schedule'],
    ]; foreach($toggles as $t): ?>
    <div class="toggle-row">
        <div class="toggle-label">
            <strong><?= $t['label'] ?></strong>
            <small><?= $t['desc'] ?></small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="<?= $t['name'] ?>" value="0">
            <input class="form-check-input" type="checkbox" name="<?= $t['name'] ?>"
                   id="task_<?= $t['name'] ?>" value="1"
                   <?= setting($t['name']) ? 'checked':'' ?>>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save Task Settings
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
