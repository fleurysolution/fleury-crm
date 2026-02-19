<?php echo form_open(get_uri("appointment_services/save_categories"), array("id" => "item-form", "class" => "general-form", "role" => "form")); ?>
<div id="categories-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

            <?php if ($model_info->id) { ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12 text-off"> <?php echo app_lang('item_edit_instruction'); ?></div>
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
                    <label for="title" class=" col-md-3"><?php echo app_lang('display_order'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "display_order",
                            "name" => "display_order",
                            "value" => $model_info->display_order,
                            "class" => "form-control validate-hidden",
                            "placeholder" => app_lang('display_order'),
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
                    <label class="col-md-3">Status</label>
                    <div class="col-md-9">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?php echo ((int)($model_info->is_active ?? 1) === 1) ? "checked" : ""; ?>>
                        <label class="form-check-label" for="is_active">Active</label>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="row">
                    <label class="col-md-3">Default Payment</label>
                    <div class="col-md-9">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="default_allow_free" id="default_allow_free" value="1" <?php echo ((int)($model_info->default_allow_free ?? 0) === 1) ? "checked" : ""; ?>>
                        <label class="form-check-label" for="default_allow_free">Allow free booking in this category</label>
                      </div>
                      <small class="text-muted">Services can still override this rule.</small>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="row">
                    <label class="col-md-3">Default Assignment</label>
                    <div class="col-md-9">
                      <select name="default_assignment_mode" class="select2 form-control">
                        <?php
                          $mode = $model_info->default_assignment_mode ?? 'round_robin';
                        ?>
                        <option value="round_robin" <?php echo ($mode === 'round_robin') ? "selected" : ""; ?>>Round-robin</option>
                        <option value="manual" <?php echo ($mode === 'manual') ? "selected" : ""; ?>>Manual</option>
                      </select>
                    </div>
                  </div>
                </div>

            

            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 row pr0">
                        <?php
                        echo view("includes/file_list", array("files" => $model_info->files));
                        ?>
                    </div>
                </div>
            </div>

            <?php echo view("includes/dropzone_preview"); ?>

        </div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-default upload-file-button float-start btn-sm round me-auto" type="button" style="color:#7988a2"><i data-feather="camera" class="icon-16"></i> <?php echo app_lang("upload_image"); ?></button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        var uploadUrl = "<?php echo get_uri("uploader/upload_file"); ?>";
        var validationUri = "<?php echo get_uri("uploader/validate_image_file"); ?>";

        var dropzone = attachDropzoneWithForm("#categories-dropzone", uploadUrl, validationUri);

        $("#item-form").appForm({
            onSuccess: function (result) {
                if (window.refreshAfterUpdate) {
                    window.refreshAfterUpdate = false;
                    location.reload();
                } else {
                    $("#service-categories-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });

        $("#item-form .select2").select2();
    });
</script>