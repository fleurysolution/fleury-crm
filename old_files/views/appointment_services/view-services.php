<div class="modal-body clearfix general-form">
    <div class="container-fluid">


        <div class="clearfix">
            <div class="col-md-12">
                <strong class="font-20"><?php echo $model_info->name; ?></strong>  
                <p> Duration minutes: <?php echo $model_info->duration_minutes; ?> </p>  
                <p> Price: <?php echo $model_info->price; ?> </p> 
                <p> Required Payment: <?php echo $model_info->requires_payment; ?> </p> 
                <p> Description: <?php echo $model_info->description; ?> </p> 
                <p> Active: <?php echo ((int)($model_info->is_active ?? 1) === 1) ? "Yes" : "No"; ?> </p>
                <p> Allow Free Booking: <?php echo ((int)($model_info->allow_free_booking ?? 0) === 1) ? "Yes" : "No"; ?> </p>
                <p> Requires Payment: <?php echo ((int)($model_info->requires_payment ?? 1) === 1) ? "Yes" : "No"; ?> </p>
                <p> Assignment Mode: <?php echo $model_info->assignment_mode ? $model_info->assignment_mode : "Inherit"; ?> </p>

                <?php
                if(!empty($model_info->files)){
                    $data = $model_info->files;
                    $images = unserialize($data);
                    if(!empty($images)){
                    $image_file = $images[0]['file_name'];
                    $image_url = base_url().'/files/timeline_files/' . $image_file;
                    echo '<p> Files: <img src="'.$image_url.'" width="300px"> </p>';
                    }else{
                        echo '<p> Files: No Image Uploaded </p>';
                    }

                    ?>
                 
                <?php } ?>
              </div>
        </div>

        

    </div>
</div>

<div class="modal-footer">
   

    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>