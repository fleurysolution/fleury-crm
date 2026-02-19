<div class="modal-body clearfix">
    <form id="approval-settings-form" class="general-form" role="form">
        <input type="hidden" name="id" value="<?= $model_info->id ?>" />

        <div class="form-group">
            <label>Module</label>
            <select name="module" class="form-control" required>
                <option value="">Select Module</option>
                <?php foreach ($modules as $module): ?>
                    <option value="<?= $module ?>" <?= $model_info->module == $module ? 'selected' : '' ?>>
                        <?= ucfirst(str_replace('_', ' ', $module)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Requester</label>
            <select name="requester_user_id" class="form-control" required>
                <option value="">Select User</option>
                <?php foreach ($users_dropdown as $id => $name): ?>
                    <option value="<?= $id ?>" <?= $model_info->requester_user_id == $id ? 'selected' : '' ?>><?= $name ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Approver</label>
            <select name="approver_user_id" class="form-control" required>
                <option value="">Select User</option>
                <?php foreach ($users_dropdown as $id => $name): ?>
                    <option value="<?= $id ?>" <?= $model_info->approver_user_id == $id ? 'selected' : '' ?>><?= $name ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Hierarchy Level</label>
            <input type="number" class="form-control" name="hierarchy_level" value="<?= $model_info->hierarchy_level ?>" min="1" placeholder="1 for lowest, 3 for highest etc.">
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="require_all_approvers" value="1" <?= $model_info->require_all_approvers ? 'checked' : '' ?>> Require all approvers
            </label>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="active" value="1" <?= $model_info->active ? 'checked' : '' ?>> Active
            </label>
        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-primary" id="save-approval-setting">Save</button>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#save-approval-setting').click(function() {
            appLoader.show();
            $.ajax({
                url: '<?= site_url('approvalSettings/save') ?>',
                type: 'POST',
                data: $('#approval-settings-form').serialize(),
                dataType: 'json',
                success: function (result) {
                    appLoader.hide();
                    if (result.success) {
                        appAlert.success(result.message);
                        location.reload();
                    } else {
                        appAlert.error(result.message);
                    }
                }
            });
        });
    });
</script>
