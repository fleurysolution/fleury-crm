<?php 
 $this->Settings_model = model('App\Models\Settings_model'); 
echo form_open(get_uri("projects/save_cloned_project"), array("id" => "project-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="project_id" value="<?php echo $model_info->id; ?>" />

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

        <?php if ($model_info->project_type == "internal_project") { ?>
            <input type="hidden" name="project_type" value="internal_project" />
        <?php } else { ?>
            <input type="hidden" name="project_type" value="client_project" />
            <div class="form-group">
                <div class="row">
                    <label for="client_id" class=" col-md-3"><?php echo app_lang('client'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_dropdown("client_id", $clients_dropdown, array($model_info->client_id), "class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>

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
                <label for="start_date" class=" col-md-3"><?php echo app_lang('start_date'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "start_date",
                        "name" => "start_date",
                        "value" => is_date_exists($model_info->start_date) ? $model_info->start_date : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('start_date'),
                        "autocomplete" => "off"
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="deadline" class=" col-md-3"><?php echo app_lang('deadline'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "deadline",
                        "name" => "deadline",
                        "value" => is_date_exists($model_info->deadline) ? $model_info->deadline : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('deadline'),
                        "autocomplete" => "off"
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="price" class=" col-md-3"><?php echo app_lang('price'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "price",
                        "name" => "price",
                        "value" => $model_info->price ? to_decimal_format($model_info->price) : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('price')
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="project_labels" class=" col-md-3"><?php echo app_lang('labels'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "project_labels",
                        "name" => "labels",
                        "value" => $model_info->labels,
                        "class" => "form-control",
                        "placeholder" => app_lang('labels')
                    ));
                    ?>
                </div>
            </div>
        </div>

         <div class="form-group">
                    <div class="row">
                        <label for="region" class=" col-md-3"><?php echo app_lang('region'); ?></label>
                        <div class=" col-md-9">
                           <select class="form-select" name="region_id" id="region" >
                                            <option value="">Select</option>
                                            <!-- <option value="">Global</option> -->
                                             <?php foreach($regions as $regionData): ?>
                                                <option value="<?= $regionData->id ?>" <?php if($regionData->id==$model_info->region_id){ echo "selected"; }?>> <?= $regionData->name ?></option>
                                             <?php endforeach; ?>
                          </select>
                        </div>
                        <div id='error' class="error"></div>
                    </div>
                </div>
                <?php $OfficeData=$this->Settings_model->get_offices($model_info->region_id);
                $divisions=$this->Settings_model->get_divisions($model_info->office_id); ?>

                <div class="form-group">
                    <div class="row">
                        <label for="office" class=" col-md-3"><?php echo app_lang('Branch'); ?></label>
                        <div class=" col-md-9">
                            <select class="form-select" name="office_id" id="office">
                                            <option value="">Select</option>  
                                             <?php foreach($OfficeData as $Office): ?>
                                                <option value="<?= $Office->id ?>" <?php if($Office->id==$model_info->office_id){ echo "selected"; }?>> <?= $Office->name ?></option>
                                             <?php endforeach; ?>                                        
                            </select>
                        </div>
                        <div id='error' class="error"></div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label for="division" class=" col-md-3"><?php echo app_lang('division'); ?></label>
                        <div class=" col-md-9">
                            <select class="form-select" name="division_id" id="division_id">
                                            <option value="">Select</option>
                                             <?php foreach($divisions as $divisionsData): ?>
                                                <option value="<?= $divisionsData->id ?>" <?php if($divisionsData->id==$model_info->division_id){ echo "selected"; }?>> <?= $divisionsData->name ?></option>
                                             <?php endforeach; ?>                                              
                            </select>
                        </div>
                        <div id='error' class="error"></div>
                    </div>
                </div>

        <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-3", "field_column" => " col-md-9")); ?> 

        <div class="form-group">
            <label for="copy_project_members"class=" col-md-12">
                <?php
                echo form_checkbox("copy_project_members", "1", true, "id='copy_project_members' disabled='disabled' class='float-start mr15 form-check-input'");
                ?>    
                <?php echo app_lang('copy_project_members'); ?>
            </label>
        </div>

        <div class="form-group">
            <label for="copy_tasks"class=" col-md-12">
                <?php
                echo form_checkbox("copy_tasks", "1", true, "id='copy_tasks' disabled='disabled' class='float-start form-check-input'");
                ?>    
                <span class="float-start ml15"> <?php echo app_lang('copy_tasks'); ?> (<?php echo app_lang("task_comments_will_not_be_included"); ?>) </span>
            </label>
        </div>

        <div class="form-group">
            <label for="copy_same_assignee_and_collaborators"class=" col-md-12">
                <?php
                echo form_checkbox("copy_same_assignee_and_collaborators", "1", true, "id='copy_same_assignee_and_collaborators'  class='float-start form-check-input'");
                ?>    
                <span class="float-start ml15"> <?php echo app_lang('copy_same_assignee_and_collaborators'); ?> </span>
            </label>
        </div>

        <div class="form-group">
            <label for="copy_tasks_start_date_and_deadline"class=" col-md-12">
                <?php
                echo form_checkbox("copy_tasks_start_date_and_deadline", "1", false, "id='copy_tasks_start_date_and_deadline'  class='float-start form-check-input'");
                ?>    
                <span class="float-start ml15"> <?php echo app_lang('copy_tasks_start_date_and_deadline'); ?> </span>
            </label>
        </div>

        <div class="form-group">
            <label for="change_the_tasks_start_date_and_deadline_based_on_project_start_date"class=" col-md-12">
                <?php
                echo form_checkbox("change_the_tasks_start_date_and_deadline_based_on_project_start_date", "1", false, "id='change_the_tasks_start_date_and_deadline_based_on_project_start_date'  class='float-start form-check-input'");
                ?>    
                <span class="float-start ml15"> <?php echo app_lang('change_the_tasks_start_date_and_deadline_based_on_project_start_date'); ?> </span>
            </label>
        </div>

        <div class="form-group">
            <label for="copy_milestones"class=" col-md-12">
                <?php
                echo form_checkbox("copy_milestones", "1", false, "id='copy_milestones'  class='float-start form-check-input'");
                ?>    
                <span class="float-start ml15"> <?php echo app_lang('copy_milestones'); ?> </span>
            </label>
        </div>

        <div class="form-group">
            <label for="change_the_milestone_dates_based_on_project_start_date"class=" col-md-12">
                <?php
                echo form_checkbox("change_the_milestone_dates_based_on_project_start_date", "1", false, "id='change_the_milestone_dates_based_on_project_start_date'  class='float-start form-check-input'");
                ?>    
                <span class="float-start ml15"> <?php echo app_lang('change_the_milestone_dates_based_on_project_start_date'); ?> </span>
            </label>
        </div>

        <div class="form-group">
            <label for="move_all_tasks_to_to_do"class=" col-md-12">
                <?php
                echo form_checkbox("move_all_tasks_to_to_do", "1", false, "id='move_all_tasks_to_to_do'  class='float-start form-check-input'");
                ?>    
                <span class="float-start ml15"> <?php echo app_lang('move_all_tasks_to_to_do'); ?> </span>
            </label>
        </div>



    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#project-form").appForm({
            onSuccess: function (result) {
                appAlert.success(result.message);
                setTimeout(function () {
                    window.location = "<?php echo site_url('projects/view'); ?>/" + result.id;
                }, 2000);
            }
        });
        setTimeout(function () {
            $("#title").focus();
        }, 200);
        $("#project-form .select2").select2();

        setDatePicker("#start_date, #deadline");

        $("#project_labels").select2({
            tags: <?php echo json_encode($label_suggestions); ?>
        });

        $("#copy_tasks_start_date_and_deadline").click(function () {
            if (this.checked) {
                $("#change_the_tasks_start_date_and_deadline_based_on_project_start_date").attr("disabled", true);
            } else {
                $("#change_the_tasks_start_date_and_deadline_based_on_project_start_date").removeAttr("disabled");
            }
        });

        $("#change_the_tasks_start_date_and_deadline_based_on_project_start_date").click(function () {
            if (this.checked) {
                $("#copy_tasks_start_date_and_deadline").attr("disabled", true);
            } else {
                $("#copy_tasks_start_date_and_deadline").removeAttr("disabled");
            }
        });

        $("#copy_milestones").click(function () {
            if (this.checked) {
                $("#change_the_milestone_dates_based_on_project_start_date").attr("disabled", true);
            } else {
                $("#change_the_milestone_dates_based_on_project_start_date").removeAttr("disabled");
            }
        });

        $("#change_the_milestone_dates_based_on_project_start_date").click(function () {
            if (this.checked) {
                $("#copy_milestones").attr("disabled", true);
            } else {
                $("#copy_milestones").removeAttr("disabled");
            }
        });

    });
</script>    

<script>
$('#region').change(function() {
    let region_id = $(this).val();
    $('#office').empty();
    $.getJSON('<?= site_url("team_members/get_offices_by_regionID") ?>/' + region_id, function(offices) {
        $('#office').append('<option value="">Select Office</option>');
        $.each(offices, function(i, office) {
            $('#office').append('<option value="'+office.id+'">'+office.name+'</option>');
        });
    });
});

$('#office').change(function() {
    let office_id = $(this).val();
    $('#division_id').empty();
    $.getJSON('<?= site_url("team_members/get_division_by_officeID") ?>/' + office_id, function(divisions) {
        $('#division_id').append('<option value="">Select Division</option>');
        $.each(divisions, function(i, division) {
            $('#division_id').append('<option value="'+division.id+'">'+division.name+'</option>');
        });
    });
});

</script>