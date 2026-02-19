<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">Modules</h4>
<p class="text-muted mb-4">Enable or disable modules to control features available in the CRM.</p>

<?= form_open('settings/save', ['class' => 'general-form']) ?>
<input type="hidden" name="setting_group" value="modules">

<div class="row">
    <?php
    $modules = [
        'module_timeline' => 'Timeline',
        'module_event' => 'Events',
        'module_todo' => 'To Do',
        'module_note' => 'Notes',
        'module_message' => 'Messages',
        'module_chat' => 'Chat',
        'module_invoice' => 'Invoices',
        'module_expense' => 'Expenses',
        'module_attendance' => 'Attendance',
        'module_leave' => 'Leave',
        'module_estimate' => 'Estimates',
        'module_estimate_request' => 'Estimate Requests',
        'module_lead' => 'Leads',
        'module_ticket' => 'Tickets',
        'module_announcement' => 'Announcements',
        'module_project_timesheet' => 'Project Timesheets',
        'module_gantt' => 'Gantt',
        'module_proposal' => 'Proposals',
        'module_contract' => 'Contracts',
        'module_subscription' => 'Subscriptions',
    ];
    
    foreach($modules as $key => $label):
    ?>
    <div class="col-md-6 mb-3">
        <div class="form-check form-switch">
             <input type="hidden" name="<?= $key ?>" value="0"> <!-- Fallback for unchecked -->
            <input class="form-check-input" type="checkbox" id="<?= $key ?>" name="<?= $key ?>" value="1" <?= setting($key) != '0' ? 'checked' : '' ?>> <!-- Default checked if not set to '0' -->
            <label class="form-check-label" for="<?= $key ?>"><?= $label ?></label>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
    <button type="submit" class="btn btn-primary">Save Changes</button>
</div>

<?= form_close() ?>

<?= $this->endSection() ?>
