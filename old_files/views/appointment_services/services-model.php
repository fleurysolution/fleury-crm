<?php echo form_open(get_uri("appointment_services/save_services"), array("id" => "item-form", "class" => "general-form", "role" => "form")); ?>
<div id="services-dropzone" class="post-dropzone">
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
                    <label for="title" class=" col-md-3"><?php echo app_lang('title'); ?></label>
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
                    <label for="category_id" class=" col-md-3"><?php echo app_lang('category'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_dropdown("category_id", $categories_dropdown, $model_info->category_id, "class='select2 validate-hidden' id='category_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="duration_minutes" class=" col-md-3"><?php echo app_lang('duration_minutes'); ?></label>
                    <div class=" col-md-9">
                        
                        <select name="duration_minutes" class="select2 validate-hidden">
                          <?php
                            $dm = (int)($model_info->duration_minutes ?? 30);
                            foreach ([15,30,45,60,90] as $v) {
                              $sel = ($dm === $v) ? "selected" : "";
                              echo "<option value='{$v}' {$sel}>{$v} Minutes</option>";
                            }
                          ?>
                        </select>

                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="price" class=" col-md-3"><?php echo app_lang('price'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "price",
                            "name" => "price",
                            "value" => $model_info->price,
                            "class" => "form-control",
                        ));
                        ?>
                    </div>
                </div>
            </div>
           
           <div class="form-group">
              <div class="row">
                <label class="col-md-3"><?php echo app_lang('status'); ?></label>
                <div class="col-md-9">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?php echo ($model_info->is_active ?? 1) ? "checked" : ""; ?>>
                    <label class="form-check-label" for="is_active">Active (visible for booking)</label>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <label class="col-md-3">Payment</label>
                <div class="col-md-9">
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="requires_payment" id="requires_payment" value="1" <?php echo ($model_info->requires_payment ?? 1) ? "checked" : ""; ?>>
                    <label class="form-check-label" for="requires_payment">Requires payment (if payable)</label>
                  </div>

                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="allow_free_booking" id="allow_free_booking" value="1" <?php echo ($model_info->allow_free_booking ?? 0) ? "checked" : ""; ?>>
                    <label class="form-check-label" for="allow_free_booking">Allow free booking (admin can waive payment)</label>
                  </div>
                  <small class="text-muted">If enabled, appointments can be confirmed without payment even when price is greater than zero.</small>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <label class="col-md-3">Assignment</label>
                <div class="col-md-9">
                  <select name="assignment_mode" class="select2 form-control">
                    <option value="">Inherit from category</option>
                    <option value="round_robin" <?php echo (($model_info->assignment_mode ?? '') === 'round_robin') ? "selected" : ""; ?>>Round-robin</option>
                    <option value="manual" <?php echo (($model_info->assignment_mode ?? '') === 'manual') ? "selected" : ""; ?>>Manual</option>
                  </select>
                  <small class="text-muted">Round-robin automatically assigns an eligible team member. Manual keeps it unassigned until admin assigns.</small>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <label class="col-md-3">Scheduling</label>
                <div class="col-md-9">
                  <div class="row">
                    <div class="col-md-6 mb-2">
                      <label class="form-label">Slot interval (minutes)</label>
                      <select name="slot_interval_minutes" class="form-control">
                        <?php
                          $slot = (int)($model_info->slot_interval_minutes ?? 15);
                          foreach ([5,10,15,30,60] as $v) {
                            $sel = ($slot === $v) ? "selected" : "";
                            echo "<option value='{$v}' {$sel}>{$v}</option>";
                          }
                        ?>
                      </select>
                    </div>

                    <div class="col-md-6 mb-2">
                      <label class="form-label">Min notice (minutes)</label>
                      <input type="number" name="min_notice_minutes" class="form-control" value="<?php echo esc($model_info->min_notice_minutes ?? 0); ?>">
                    </div>

                    <div class="col-md-6 mb-2">
                      <label class="form-label">Buffer before (minutes)</label>
                      <input type="number" name="buffer_before_minutes" class="form-control" value="<?php echo esc($model_info->buffer_before_minutes ?? 0); ?>">
                    </div>

                    <div class="col-md-6 mb-2">
                      <label class="form-label">Buffer after (minutes)</label>
                      <input type="number" name="buffer_after_minutes" class="form-control" value="<?php echo esc($model_info->buffer_after_minutes ?? 0); ?>">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Max advance booking (days)</label>
                      <input type="number" name="max_advance_days" class="form-control" value="<?php echo esc($model_info->max_advance_days ?? 365); ?>">
                    </div>
                  </div>
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
        <?php if (!empty($model_info->id)) { ?>
                <?php echo modal_anchor(
                    get_uri("appointment_services/modal_service_team_members"),
                    "<i data-feather='users' class='icon-16'></i> Eligible Staff",
                    ["class" => "btn btn-default", "title" => "Eligible Staff", "data-post-service_id" => $model_info->id]
                ); ?>
            <?php } ?>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        var uploadUrl = "<?php echo get_uri("uploader/upload_file"); ?>";
        var validationUri = "<?php echo get_uri("uploader/validate_image_file"); ?>";

        var dropzone = attachDropzoneWithForm("#services-dropzone", uploadUrl, validationUri);

        $("#item-form").appForm({
            onSuccess: function (result) {
                if (window.refreshAfterUpdate) {
                    window.refreshAfterUpdate = false;
                    location.reload();
                } else {
                    $("#item-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });

        $("#item-form .select2").select2();
    });
</script>