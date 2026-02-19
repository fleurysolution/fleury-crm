<?php echo form_open(get_uri("clients/save"), array("id" => "client-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="ticket_id" value="<?php echo $ticket_id ?? ''; ?>" />
        <?php echo view("clients/client_form_fields"); ?>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <?php if (empty($model_info->id)) { ?>
        <button type="button" id="save-and-continue-button" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save_and_continue'); ?></button>
    <?php } ?>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        var ticket_id = "<?php echo $ticket_id ?? ''; ?>";

        window.clientForm = $("#client-form").appForm({
            closeModalOnSuccess: false,
            onSuccess: function (result) {
                if (result.view === "details" || ticket_id) {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                } else {
                    $("#client-table").appTable({newData: result.data, dataId: result.id});
                    window.clientForm.closeModal();
                }
            }
        });
        setTimeout(function () {
            $("#company_name").focus();
        }, 200);

        $("#save-and-continue-button").click(function () {
            $(this).trigger("submit");
        });
    });
</script>
