<!-- Project amendment form -->
<?php echo form_open(get_uri("projects/save_project_amendment"), array("id" => "project-amendment-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />

      


        <div class="form-group" style="min-height: 50px">
            <div class="row">
               
                <label for="amendedPrice" class="form-label"><?php echo app_lang('amended_price'); ?></label>
                <div class="col-md-9">
                    <div class="select-amendment-field">
                        <div class="select-amendment-form clearfix pb10">
                            <input type="number" step="0.01" id="amendedPrice" name="amended_price" class="form-control" placeholder="Enter amended price" required>
                        </div>                                
                    </div>                    
                </div>

            </div>
        </div>
        <div class="form-group" style="min-height: 50px">
            <div class="row">
             
                            <label for="amendedPrice" class="form-label"><?php echo app_lang('amended_reason'); ?></label>
                <div class="col-md-9">
                    <div class="select-amendment-field">
                        <div class="select-amendment-form clearfix pb10">
                            <textarea id="reason" name="reason" class="form-control" rows="3" placeholder="Provide reason for amendment" required></textarea>

                        </div>                                
                    </div>                    
                </div>
                
            </div>
        </div>


    </div>
</div>

<div class="modal-footer">
  

    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>

    <?php if ($view_type == "from_project_modal") { ?>
        <button type="button" id="next-button" class="btn btn-info text-white"><span data-feather="arrow-right-circle" class="icon-16"></span> <?php echo app_lang('skip'); ?></button>
        <button type="button" id="save-and-continue-button" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save_and_continue'); ?></button>
    <?php } else { ?>
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    <?php } ?>

</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        window.projectamendmentForm = $("#project-amendment-form").appForm({
            closeModalOnSuccess: false,
            onSuccess: function (result) {
                if (result.id !== "exists") {
                    for (i = 0; i < result.data.length; i++) {
                            $("#project-amendment-table").appTable({newData: result.data[i], dataId: result.id[i]});
                    }
                }

                if (window.showAddMultipleTasksModal) {
                    showAddMultipleTaskModal();
                } else {
                    window.projectamendmentForm.closeModal();
                }
            }
        });

        var $wrapper = $('.select-amendment-field'),
                $field = $('.select-amendment-form:first-child', $wrapper).clone(); //keep a clone for future use.

        $(".add-amendment", $(this)).click(function (e) {
            var $newField = $field.clone();

            //remove used options
            $('.user_select2').each(function () {
                $newField.find("option[value='" + $(this).val() + "']").remove();
            });

            var $newObj = $newField.appendTo($wrapper);
            $newObj.find(".user_select2").select2();

            $newObj.find('.remove-amendment').click(function () {
                $(this).parent('.select-amendment-form').remove();
                showHideAddMore($field);
            });

            showHideAddMore($field);
        });

        showHideAddMore($field);

        $(".remove-amendment").hide();
        $(".user_select2").select2();

        function showHideAddMore($field) {
            //hide add more button if there are no options 
            if ($('.select-amendment-form').length < $field.find("option").length) {
                $("#add-more-user").show();
            } else {
                $("#add-more-user").hide();
            }
        }

        //open add multiple task modal
        window.showAddMultipleTasksModal = false;

        $("#save-and-continue-button").click(function () {
            window.showAddMultipleTasksModal = true;
            $(this).trigger("submit");
        });

        $("#next-button").click(function () {
            showAddMultipleTaskModal();
        });

        function showAddMultipleTaskModal() {
            var $addMultipleTasksLink = $("#link-of-add-task-modal").find("a");
            $addMultipleTasksLink.attr("data-post-project_id", <?php echo $project_id; ?>);
            $addMultipleTasksLink.attr("data-title", "<?php echo app_lang('add_multiple_tasks') ?>");
            $addMultipleTasksLink.attr("data-post-add_type", "multiple");

            $addMultipleTasksLink.trigger("click");
        }

    });
</script>    