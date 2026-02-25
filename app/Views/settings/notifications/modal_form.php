<!-- Notification Settings Modal Form (AJAX partial) -->
<form id="notifEditForm" class="settings-ajax-form">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= esc($model_info->id) ?>">

    <h6 class="fw-bold mb-3"><?= esc(ucfirst(str_replace('_', ' ', $model_info->event))) ?></h6>
    <p class="text-muted small">Category: <span class="badge bg-info"><?= esc(ucfirst($model_info->category)) ?></span></p>

    <!-- Channels -->
    <div class="mb-3">
        <label class="form-label fw-semibold">Notification Channels</label>
        <div class="d-flex gap-3">
            <?php foreach (['enable_email' => 'Email', 'enable_web' => 'Web', 'enable_slack' => 'Slack'] as $k => $lbl): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="<?= $k ?>" id="<?= $k ?>_<?= $model_info->id ?>" value="1"
                        <?= $model_info->$k ? 'checked' : '' ?>>
                    <label class="form-check-label" for="<?= $k ?>_<?= $model_info->id ?>"><?= esc($lbl) ?></label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Notify To Terms -->
    <div class="mb-3">
        <label class="form-label fw-semibold">Notify To</label>
        <div class="row g-2">
            <?php foreach ($available_terms as $term): ?>
                <?php if (!in_array($term, ['team', 'team_members'])): ?>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="<?= $term ?>" id="term_<?= $term ?>_<?= $model_info->id ?>"
                                value="1" <?= in_array($term, $selected_terms) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="term_<?= $term ?>_<?= $model_info->id ?>">
                                <?= esc(ucfirst(str_replace('_', ' ', $term))) ?>
                            </label>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>

    <script>
    document.getElementById('notifEditForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('<?= site_url('settings/save_notification_settings') ?>', {
            method: 'POST', body: new FormData(this),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json()).then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('notifModal')).hide();
                notifTable.ajax.reload(null, false);
            } else {
                alert(data.message);
            }
        });
    });
    </script>
</form>
