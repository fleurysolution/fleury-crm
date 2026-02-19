<div class="table-responsive mt-3">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Module</th>
                <th>Requester</th>
                <th>Approver</th>
                <th>Hierarchy</th>
                <th>Require All</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($settings)): ?>
                <?php $sr=1; foreach ($settings as $i => $s): ?>
                    <tr>
                        <td><?= $sr ?></td>
                        <td><?= ucfirst(str_replace('_', ' ', $s->module)) ?></td>
                        <td><?= esc($s->requester_role ?: 'User ID ' . $s->requester_user_id) ?></td>
                        <td><?= esc($s->approver_role ?: 'User ID ' . $s->approver_user_id) ?></td>
                        <td><?= esc($s->hierarchy_level) ?></td>
                        <td><?= $s->require_all_approvers ? 'Yes' : 'No' ?></td>
                        <td><?= $s->active ? 'Yes' : 'No' ?></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-warning edit-setting-btn" data-id="<?= $s->id ?>">Edit</a>
                            
                               <a href="#" 
                               class="btn btn-sm btn-danger delete-approval-setting" 
                               data-id="<?= $s->id ?>">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php $sr++; endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">No approval settings found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<script>
$(document).on('click', '.edit-setting-btn', function() {
    const id = $(this).data('id');
    $.ajax({
        url: "<?php echo base_url( 'approvalsettings/get_setting');?>/" + id,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data) {
                // Fill the form fields dynamically
                $('#approvalFormSection').collapse('show');
                $('#setting_id').val(data.id);
                $('#module').val(data.module);
                $('#requester_role').val(data.requester_role);
                $('#approver_role').val(data.approver_role);
                $('#hierarchy_level').val(data.hierarchy_level);
                $('#require_all_approvers').prop('checked', data.require_all_approvers == 1);
                $('#active').prop('checked', data.active == 1);
            }
        }
    });
});


$(document).on('click', '.delete-approval-setting', function (e) {
    e.preventDefault();

    var id = $(this).data('id');

    if (!confirm('Are you sure you want to delete this setting?')) return;

    appLoader.show(); // show Rise loader

    $.ajax({
        url: '<?= site_url("approvalsettings/delete") ?>/' + id,
        type: 'POST',
        dataType: 'json',
        success: function (response) {
            appLoader.hide();

            if (response.success) {
                appAlert.success(response.message, {duration: 4000});

                // Remove the deleted row from table
                $('a.delete-approval-setting[data-id="' + id + '"]').closest('tr').fadeOut(400, function() {
                    $(this).remove();
                });
            } else {
                appAlert.error(response.message || "Failed to delete setting.", {duration: 4000});
            }
        },
        error: function () {
            appLoader.hide();
            appAlert.error("Server error occurred.", {duration: 4000});
        }
    });
});

</script>