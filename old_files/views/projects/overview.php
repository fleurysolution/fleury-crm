<div class="clearfix default-bg">
<?php //echo echo modal_anchor(get_uri("projects/project_member_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_member'), array("class" => "btn btn-default float-end add-member-button", "title" => app_lang('add_member'), "data-post-project_id" => $project_id,"data-post-region_id" => $region_id,"data-post-office_id" => $office_id,"data-post-division_id" => $division_id)); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <?php echo view("projects/project_progress_chart_info"); ?>
                </div>
                <div class="col-md-6 col-sm-12">
                    <?php echo view("projects/project_task_pie_chart"); ?>
                </div>

                <?php if (get_setting('module_project_timesheet')) { ?>
                    <div class="col-md-12 col-sm-12">
                        <?php echo view("projects/widgets/total_hours_worked_widget"); ?>
                    </div>
                <?php } ?>
                <div class="col-md-12 col-sm-12 project-custom-fields">
                    <?php echo view('projects/additional_details'); ?>
                </div>
                <div class="col-md-12 col-sm-12 project-custom-fields">
                    <?php echo view('projects/custom_fields_list', array("custom_fields_list" => $custom_fields_list)); ?>
                </div>
                

                <?php if ($project_info->estimate_id) { ?>
                    <div class="col-md-12 col-sm-12">
                        <?php echo view("projects/estimates/index"); ?>
                    </div>
                <?php } ?>

                <?php if ($project_info->order_id) { ?>
                    <div class="col-md-12 col-sm-12">
                        <?php echo view("projects/orders/index"); ?>
                    </div>
                <?php } ?>

                <?php if ($project_info->proposal_id) { ?>
                    <div class="col-md-12 col-sm-12">
                        <?php echo view("projects/proposals/index"); ?>
                    </div>
                <?php } ?>

                <?php if ($can_add_remove_project_members) { 
                    $project_attribtues['project_infos']=$project_info;
                    ?>
                    <div class="col-md-12 col-sm-12">
                        <?php echo view("projects/project_members/index",$project_attribtues); ?>
                    </div>
                <?php } ?>

                <?php if ($can_access_clients && $project_info->project_type === "client_project") { ?>
                    <div class="col-md-12 col-sm-12">
                        <?php echo view("projects/client_contacts/index"); ?>
                    </div>
                <?php } ?>

                <?php if ($can_access_clients && $project_info->project_type === "client_project") { ?>
                    <div class="col-md-12 col-sm-12">
                        <?php echo view("projects/project_amendment/index"); ?>
                    </div>
                <?php } ?>

                <div class="col-md-12 col-sm-12">
                    <?php echo view("projects/project_description"); ?>
                </div>

            </div>
        </div>
        <div class="col-md-6">
            <div class="card project-activity-section">
                <div class="card-header">
                    <h4><?php echo app_lang('activity'); ?></h4>
                </div>
                <?php echo view("projects/history/index"); ?>
            </div>
        </div>
    </div>
</div>