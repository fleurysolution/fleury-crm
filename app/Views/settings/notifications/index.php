<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-bell text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Notification Settings</h5>
        <small class="text-muted">Control which events trigger notifications</small>
    </div>
</div>

<?= form_open('settings/save_notification_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="notification">

<?php
$notifGroups = [
    'Invoice Notifications' => [
        ['name'=>'notify_on_invoice_create',  'label'=>'Invoice Created',           'desc'=>'When a new invoice is issued'],
        ['name'=>'notify_on_invoice_payment', 'label'=>'Invoice Payment Received',  'desc'=>'When a payment is recorded'],
        ['name'=>'notify_on_invoice_overdue', 'label'=>'Invoice Overdue',           'desc'=>'When an invoice passes its due date'],
    ],
    'Project & Task Notifications' => [
        ['name'=>'notify_on_project_create',     'label'=>'Project Created',        'desc'=>'When a new project is started'],
        ['name'=>'notify_on_task_assign',         'label'=>'Task Assigned',         'desc'=>'When a task is assigned to a team member'],
        ['name'=>'notify_on_task_complete',       'label'=>'Task Completed',        'desc'=>'When a task is marked as done'],
        ['name'=>'notify_on_task_comment',        'label'=>'Task Comment',          'desc'=>'When someone comments on a task'],
        ['name'=>'notify_on_task_deadline',       'label'=>'Task Deadline Approaching','desc'=>'Reminder before task due date'],
    ],
    'Ticket Notifications' => [
        ['name'=>'notify_on_ticket_open',       'label'=>'Ticket Opened',           'desc'=>'When a new support ticket is submitted'],
        ['name'=>'notify_on_ticket_reply',      'label'=>'Ticket Reply',            'desc'=>'When a reply is posted to a ticket'],
        ['name'=>'notify_on_ticket_close',      'label'=>'Ticket Closed',           'desc'=>'When a ticket is resolved and closed'],
    ],
    'Lead Notifications' => [
        ['name'=>'notify_on_lead_create',       'label'=>'New Lead',                'desc'=>'When a lead is captured from any source'],
        ['name'=>'notify_on_lead_assign',       'label'=>'Lead Assigned',           'desc'=>'When a lead is assigned to a staff member'],
    ],
    'Contract & Estimate Notifications' => [
        ['name'=>'notify_on_contract_sign',     'label'=>'Contract Signed',         'desc'=>'When a client signs a contract'],
        ['name'=>'notify_on_estimate_action',   'label'=>'Estimate Accepted/Declined','desc'=>'When a client responds to an estimate'],
    ],
];
foreach($notifGroups as $group => $items):
?>
<div class="settings-section-hdr"><?= esc($group) ?></div>
<div class="border rounded-3 px-3 py-1 bg-white mb-3">
    <?php foreach($items as $t): ?>
    <div class="toggle-row">
        <div class="toggle-label">
            <strong><?= $t['label'] ?></strong>
            <small><?= $t['desc'] ?></small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="form-check form-switch mb-0" title="Email">
                <input type="hidden" name="<?= $t['name'] ?>_email" value="0">
                <input class="form-check-input" type="checkbox" title="Email"
                       name="<?= $t['name'] ?>_email" id="<?= $t['name'] ?>_email" value="1"
                       <?= setting($t['name'].'_email', '1') ? 'checked':'' ?>>
                <label class="form-check-label text-muted" for="<?= $t['name'] ?>_email" style="font-size:.72rem;">Email</label>
            </div>
            <div class="form-check form-switch mb-0" title="In-app">
                <input type="hidden" name="<?= $t['name'] ?>_inapp" value="0">
                <input class="form-check-input" type="checkbox" title="In-app"
                       name="<?= $t['name'] ?>_inapp" id="<?= $t['name'] ?>_inapp" value="1"
                       <?= setting($t['name'].'_inapp', '1') ? 'checked':'' ?>>
                <label class="form-check-label text-muted" for="<?= $t['name'] ?>_inapp" style="font-size:.72rem;">In-app</label>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endforeach; ?>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save Notification Settings
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
