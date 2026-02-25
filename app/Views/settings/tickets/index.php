<?= $this->extend('settings/layout') ?>
<?= $this->section('settings_content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <div class="settings-icon-badge"><i class="fa-solid fa-ticket text-primary fa-lg"></i></div>
    <div>
        <h5 class="fw-bold mb-0">Ticket Settings</h5>
        <small class="text-muted">Support ticket behaviour and automation</small>
    </div>
</div>

<?= form_open('settings/save_ticket_settings', ['class' => 'settings-ajax-form']) ?>
<input type="hidden" name="setting_group" value="ticket">

<div class="settings-section-hdr">Numbering</div>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="ticket_prefix" class="form-label">Ticket Prefix</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
            <input type="text" name="ticket_prefix" id="ticket_prefix" class="form-control"
                   value="<?= esc(setting('ticket_prefix','TKT-')) ?>" placeholder="TKT-">
        </div>
    </div>
    <div class="col-md-4">
        <label for="auto_close_ticket_after" class="form-label">Auto-close After (days)</label>
        <div class="input-group">
            <input type="number" name="auto_close_ticket_after" id="auto_close_ticket_after"
                   class="form-control" value="<?= esc(setting('auto_close_ticket_after','7')) ?>" min="0">
            <span class="input-group-text">days</span>
        </div>
        <div class="form-text">0 = never auto-close.</div>
    </div>
</div>

<div class="settings-section-hdr">Options</div>
<div class="border rounded-3 px-3 py-1 bg-white mb-3">
    <?php $toggles = [
        ['name'=>'auto_reply_to_tickets',                'label'=>'Auto-reply to New Tickets',          'desc'=>'Send an automatic confirmation reply when a ticket is opened'],
        ['name'=>'show_recent_ticket_comments_at_the_top','label'=>'Show Recent Comments at Top',        'desc'=>'Display newest ticket comments first'],
    ]; foreach($toggles as $t): ?>
    <div class="toggle-row">
        <div class="toggle-label">
            <strong><?= $t['label'] ?></strong>
            <small><?= $t['desc'] ?></small>
        </div>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="<?= $t['name'] ?>" value="0">
            <input class="form-check-input" type="checkbox" name="<?= $t['name'] ?>"
                   id="tkt_<?= $t['name'] ?>" value="1"
                   <?= setting($t['name']) ? 'checked':'' ?>>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="mb-3">
    <label for="auto_reply_to_tickets_message" class="form-label">Auto-reply Message</label>
    <textarea name="auto_reply_to_tickets_message" id="auto_reply_to_tickets_message"
              class="form-control" rows="4"><?= esc(setting('auto_reply_to_tickets_message','Thank you for contacting us. We will respond shortly.')) ?></textarea>
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-save">
        <i class="fa-solid fa-floppy-disk me-2"></i>Save Ticket Settings
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
