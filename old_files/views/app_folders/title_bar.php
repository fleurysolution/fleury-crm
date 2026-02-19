<?php
$context = "file_manager";
$context_id = 0;

if (isset($view_from) && $view_from) {
    if ($view_from == "client_details_view" || $view_from == "client_view") {
        $context = "client";
        $context_id = $client_id ? $client_id : 0;
    } else if ($view_from == "project_view") {
        $context = "project";
        $context_id = $project_id ? $project_id : 0;
    }
}

?>
<h1><?php
    if ($folder_info) {
        echo js_anchor("<i data-feather='chevron-left'></i>", array('class' => "breadcrumb-folder-item p15 pl10 text-default", "data-folder_id" => $parent_folder_info ? $parent_folder_info->folder_id : ""));

        echo $folder_info->title;
    } else {
        echo "<span class='p15 mr5'><i data-feather='home' class='icon-18'></i></span>" . app_lang("root_folder");
    }
    ?>
</h1>

<div class="title-button-group">
    <?php
    if ($has_write_permission) {
        echo modal_anchor(get_uri($controller_slag . "/folder_modal_form"), "<i data-feather='folder-plus' class='icon-16 mr5'></i>" . app_lang('new_folder'), array("class" => "btn btn-default", "title" => app_lang('new_folder'), "id" => "new_folder_button", "data-post-parent_id" => $folder_info ? $folder_info->id : "", "data-post-context" => $context, "data-post-context_id" => $context_id));
    }

    if ($has_upload_permission && $folder_item_type == "file") {
        echo $add_files_button;
        if(!empty($folder_info)){
        $folderid= $folder_info->id;
        }else{
        $folderid='';
        }
        echo '<a href="#" id="file-manager-add-files-button" class="btn btn-default" title="Add files" data-post-context="" data-post-context_id="0" data-post-folder_id="'.$folderid.'" data-act="ajax-modal" data-title="Add files" data-action-url="'.base_url().'file_manager/word_file_add_modal_form">Create Word File</a>';
        echo '<a href="#" id="file-manager-add-files-button" class="btn btn-default" title="Add files" data-post-context="" data-post-context_id="0" data-post-folder_id="'.$folderid.'" data-act="ajax-modal" data-title="Add files" data-action-url="'.base_url().'file_manager/excel_file_add_modal_form">Create Excel File</a>';
    }

    echo js_anchor("<i data-feather='alert-circle' class='icon-16'></i>", array('title' => app_lang('view_details'), "id" => "view-details-button", "class" => "btn btn-default"));

    echo js_anchor("<i data-feather='share' class='icon-16'></i>", array('title' => app_lang('Share Now'), "id" => "share-Now-button", "class" => "btn btn-default"));
    ?>
</div>