<?php echo form_open(get_uri("package_price/save"), array("id" => "category-form", "class" => "general-form", "role" => "form"));
   // print_r($model_info);
 ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <input type="hidden" name="stripe_product_id" value="<?php echo $model_info->stripe_product_id; ?>" />
        <input type="hidden" name="stripe_price_id" value="<?php echo $model_info->stripe_price_id; ?>" />
        <div class="form-group">
            <br />
            <div class="row">
             <div class="form-group">
            <label for="package_status" class="col-md-3">Package Status</label>
            <div class="col-md-12">
                <div class="form-check form-check-inline">
                    <?php
                    echo form_radio(array(
                        "id" => "package_status_active",
                        "name" => "package_status",
                        "value" => '1',
                          "checked" => ($model_info->status == '1') ? true : false,
                        "class" => "form-check-input",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                    <label for="package_status_active" class="form-check-label">Active</label>
                </div>
        
                <div class="form-check form-check-inline">
                    <?php
                    echo form_radio(array(
                        "id" => "package_status_deactive",
                        "name" => "package_status",
                        "value" => '0',
                         "checked" => ($model_info->status == '0') ? true : false,
                        "class" => "form-check-input",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                    <label for="package_status_deactive" class="form-check-label">Deactive</label>
                </div>
            </div>
        </div>


                <div class="form-group">
                <label for="title" class=" col-md-3">Package Name</label>
                <div class="col-md-12">
                    <?php
                    echo form_input(array(
                        "id" => "name",
                        "name" => "package_name",
                        "value" => $model_info->package_name,
                        "class" => "form-control",
                        "placeholder" => app_lang('name'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
           <div class="form-group">
            <label for="title" class="col-md-3">Price</label>
            <div class="col-md-12">
                <?php
                // Check if price exists
                $disabled = !empty($model_info->price) ? 'disabled' : ''; 
        
                // Display the input field if not disabled
                echo form_input(array(
                    "id" => "price",
                    "name" => "price",
                    "value" => $model_info->price,
                    "class" => "form-control",
                    "placeholder" => 'Price',
                    "autofocus" => true,
                    "data-rule-required" => !$disabled ? "true" : "false",  // Validate price if not disabled
                    "data-msg-required" => app_lang("field_required"),
                    $disabled => $disabled  // Add disabled attribute if price exists
                ));
        
                // If the field is disabled, add a hidden input to pass the value
                if ($disabled) {
                    echo form_hidden('price', $model_info->price);
                }
                ?>
            </div>
        </div>


             <div class="form-group">
                <label for="title" class=" col-md-3">Trial Days</label>
                <div class="col-md-12">
                    <?php
                    echo form_input(array( 
                        "id" => "trial_days",
                        "name" => "trial_days",
                        "value" => $model_info->trial_days,
                        "class" => "form-control",
                        "placeholder" => 'Days',
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
            
             <div class="form-group">
                <label for="title" class=" col-md-3">Max No. Of Staff</label>
                <div class="col-md-12">
                    <?php
                    echo form_input(array( 
                        "id" => "max_staff",
                        "name" => "max_staff",
                        "value" => $model_info->max_staff,
                        "class" => "form-control",
                        "placeholder" => 'No Of Staff',
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
             <div class="form-group">
                <label for="title" class=" col-md-3">Max No.Of Clients</label>
                <div class="col-md-12">
                    <?php
                    echo form_input(array( 
                        "id" => "max_clients",
                        "name" => "max_clients",
                        "value" =>  $model_info->max_clients,
                        "class" => "form-control",
                        "placeholder" => 'No Of Client',
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
            
              <div class="form-group">
                <label for="title" class=" col-md-3">Max Invoices Limit</label>
                <div class="col-md-12">
                    <?php
                    echo form_input(array( 
                        "id" => "max_invoices",
                        "name" => "max_invoices",
                        "value" => $model_info->max_invoices,
                        "class" => "form-control",
                        "placeholder" => 'Max Invoices Limits',
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="title" class=" col-md-3">Num Of Projects Limit</label>
                <div class="col-md-12">
                    <?php
                    echo form_input(array( 
                        "id" => "max_projects",
                        "name" => "max_projects",
                        "value" => $model_info->max_projects,
                        "class" => "form-control",
                        "placeholder" => 'Max Projects Limits',
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="title" class=" col-md-3">Storage Limit</label>
                <div class="col-md-12">
                    <?php
                    echo form_input(array( 
                        "id" => "max_storage_mb",
                        "name" => "max_storage_mb",
                        "value" => $model_info->max_storage_mb,
                        "class" => "form-control",
                        "placeholder" => 'Storage Limits',
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

            
             
             <div class="form-group">
                <label for="support" class=" col-md-3">Support</label>
                <div class="col-md-12">
                    <?php
                    echo form_input(array( 
                        "id" => "support",
                        "name" => "support",
                        "value" => $model_info->support,
                        "class" => "form-control",
                        "placeholder" => 'Support',
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label for="project_task_management" class="col-md-3">Project & Task Management</label>
                <div class="col-md-12">
                    <?php
                    echo form_checkbox(array( 
                        "id" => "project_task_management",
                        "name" => "project_task_management",
                        "value" => '1',
                        "checked" => ($model_info->project_task_management == '1') ? true : false,
                        "class" => "form-check-input",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                    <label for="project_task_management" class="form-check-label">Enable</label>
                </div>
            </div>
             <div class="form-group">
                <label for="enhanced_reporting" class="col-md-3">Enhanced Reporting</label>
                <div class="col-md-12">
                    <?php
                    echo form_checkbox(array( 
                        "id" => "enhanced_reporting",
                        "name" => "enhanced_reporting",
                        "value" => '1',
                        "class" => "form-check-input",
                         "checked" => ($model_info->enhanced_reporting == '1') ? true : false,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                    <label for="enhanced_reporting" class="form-check-label">Enable</label>
                </div>
            </div>
             <div class="form-group">
                <label for="collaboration_tools" class="col-md-3">Collaboration Tools</label>
                <div class="col-md-12">
                    <?php
                    echo form_checkbox(array( 
                        "id" => "collaboration_tools",
                        "name" => "collaboration_tools",
                        "value" => '1',
                        "class" => "form-check-input",
                        "checked" => ($model_info->collaboration_tools == '1') ? true : false,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                    <label for="collaboration_tools" class="form-check-label">Enable</label>
                </div>
            </div>
            
            

              <div class="form-group">
                <label for="duration" class="col-md-3">Duration</label>
                <div class="col-md-12">
                    <?php
                    echo form_dropdown(
                        "duration", // Name of the dropdown
                        array(
                            "month" => "Month", // Key-value pairs for options
                            "year" => "Year"
                        ),
                        $model_info->duration, // Selected value
                        array(
                            "id" => "duration",
                            "class" => "form-control",
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required")
                        )
                    );
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label for="title" class=" col-md-3">Payment Link</label>
                <div class="col-md-12">
                    <?php
                    echo form_input(array(
                        "id" => "button_text",
                        "name" => "button_text",
                        "value" => $model_info->payment_button,
                        "class" => "form-control",
                        "placeholder" =>'button text',
                        "disabled" =>true,
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="title" class=" col-md-3">Button Text</label>
                <div class="col-md-12">
                    <?php
                    echo form_input(array(
                        "id" => "button_text",
                        "name" => "button_text",
                        "value" => $model_info->button_text,
                        "class" => "form-control",
                        "placeholder" =>'button text',
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
           
         
            <div class="form-group">
              <label for="categoryDescription"><?php echo app_lang('description'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "description",
                        "name" => "description",
                        "value" => $model_info->description,
                        "class" => "form-control",
                        "placeholder" => app_lang('description'),
                        "data-rich-text-editor" => true
                        ));
                    ?>
                 <!-- <textarea class="form-control" id="categoryDescription" name="description" rows="3" placeholder="Enter category description"></textarea> -->
                </div>
            </div>


        
            </div>
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
        $("#category-form").appForm({
            onSuccess: function (result) {
                $("#category-table").appTable({newData: result.data, dataId: result.id});
            }
        });
        setTimeout(function () {
            $("#title").focus();
        }, 200);
    });
</script>