<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-cubes text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Module Settings</h5>
        <small class="text-muted">Enable or disable application modules</small>
    </div>
</div>

<?= form_open('settings/save_module_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="module">

<?php
$moduleGroups = [
    'Finance' => [
        ['key'=>'module_invoice',      'label'=>'Invoices',       'icon'=>'fa-file-invoice',  'desc'=>'Create and manage client invoices'],
        ['key'=>'module_estimate',     'label'=>'Estimates',      'icon'=>'fa-file-lines',    'desc'=>'Create and send estimates'],
        ['key'=>'module_contract',     'label'=>'Contracts',      'icon'=>'fa-file-contract', 'desc'=>'Manage service contracts'],
        ['key'=>'module_proposal',     'label'=>'Proposals',      'icon'=>'fa-handshake',     'desc'=>'Create business proposals'],
        ['key'=>'module_expense',      'label'=>'Expenses',       'icon'=>'fa-receipt',       'desc'=>'Track team expenses'],
        ['key'=>'module_subscription', 'label'=>'Subscriptions',  'icon'=>'fa-rotate',        'desc'=>'Manage recurring subscriptions'],
        ['key'=>'module_order',        'label'=>'Orders',         'icon'=>'fa-cart-shopping', 'desc'=>'Manage customer orders'],
        ['key'=>'module_store',        'label'=>'Online Store',   'icon'=>'fa-store',         'desc'=>'Client-facing product store'],
    ],
    'Projects & Work' => [
        ['key'=>'module_project',      'label'=>'Projects',       'icon'=>'fa-folder-open',   'desc'=>'Project management and tracking'],
        ['key'=>'module_gantt',        'label'=>'Gantt Chart',    'icon'=>'fa-chart-gantt',   'desc'=>'Visual project timelines'],
        ['key'=>'module_task',         'label'=>'Tasks',          'icon'=>'fa-list-check',    'desc'=>'Team task management'],
        ['key'=>'module_event',        'label'=>'Events',         'icon'=>'fa-calendar-days', 'desc'=>'Events and agenda'],
        ['key'=>'module_leave',        'label'=>'Leave',          'icon'=>'fa-plane',         'desc'=>'Employee leave management'],
        ['key'=>'module_attendance',   'label'=>'Attendance',     'icon'=>'fa-fingerprint',   'desc'=>'Track employee attendance'],
        ['key'=>'module_reminder',     'label'=>'Reminders',      'icon'=>'fa-bell',          'desc'=>'Reminders and follow-ups'],
    ],
    'Communication' => [
        ['key'=>'module_lead',         'label'=>'Leads',          'icon'=>'fa-user-plus',     'desc'=>'Lead tracking and CRM'],
        ['key'=>'module_ticket',       'label'=>'Support Tickets','icon'=>'fa-ticket',        'desc'=>'Customer support ticketing'],
        ['key'=>'module_message',      'label'=>'Messaging',      'icon'=>'fa-comments',      'desc'=>'Internal team messaging'],
        ['key'=>'module_announcement', 'label'=>'Announcements',  'icon'=>'fa-bullhorn',      'desc'=>'Company-wide announcements'],
    ],
    'Other' => [
        ['key'=>'module_timeline',       'label'=>'Activity Timeline','icon'=>'fa-timeline',       'desc'=>'Activity and audit timeline'],
        ['key'=>'module_knowledge_base', 'label'=>'Knowledge Base',   'icon'=>'fa-book-open',      'desc'=>'Internal knowledge base articles'],
        ['key'=>'module_file_manager',   'label'=>'File Manager',     'icon'=>'fa-folder',         'desc'=>'Shared file storage and management'],
    ],
];
foreach($moduleGroups as $groupName => $modules):
?>
<div class="settings-section-hdr"><?= esc($groupName) ?></div>
<div class="row g-3 mb-2">
    <?php foreach($modules as $m): ?>
    <div class="col-md-6">
        <div class="d-flex align-items-center justify-content-between border rounded-3 px-3 py-2 bg-white" style="gap:.75rem;">
            <div class="d-flex align-items-center gap-2">
                <div style="width:36px;height:36px;border-radius:8px;background:rgba(74,144,226,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid <?= esc($m['icon']) ?> text-primary" style="font-size:.8rem;"></i>
                </div>
                <div>
                    <div class="fw-semibold" style="font-size:.855rem;color:#374151;"><?= esc($m['label']) ?></div>
                    <div class="text-muted" style="font-size:.75rem;"><?= esc($m['desc']) ?></div>
                </div>
            </div>
            <div class="form-check form-switch mb-0 flex-shrink-0">
                <input type="hidden" name="<?= $m['key'] ?>" value="0">
                <input class="form-check-input" type="checkbox"
                       name="<?= $m['key'] ?>" id="mod_<?= $m['key'] ?>" value="1"
                       <?= setting($m['key'],'1') ? 'checked':'' ?>>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endforeach; ?>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save Module Settings
    </button>
</div>
<?= form_close() ?>

<style>
.settings-icon-badge{width:48px;height:48px;border-radius:12px;background:rgba(74,144,226,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.form-switch .form-check-input{width:2.5em;height:1.35em;cursor:pointer;}
</style>
<?= $this->endSection() ?>
