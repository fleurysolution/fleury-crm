<?php echo form_open(get_uri("appointment_services/save"), array("id" => "item-form", "class" => "general-form", "role" => "form")); ?>
<div id="appointment-dropzone" class="appointments-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

            <?php if ($model_info->id) { ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 text-off"> <?php echo app_lang('appointment_edit_instruction'); ?></div>
                    </div>
                </div>
            <?php } ?>

            <div class="form-group">
                <div class="row">
                    <label for="title" class=" col-md-3"><?php echo app_lang('name'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "name",
                            "name" => "name",
                            "value" => $model_info->name,
                            "class" => "form-control validate-hidden",
                            "placeholder" => app_lang('name'),
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
                    <label for="title" class=" col-md-3"><?php echo app_lang('email'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "email",
                            "name" => "email",
                            "value" => $model_info->email,
                            "class" => "form-control validate-hidden",
                            "placeholder" => app_lang('email'),
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
                    <label for="title" class=" col-md-3"><?php echo app_lang('phone'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "phone",
                            "name" => "phone",
                            "value" => $model_info->phone,
                            "class" => "form-control validate-hidden",
                            "placeholder" => app_lang('phone'),
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
                    <label for="title" class=" col-md-3"><?php echo app_lang('start_time'); ?></label>
                    <div class="col-md-9">
                        <input type="datetime-local" name="start_time" id="start_time"  autofocus="true" data-rule-required="true" class="form-control  validate-hidden" data-msg-required="<?php echo app_lang("field_required"); ?>" value="<?php echo $model_info->start_time; ?>">
                       
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="title" class=" col-md-3"><?php echo app_lang('end_time'); ?></label>
                    <div class="col-md-9">
                        <input type="datetime-local" name="end_time" id="end_time"  autofocus="true" data-rule-required="true" class="form-control  validate-hidden" data-msg-required="<?php echo app_lang("field_required"); ?>" value="<?php echo $model_info->end_time; ?>">
                       
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="title" class=" col-md-3"><?php echo app_lang('meeting_link'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "meeting_link",
                            "name" => "meeting_link",
                            "value" => $model_info->meeting_link,
                            "class" => "form-control validate-hidden",
                            "placeholder" => app_lang('meeting_link'),
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
                    <label for="description" class="col-md-3"><?php echo app_lang('description'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_textarea(array(
                            "id" => "description",
                            "name" => "description",
                            "value" => $model_info->description ? process_images_from_content($model_info->description, false) : "",
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
                    <label for="description" class="col-md-3"><?php echo app_lang('notes'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_textarea(array(
                            "id" => "notes",
                            "name" => "notes",
                            "value" => $model_info->notes ? process_images_from_content($model_info->notes, false) : "",
                            "class" => "form-control",
                            "placeholder" => app_lang('notes'),
                            "data-rich-text-editor" => true
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="payment_status" class=" col-md-3"><?php echo app_lang('payment_status'); ?></label>
                    <div class=" col-md-9">
                        <select name="payment_status" class="form-select">
                            <option value="unpaid" <?php if($model_info->payment_status=='unpaid'){ echo 'selected'; } ?>>Unpaid</option>
                            <option value="paid" <?php if($model_info->payment_status=='paid'){ echo 'selected'; } ?>>Paid</option>
                        </select>                      
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="payment_status" class=" col-md-3"><?php echo app_lang('status'); ?></label>
                    <div class=" col-md-9">
                        <select name="status" class="form-select">
                            <option value="pending" <?php if($model_info->payment_status=='pending'){ echo 'selected'; } ?>>Pending</option>
                            <option value="confirmed" <?php if($model_info->payment_status=='confirmed'){ echo 'selected'; } ?>>Confirmed</option>
                            <option value="cancelled" <?php if($model_info->payment_status=='cancelled'){ echo 'selected'; } ?>>Cancelled</option>
                            <option value="completed" <?php if($model_info->payment_status=='completed'){ echo 'selected'; } ?>>Completed</option>
                        </select>                      
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="category_id" class=" col-md-3"><?php echo app_lang('services'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        if(!empty($services_dropdown)){
                        echo form_dropdown("service_id", $services_dropdown, $model_info->service_id, "class='select2 validate-hidden  form-select' id='service_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="category_id" class=" col-md-3"><?php echo app_lang('category'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_dropdown("category_id", $categories_dropdown, $model_info->category_id, "class='select2 validate-hidden form-select' id='category_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        ?>
                    </div>
                </div>
            </div>
          

         

        </div>
    </div>

    <div class="modal-footer">
       
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        //var uploadUrl = "<?php echo get_uri("uploader/upload_file"); ?>";
        //var validationUri = "<?php echo get_uri("uploader/validate_image_file"); ?>";

        //var dropzone = attachDropzoneWithForm("#materials-dropzone", uploadUrl, validationUri);

        $("#item-form").appForm({
            onSuccess: function (result) {
                if (window.refreshAfterUpdate) {
                    window.refreshAfterUpdate = false;
                    location.reload();
                } else {
                    location.reload();
                    $("#item-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });

        $("#item-form .select2").select2();
    });
</script>