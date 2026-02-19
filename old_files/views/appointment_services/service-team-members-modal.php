<?php echo form_open(get_uri("appointment_services/save_service_team_members"), ["id" => "service-team-form", "class" => "general-form", "role" => "form"]); ?>

<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="service_id" value="<?php echo esc($service_id); ?>" />

        <div class="mb-2">
            <strong>Eligible staff for this service</strong>
            <div class="text-muted">Only selected staff will be considered for round-robin assignment.</div>
        </div>

        <div class="table-responsive" style="max-height: 420px; overflow:auto;">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th style="width:60px;">Select</th>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($staff_list as $s): 
                    $checked = in_array((int)$s->id, $mapped_ids, true) ? "checked" : "";
                    $full = trim($s->first_name . " " . $s->last_name);
                ?>
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="team_member_ids[]" value="<?php echo (int)$s->id; ?>" <?php echo $checked; ?> />
                        </td>
                        <td><?php echo esc($full); ?></td>
                        <td><?php echo esc($s->email); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal">
        <span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?>
    </button>
    <button type="submit" class="btn btn-primary">
        <span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?>
    </button>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
$(document).ready(function () {
    $("#service-team-form").appForm({
        onSuccess: function (result) {
            appAlert.success(result.message, {duration: 4000});
            window.refreshAfterUpdate = true;
        }
    });
});
</script>
