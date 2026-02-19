<?php echo form_open(get_uri("material_categories/area_save"), array("id" => "area-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <div class="form-group">
            <br />
            <div class="row">
                <div class="form-group">
                <label for="title" class=" col-md-3"><?php echo app_lang('title'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "areaname",
                        "name" => "areaname",
                        "value" => $model_info->areaname,
                        "class" => "form-control",
                        "placeholder" => app_lang('areaname'),
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
        $("#area-form").appForm({
            onSuccess: function (result) {
                $("#area-table").appTable({newData: result.data, dataId: result.id});
            }
        });
        setTimeout(function () {
            $("#areaname").focus();
        }, 200);
    });
</script>