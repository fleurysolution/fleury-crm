<?php echo form_open(get_uri("projects/save_milestone"), array("id" => "milestone-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
        <div class="form-group">
            <div class="row">
                <label for="title" class=" col-md-3"><?php echo app_lang('title'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "title",
                        "name" => "title",
                        "value" => $model_info->title,
                        "class" => "form-control",
                        "placeholder" => app_lang('title'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="description" class=" col-md-3"><?php echo app_lang('description'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "description",
                        "name" => "description",
                        "value" => process_images_from_content($model_info->description, false),
                        "class" => "form-control",
                        "placeholder" => app_lang('description'),
                        "data-rich-text-editor" => true
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="due_date" class=" col-md-3"><?php echo app_lang('due_date'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "due_date",
                        "name" => "due_date",
                        "value" => $model_info->due_date,
                        "class" => "form-control",
                        "placeholder" => app_lang('due_date'),
                        "autocomplete" => "off",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required")
                    ));
                    ?>
                </div>
            </div>
        </div>

         <div class="table-responsive">
            <table id="milestone-table-model" class="display" width="100%">            
            </table>
        </div>


    </div>
</div>

<!-- <div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div> -->

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal">
        <span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?>
    </button>

    <button type="button" id="save-and-add-another" class="btn btn-success">
        <span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('save_and_add_another'); ?>
    </button>

    <button type="button" id="save-and-add-tasks" class="btn btn-info text-white">
        <span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save_and_add_tasks'); ?>
    </button>
    <div id="link-of-add-task-modal" class="hide">
        <?php echo modal_anchor(get_uri("tasks/modal_form"), "", array()); ?>
    </div>
    <div id="link-of-add-milestone-modal" class="hide">
        <?php echo modal_anchor(get_uri("projects/milestone_modal_form"), "", array()); ?>
    </div>
    <!-- <button type="submit" class="btn btn-primary">
        <span data-feather="check-circle" class="icon-16"></span> <?php //echo app_lang('save'); ?>
    </button> -->
</div>

<?php echo form_close(); ?>

<!-- <script type="text/javascript">
    $(document).ready(function () {
        $("#milestone-form").appForm({
            onSuccess: function (result) {
                $("#milestone-table").appTable({newData: result.data, dataId: result.id});
            }
        });
        setTimeout(function () {
            $("#title").focus();
        }, 200);

        setDatePicker("#due_date");

    });
</script>     -->
<script type="text/javascript">
    $(document).ready(function () {
        var nextAction = "close"; // default

        $("#milestone-form").appForm({
            closeModalOnSuccess: false,
            onSuccess: function (result) {
                $("#milestone-table").appTable({ newData: result.data, dataId: result.id });

                if (nextAction === "add_another") {
                    showAddMilestoneModal();  // 🔥 open another milestone modal
                } 
                else if (nextAction === "add_tasks") {
                    showAddMultipleTaskModal();
                } 
                else {
                    window.milestoneModal.closeModal();
                }
            }
        });

        // Keep modal reference
        window.milestoneModal = $("#ajaxModal");

        // Focus and datepicker
        setTimeout(function () {
            $("#title").focus();
        }, 200);
        setDatePicker("#due_date");

        // Buttons
        $("#save-and-add-another").click(function () {
            nextAction = "add_another";
            $("#milestone-form").trigger("submit");
        });

        $("#save-and-add-tasks").click(function () {
            nextAction = "add_tasks";
            $("#milestone-form").trigger("submit");
        });

        // Opens Add Multiple Task Modal
        function showAddMultipleTaskModal() {
            var $addTaskLink = $("#link-of-add-task-modal").find("a");

            $addTaskLink.attr("data-post-project_id", <?php echo $project_id; ?>);
            $addTaskLink.attr("data-title", "<?php echo app_lang('add_multiple_tasks'); ?>");
            $addTaskLink.attr("data-post-add_type", "multiple");

            $addTaskLink.trigger("click");
            window.milestoneModal.closeModal();
        }

        // Opens Another Milestone Modal
        function showAddMilestoneModal() {
            var $addMilestoneLink = $("#link-of-add-milestone-modal").find("a");

            $addMilestoneLink.attr("data-post-project_id", <?php echo $project_id; ?>);
            $addMilestoneLink.attr("data-title", "<?php echo app_lang('add_milestone'); ?>");

            $addMilestoneLink.trigger("click");
            window.milestoneModal.closeModal();
        }

        var optionVisibility = false;
        if ("<?php echo ($can_edit_milestones || $can_delete_milestones); ?>") {
            optionVisibility = true;
        }
        $("#milestone-table-model").appTable({
            source: '<?php echo_uri("projects/milestones_list_data_for_model/" . $project_id) ?>',
            order: [[0, "dasc"]],
            columns: [
                {visible: false, searchable: false},
                {title: "<?php echo app_lang("due_date") ?>", "class": "text-center w100 all", "iDataSort": 0},
                {title: "<?php echo app_lang("title") ?>", "class": "all"},
            ],
          
        });
    });
</script>
