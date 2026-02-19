<?php echo form_open(get_uri("file_manager/save_excel_file_add_modal_form"), array("id" => "xls_file_form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="folder_id" value="<?php echo $folder_id; ?>" />
        <input type="hidden" name="context" value="<?php echo $context; ?>" />
        <input type="hidden" name="context_id" value="<?php echo $context_id; ?>" />
        <?php //echo view("includes/multi_file_uploader"); ?>
        <div class="form-group">
    <div class="col-sm-12">
        
        <div id="file-upload-dropzone-scrollbar" class="ps" style="height: 280px; position: relative;">
            <div id="uploaded-file-previews">
                
            <div id="" class="box dz-complete">
                    <div class="preview box-content pr15" style="width:100px;">
                        <img data-dz-thumbnail="" class="upload-thumbnail-sm">
                        <div class="progress upload-progress-sm active mt5" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                            <div class="progress-bar progress-bar-success" style="width: 100%;" data-dz-uploadprogress=""></div>
                        </div>
                    </div>
                    <div class="box-content">
                        <p class="name" data-dz-name=""><?php echo date('ymd-his').'-client.xlsx'; ?></p>
                        <p class="clearfix">
                            <span class="size float-start" data-dz-size=""><strong>8688</strong> B</span>
                            <span data-dz-remove="" class="btn btn-default btn-sm border-circle float-end mr10 fs-14 margin-top-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x icon-16"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </span>
                        </p>
                        <strong class="error text-danger" data-dz-errormessage=""></strong>
                        <input class="file-count-field" type="hidden" name="files[]" value="1">

                        <input type="text" value="" class="form-control description-field" placeholder="Description" data-rule-required="false" data-msg-required="This field is required." name="description_1">
                    </div>
                <input type="hidden" name="file_name_1" value="<?php echo date('ymd-his').'-client.xls'; ?>">
                <input type="hidden" name="file_size_1" value="8688"></div></div>
        </div>
    </div>
</div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default cancel-upload" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#xls_file_form").appForm({
            onSuccess: function(result) {
                location.reload();
            }
        });

    });
</script>