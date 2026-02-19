<?php
if (isset($file_info) && $file_info) {

    $sidebar = 0;
    if (isset($show_file_preview_sidebar) && $show_file_preview_sidebar) {
        $sidebar = 1;
    }

    $file_name = $file_info->file_name;
    echo "<div class='file-manager-preview-section overflow-hidden'>";
    echo view("includes/file_preview");
    echo "</div>";
?>

    <div>
        <div class=" text-break strong"><?php echo js_anchor(remove_file_prefix($file_name), array('title' => "", "data-group" => "details", "data-toggle" => "app-modal", "data-sidebar" => $sidebar, "data-url" => $file_preview_url)); ?></div>
        <?php
        if (isset($file_info->file_size) && $file_info->file_size) {
            echo "<div class='text-off b-b pb10'>" . convert_file_size($file_info->file_size) . "</div>";
        }
        ?>
    </div>
    <div class="pt20 pb20">
        <h4><?php echo app_lang("file_details"); ?></h4>
        <div class="text-off"><?php echo app_lang("uploaded_by"); ?></div>
        <ul class="list-group access-list">
            <li class="list-group-item">
                <?php
                if (isset($file_info->uploaded_by_user_name) && isset($file_info->uploaded_by) && $file_info->uploaded_by_user_name) {
                    echo get_team_member_profile_link($file_info->uploaded_by, $file_info->uploaded_by_user_name);
                } ?>
            </li>
        </ul>

        <div class="text-off"><?php echo app_lang("uploaded_at"); ?></div>
        <ul class="list-group access-list">
            <li class="list-group-item">
                <?php
                if (isset($file_info->created_at) && $file_info->created_at) {
                    echo format_to_relative_time($file_info->created_at);
                } ?>
            </li>
           
        </ul>
    </div>

<?php } ?>


<div class="share-buttons">
         <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $file_url; ?>" class="share-button facebook" target="_blank">
            <i class="fab fa-facebook-f"></i>
         </a>
         
         <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $file_url; ?>&title=Your Digital Partner&summary=<?php echo $file_url.'<br> Download File: '.$file_url ;?> &source=Fleury Solutions" class="share-button linkedin" target="_blank">
            <i class="fab fa-linkedin-in"></i>
         </a>

         <a href="mailto:?subject=You have attachment to download &body=Hi,%0D%0A%0D%0A Please download the attachment from the referred link. %0D%0A%0D%0AClick the link given below: %0D%0A%0D%0A <?php echo $file_url ;?>  %0D%0A%0D%0A thank you" class="share-button pinterest">
            Email Me
         </a>
         <!--  <a href="javascript:void(0)" class="share-button pinterest" onclick="showfunction();">
            Email Me
         </a> -->
         <?php //echo '<a href="#" id="file-manager-send-files-button" class="btn btn-default" title="Send Email" data-post-context="" data-post-context_id="0" data-post-folder_id="'.$file_url.'" data-act="ajax-modal" data-title="Send Email" data-action-url="'.base_url().'file_manager/send_files_email_modal_form">Send email</a>'; ?>
         
         
         <a href="whatsapp://send?text=Hi, Please download the file from the given link <?php echo $file_url; ?>"  
 class="share-button whatsapp" target="_blank">
            <i class="fab fa-whatsapp"></i>
         </a>
      </div>
<div class="hidden" id="showme" style="display:none"> 
            <form> 
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
            </form>
         </div>

<script>
    function showfunction(){
        
            var x = document.getElementById("showme");
              if (x.style.display === "none") {
                x.style.display = "block";
              } else {
                x.style.display = "none";
              }
    }
</script>