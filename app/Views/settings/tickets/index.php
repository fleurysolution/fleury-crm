<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<h4 class="mb-4">Ticket Settings</h4>

<?= form_open('settings/save', ['class' => 'general-form']) ?>
<input type="hidden" name="setting_group" value="ticket">

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="ticket_prefix" class="form-label">Ticket Prefix</label>
        <input type="text" name="ticket_prefix" id="ticket_prefix" class="form-control" value="<?= esc(setting('ticket_prefix', 'TIC')) ?>">
    </div>
    
    <div class="col-md-6 mb-3">
        <label for="auto_close_ticket_after" class="form-label">Auto Close Ticket After (Days)</label>
        <input type="number" name="auto_close_ticket_after" id="auto_close_ticket_after" class="form-control" value="<?= esc(setting('auto_close_ticket_after', '0')) ?>">
        <small class="text-muted">Set 0 to disable.</small>
    </div>
    
    <div class="col-md-12 mb-3">
        <div class="form-check form-switch">
             <input type="hidden" name="auto_reply_to_tickets" value="0">
            <input class="form-check-input" type="checkbox" id="auto_reply_to_tickets" name="auto_reply_to_tickets" value="1" <?= setting('auto_reply_to_tickets') ? 'checked' : '' ?>>
            <label class="form-check-label fw-bold" for="auto_reply_to_tickets">Enable Auto Reply to Tickets?</label>
        </div>
    </div>
    
    <div class="col-md-12 mb-3">
        <label for="auto_reply_to_tickets_message" class="form-label">Auto Reply Message</label>
        <textarea name="auto_reply_to_tickets_message" id="auto_reply_to_tickets_message" class="form-control" rows="4"><?= esc(setting('auto_reply_to_tickets_message')) ?></textarea>
    </div>
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
    <button type="submit" class="btn btn-primary">Save Changes</button>
</div>

<?= form_close() ?>

<?= $this->endSection() ?>
