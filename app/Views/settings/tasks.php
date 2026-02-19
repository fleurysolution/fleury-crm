<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">Task Settings</h4>

<?= form_open('settings/save', ['class' => 'general-form']) ?>
<input type="hidden" name="setting_group" value="task">

<div class="row">
    <div class="col-md-12 mb-3">
        <div class="form-check form-switch">
             <input type="hidden" name="project_task_reminder_on_the_day_of_deadline" value="0">
            <input class="form-check-input" type="checkbox" id="project_task_reminder_on_the_day_of_deadline" name="project_task_reminder_on_the_day_of_deadline" value="1" <?= setting('project_task_reminder_on_the_day_of_deadline') ? 'checked' : '' ?>>
            <label class="form-check-label fw-bold" for="project_task_reminder_on_the_day_of_deadline">Send task reminder on the day of deadline?</label>
        </div>
    </div>
    
    <div class="col-md-12 mb-3">
        <div class="form-check form-switch">
             <input type="hidden" name="enable_recurring_option_for_tasks" value="0">
            <input class="form-check-input" type="checkbox" id="enable_recurring_option_for_tasks" name="enable_recurring_option_for_tasks" value="1" <?= setting('enable_recurring_option_for_tasks') ? 'checked' : '' ?>>
            <label class="form-check-label fw-bold" for="enable_recurring_option_for_tasks">Enable recurring option for tasks?</label>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
         <label for="task_point_range" class="form-label">Task Point Range</label>
        <input type="number" name="task_point_range" id="task_point_range" class="form-control" value="<?= esc(setting('task_point_range', '5')) ?>">
    </div>
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
    <button type="submit" class="btn btn-primary">Save Changes</button>
</div>

<?= form_close() ?>

<?= $this->endSection() ?>
