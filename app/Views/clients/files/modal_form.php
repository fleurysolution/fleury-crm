<?php echo form_open_multipart(get_uri("clients/save_file"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="client_id" value="<?php echo $client_id ?? ''; ?>" />
        
        <div class="form-group mb-3">
            <label for="files" class="col-md-3">Upload Files</label>
            <div class="col-md-9">
                <input type="file" name="files[]" id="files" class="form-control" multiple required>
            </div>
        </div>

        <div class="form-group mb-3">
             <label for="description" class="col-md-3">Description</label>
             <div class="col-md-9">
                 <textarea name="description" id="description" class="form-control" placeholder="Optional description"></textarea>
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
    $(document).ready(function() {
        $("#file-form").appForm({
            onSuccess: function(result) {
                appAlert.success(result.message, {duration: 10000});
                // Reload the files tab content
                // Assuming we are in the clients view context
                 if (typeof loadTabContent === 'function') {
                    // Try to reload just the tab
                    // For now, simpler to reload page or replace table content if we had reference
                    location.reload(); 
                 } else {
                     location.reload();
                 }
            }
        });
    });
</script>
