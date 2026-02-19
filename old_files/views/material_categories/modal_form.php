<?php echo form_open(get_uri("material_categories/save"), array("id" => "category-form", "class" => "general-form", "role" => "form")); ?>
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

            <div class="form-group">
              <label for="parentCategory"><?php echo app_lang('parent_category'); ?></label>
                <div class="col-md-9">
                   <?php
                    $dropdown[]='';
                   $dropdown[]= $categories_dropdown;

                        echo form_dropdown("parent_id", $dropdown, $model_info->parent_id, "class='select2 validate-hidden form-control' id='category_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        ?>
                </div>
            </div>

            <!-- 
            <div class="form-group">
              <label for="hierarchyLevel">Hierarchy Level</label>
                <div class="col-md-9">
                  <select class="form-control" id="hierarchyLevel" name="hierarchy_level">
                    <option value="1">1 - Main Category</option>
                    <option value="2">2 - Sub-Category</option>
                    <option value="3">3 - Child Category</option>
                  </select>
                </div>
            </div> -->
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