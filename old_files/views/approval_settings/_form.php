<form action="<?= site_url('approvalsettings/save') ?>" method="post" class="row g-3 mt-3"  id="approvalSettingForm" >
    <input type="hidden" name="id" id="setting_id">

    <div class="col-md-4">
        <label for="module" class="form-label">Module</label>
        <select name="module" id="module" class="form-select" required>
            <option value="">Select Module</option>
            <?php foreach ($modules as $module): ?>
                <option value="<?= esc($module) ?>"><?= ucfirst(str_replace('_', ' ', $module)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label for="requester_role" class="form-label">Requester Role</label>
        <select name="requester_role" id="requester_role" class="form-select">
            <option value="">Select Role</option>
            <?php foreach ($roles as $role): ?>
                <option value="<?= esc($role) ?>"><?= ucfirst($role) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label for="approver_role" class="form-label">Approver Role</label>
        <select name="approver_role" id="approver_role" class="form-select">
            <option value="">Select Role</option>
            <?php foreach ($roles as $role): ?>
                <option value="<?= esc($role) ?>"><?= ucfirst($role) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label for="requester_user_id" class="form-label">Requester User</label>
        <select name="requester_user_id" id="requester_user_id" class="form-select">
            <option value="">Select User</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user->id ?>">
                    <?= esc($user->first_name . ' ' . $user->last_name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label for="approver_user_id" class="form-label">Approver User</label>
        <select name="approver_user_id" id="approver_user_id" class="form-select">
            <option value="">Select User</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user->id ?>">
                    <?= esc($user->first_name . ' ' . $user->last_name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-2">
        <label for="hierarchy_level" class="form-label">Hierarchy Level</label>
        <input type="number" name="hierarchy_level" id="hierarchy_level" class="form-control" min="0" value="0">
    </div>

    <div class="col-md-2 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="require_all_approvers" id="require_all_approvers" value="1">
            <label class="form-check-label" for="require_all_approvers">Require All</label>
        </div>
    </div>

    <div class="col-md-2 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="active" id="active" value="1" checked>
            <label class="form-check-label" for="active">Active</label>
        </div>
    </div>

    <div class="col-12 text-end">
        <button type="submit" class="btn btn-success px-4">Save</button>
    </div>
</form>


<script>
$(document).on('submit', '#approvalSettingForm', function(e) {
    e.preventDefault();

    appLoader.show(); // shows Rise loading spinner

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (response) {
            appLoader.hide();

            if (response.success) {
                appAlert.success(response.message, {duration: 4000});

                // Optional: reload the table or page section
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                appAlert.error(response.message || "Something went wrong", {duration: 4000});
            }
        },
        error: function () {
            appLoader.hide();
            appAlert.error("Server error occurred. Please try again.", {duration: 4000});
        }
    });
});

</script>