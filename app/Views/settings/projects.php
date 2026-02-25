<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-folder-open text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Project Settings</h5>
        <small class="text-muted">Project defaults, access and task timers</small>
    </div>
</div>

<?= form_open('settings/save_projects_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="project">

<div class="settings-section-hdr">Behaviour</div>
<div class="border rounded-3 px-3 py-1 bg-white">
    <?php $toggles = [
        ['name'=>'allow_staff_to_create_projects',          'label'=>'Staff Can Create Projects',                'desc'=>'Allow non-admin team members to create new projects'],
        ['name'=>'allow_customer_to_view_tasks',            'label'=>'Clients Can View Tasks',                   'desc'=>'Allow clients to see tasks in their portal'],
        ['name'=>'allow_customer_to_create_tasks',          'label'=>'Clients Can Create Tasks',                 'desc'=>'Allow clients to add tasks to their projects'],
        ['name'=>'allow_customer_to_upload_attachments',    'label'=>'Clients Can Upload Attachments',           'desc'=>'Allow clients to upload files to project tasks'],
        ['name'=>'allow_customer_to_view_task_comments',    'label'=>'Clients Can View Task Comments',           'desc'=>'Show internal task comments to clients'],
        ['name'=>'allow_customer_to_comment_on_tasks',      'label'=>'Clients Can Comment on Tasks',             'desc'=>'Allow clients to post comments on tasks'],
        ['name'=>'allow_customer_to_view_task_activity',    'label'=>'Clients Can View Task Activity',           'desc'=>'Show task activity log to clients'],
        ['name'=>'allow_customer_to_view_finance_overview', 'label'=>'Clients Can View Finance Overview',        'desc'=>'Show the project finance tab to clients'],
        ['name'=>'allow_customer_to_view_gantt',            'label'=>'Clients Can View Gantt Chart',             'desc'=>'Show the Gantt chart to clients'],
        ['name'=>'allow_customer_to_view_timesheets',       'label'=>'Clients Can View Timesheets',              'desc'=>'Show team timesheet entries to clients'],
        ['name'=>'allow_customer_to_view_activity_log',     'label'=>'Clients Can View Activity Log',            'desc'=>'Show the full project activity log to clients'],
        ['name'=>'allow_customer_to_view_team_members',     'label'=>'Clients Can View Team Members',            'desc'=>'Show the team members list to clients'],
        ['name'=>'enable_timesheets',                       'label'=>'Enable Timesheets',                        'desc'=>'Turn on timelog tracking for project tasks'],
        ['name'=>'save_and_send_timer_time_as_billable',    'label'=>'Timer Entries Are Billable by Default',    'desc'=>'Mark timer-logged time as billable automatically'],
    ]; foreach($toggles as $t): ?>
    <div class="toggle-row">
        <div class="toggle-label">
            <strong><?= $t['label'] ?></strong>
            <small><?= $t['desc'] ?></small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="<?= $t['name'] ?>" value="0">
            <input class="form-check-input" type="checkbox" name="<?= $t['name'] ?>"
                   id="proj_<?= $t['name'] ?>" value="1"
                   <?= setting($t['name']) ? 'checked':'' ?>>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save Project Settings
    </button>
</div>
<?= form_close() ?>

<style>
.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.toggle-row{display:flex;align-items:center;justify-content:space-between;padding:.75rem 0;border-bottom:1px solid #f3f4f6;}
.toggle-row:last-child{border-bottom:none;}
.toggle-label strong{font-size:.875rem;color:#374151;display:block;}
.toggle-label small{font-size:.775rem;color:#6b7280;}
.form-switch .form-check-input{width:2.5em;height:1.35em;cursor:pointer;}
</style>
<?= $this->endSection() ?>
