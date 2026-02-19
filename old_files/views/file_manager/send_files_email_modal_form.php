<?php echo form_open(get_uri("file_manager/sharefilenow"), array("id" => "send_files_email", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="text" name="file_id" value="<?php echo $folder_id; ?>" />
       
        <div class="form-group">
    <div class="col-sm-12">
        
        <div id="file-upload-dropzone-scrollbar" class="ps" >
            <div id="uploaded-file-previews">
                
            <div id="" class="box dz-complete">
                    
                    <div class="box-content">
                       <div class="form-group">
                    <label> To </label>
                    <input type="text" name="emailsto" class="form-control" >
                </div>

                <div class="form-group">
                    <label> CC </label>
                    <input type="text" name="emailscc" class="form-control" >
                </div>

                <div class="form-group">
                    <label> BCC </label>
                    <input type="text" name="emailsbcc" class="form-control" >
                </div>

                <div class="form-group">
                    <label> Message </label>
                    <textarea class="form-control"> </textarea>
                </div>

                    </div>
                
                </div></div>
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
        $("#send_files_email").appForm({
            onSuccess: function(result) {
                location.reload();
            }
        });

    });
</script>