<div id="file-manager-window-area" class="show-context-menu">
<form action="<?php echo base_url('file_manager/sharetoteam');?>" method="post">

    

    <ul class="files-and-folders-list" data-has_write_permission="<?php echo $has_write_permission; ?>" data-has_upload_permission="<?php echo $has_upload_permission; ?>">
        <?php
        foreach ($folders_list as $folder) {
            $is_favourite = strpos($folder->starred_by, ":" . $login_user->id . ":") ? 1 : '';
            $has_this_folder_write_permission = false;

            if ($login_user->is_admin || ($folder->context == "file_manager" && $folder->actual_permission_rank >= 6) || ($folder->context != "file_manager" && $login_user->user_type == "staff")) {
                $has_this_folder_write_permission = true;
            }
        ?>
            <li class="folder-item" data-id="<?php echo $folder->id; ?>" data-folder_id="<?php echo $folder->folder_id; ?>" data-type='folder' data-is_favourite="<?php echo $is_favourite; ?>" data-has_this_folder_write_permission="<?php echo $has_this_folder_write_permission; ?>">
                <div class='folder-item-content show-context-menu folder-thumb-area'>
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3 icon-wrapper">
                            <i data-feather='folder' class='icon-40 bold-folder-icon'></i>
                        </div>
                        <div class="w-100">
                            <div class="folder-name item-name"><?php echo $folder->title; ?></div>
                            <small class="text-off folder-info">
                                <?php
                                if ($folder->subfolder_count) {
                                    echo $folder->subfolder_count . " ";

                                    if ($folder->subfolder_count > 1) {
                                        echo app_lang("folders");
                                    } else {
                                        echo app_lang("folder");
                                    }
                                }

                                if ($folder->subfile_count) {

                                    if ($folder->subfolder_count) {
                                        echo ", ";
                                    }

                                    echo $folder->subfile_count . " ";

                                    if ($folder->subfile_count > 1) {
                                        echo app_lang("files");
                                    } else {
                                        echo app_lang("file");
                                    }
                                }

                                if (!$folder->subfolder_count && !$folder->subfile_count) {
                                    echo app_lang('empty');
                                }
                                ?>
                            </small>
                        </div>
                    </div>
                </div>
                <span class="file-manager-more-menu">
                    <i data-feather='more-horizontal' class='icon-18'></i>
                </span>
            </li>
            <?php
        }

        foreach ($folder_items as $folder_item) {
            if ($folder_item_type == "file") {
                $file_name = short_file_name(remove_file_prefix($folder_item->file_name));
                $file_size = convert_file_size($folder_item->file_size);

                $preview_link_attr = $file_preview_link_attributes;

                $data_url = $file_preview_url . "/" .$project_id."/" . $folder_item->id;
                if ($client_id) {
                    $data_url .= "/" . $client_id;
                }

                $preview_link_attr["data-url"] = $data_url;

                $preview_link_attr["data-preview_function"] = "showFilePreviewAppModal";
                $preview_link_attr["data-group"] = "window_files";

                  $file_icon = get_file_icon(strtolower(pathinfo($folder_item->file_name, PATHINFO_EXTENSION)));
                $image_url = get_avatar($folder_item->uploaded_by_user_image);
                $extension = strtolower(pathinfo($folder_item->file_name, PATHINFO_EXTENSION));
                $image_types = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
                $file_path = get_uri("files/project_files/" . $folder_item->project_id . "/" . $folder_item->file_name);
                if (in_array($extension, $image_types)) {
                    $file_display = "<a href='javascript:void(0);' class='open-image-modal'  data-image-url='{$file_path}'><img src='{$file_path}'  alt='...' style='height:60px; object-fit:cover;'></a>";

                    /*"<a href='javascript:void(0);' class='open-image-modalList' data-image-url='{$file_path}'>
                            <img src='{$file_path}' style='height:60px; width:auto; object-fit:cover; border-radius:6px;'>
                        </a><br>";*/
                } else {
                    // Otherwise, show only the file icon
                    $file_display = "<div data-feather='{$file_icon}' class='mr10 float-start'></div>";
                }

            ?>
                <li class="folder-item" data-id="<?php echo $folder_item->id; ?>" data-type='file'>
                    <div class='folder-item-content show-context-menu file-thumb-area'>
                        <div class="d-flex">
                            <input type="hidden" name="project_id[]" value="<?php echo $project_id; ?>">
                            <input type="hidden" name="client_id[]" value="<?php echo $client_id; ?>">
                            <input type="checkbox" name="checkbox_file[]" value="<?php echo $folder_item->id; ?>">
                            <div class="flex-shrink-0 me-3 icon-wrapper">
                                <i data-feather='file' class='icon-40 bold-file-icon'></i>
                            </div>
                            <div class="w-100">
                                 <div class="text-break"> <?php echo $file_display; ?><!--  <a href='javascript:void(0);' class='open-image-modal'  data-image-url='<?php echo get_uri("files/project_files/".$project_id.'/' . $folder_item->file_name); ?>'><img src='<?php echo get_uri("files/project_files/".$project_id.'/' . $folder_item->file_name); ?>'  alt='...' style='height:60px; object-fit:cover;'></a> --></div>
                                <div class="text-break"> <?php echo js_anchor($folder_item->file_name, $preview_link_attr); ?></div>
                                <small class="text-off file-size"><?php echo $file_size; ?></small>
                            </div>
                        </div>
                    </div>
                    <span class="file-manager-more-menu">
                        <i data-feather='more-horizontal' class='icon-18'></i>
                    </span>
                </li>
        <?php
            }
        }
        ?>
    </ul>
 <?php if ($folder_item_type == "file") { ?>
    <input type="submit" name="submit" value="Share Now" class="btn btn-primary">
     <input type="submit" name="print_now" value="Print Now" class="btn btn-primary">
<?php } ?>
</form>
</div>


<!-- Modal for Image Preview -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content bg-transparent border-0 shadow-none">
      <div class="modal-body text-center position-relative p-0">
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"></button>
        <img id="previewImage" src="" alt="File Preview" class="img-fluid rounded" style="max-height:90vh;">
      </div>
    </div>
  </div>
</div>

<script>
$(document).on("click", ".open-image-modal", function() {
    var imageUrl = $(this).data("image-url");
    $("#previewImage").attr("src", imageUrl);
    $("#imagePreviewModal").modal("show");
});
</script>