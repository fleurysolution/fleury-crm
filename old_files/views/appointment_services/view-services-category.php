<div class="modal-body clearfix general-form">
    <div class="container-fluid">
        <div class="clearfix">
            <div class="col-md-12">
                <strong class="font-20"><?php echo esc($model_info->name); ?></strong>

                <p>Display Priority: <?php echo esc($model_info->display_order); ?></p>

                <?php if (isset($model_info->is_active)) { ?>
                    <p>Active: <?php echo ((int) $model_info->is_active === 1) ? "Yes" : "No"; ?></p>
                <?php } ?>

                <?php if (isset($model_info->default_allow_free)) { ?>
                    <p>Default Allow Free: <?php echo ((int) $model_info->default_allow_free === 1) ? "Yes" : "No"; ?></p>
                <?php } ?>

                <?php if (isset($model_info->default_assignment_mode)) { ?>
                    <p>Default Assignment: <?php echo esc($model_info->default_assignment_mode); ?></p>
                <?php } ?>

                <?php
                // Safe file handling
                $image_url = null;

                if (!empty($model_info->files)) {
                    $images = @unserialize($model_info->files);

                    if (is_array($images) && !empty($images)) {
                        $first = $images[0] ?? null;
                        if (is_array($first) && !empty($first['file_name'])) {
                            $image_file = $first['file_name'];
                            $image_url = base_url() . '/files/timeline_files/' . $image_file;
                        }
                    }
                }
                ?>

                <?php if ($image_url) { ?>
                    <p>Files: <img src="<?php echo esc($image_url); ?>" width="300px"></p>
                <?php } else { ?>
                    <p>Files: No Image Uploaded</p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal">
        <span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?>
    </button>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
