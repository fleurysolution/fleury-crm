<?php echo form_open(get_uri("estimates/save_scope"), array("id" => "estimate-form", "class" => "general-form", "role" => "form")); ?>
<div class="clearfix default-bg details-view-container">
    <div class="row">
        <div class="col-md-12 d-flex">

            <div class="card p15 w-100">
                <div id="page-content" class="clearfix">
                    <div style="max-width: 1000px; margin: auto;">
                        <div>
                            <div class="clearfix pl5 pr5 pb10 preview-editor-button-group">
                                    <?php echo modal_anchor(get_uri("estimate_templates/insert_template_modal_form"), "<i data-feather='rotate-ccw' class='icon-16'></i> " . app_lang('change_template'), array("class" => "btn btn-default float-start", "title" => app_lang('change_template'))); ?>
                                    <button type="button" class="btn btn-primary ml10 float-end" id="contract-save-and-show-btn"><span data-feather='check-circle' class="icon-16"></span> <?php echo app_lang('save_and_show'); ?></button>
                                    <button type="submit" class="btn btn-primary float-end"><span data-feather='check-circle' class="icon-16"></span> <?php echo app_lang('save'); ?></button>
                                </div>


                            <div class="clearfix p20">

                                <div class="form-group">
                                 <input type="hidden" name="estimate_id" value="<?php echo $estimate_info->id; ?>" />

                                 <?php
                                        echo form_input(array(
                                            "id" => "estimate_title",
                                            "name" => "estimate_title",
                                            "value" => isset($estimate_info->estimate_title) ? $estimate_info->estimate_title : "",
                                            "class" => "form-control",
                                            "placeholder" => "Title"
                                        ));
                                        ?>
                                    </div>
                                    <div class="form-group">
                               <?php
                                echo form_textarea(array(
                                    "id" => "estimate_scope",
                                    "name" => "estimate_scope",
                                    "value" => isset($estimate_info->scope) ? process_images_from_content($estimate_info->scope, false) : "",
                                    "class" => "form-control",    
                                    "data-toolbar" => "pdf_friendly_toolbar",
                                    "data-height" => 600,                               
                                    "data-rich-text-editor" => true,
                                    "data-keep-rich-text-editor-after-submit" => true,
                                    "data-encode_ajax_post_data" => "1"
                                ));
                                ?>
                            </div>

<div class="form-group">
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>
                            </div>

                            

                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#estimate-form").appForm({
            isModal: false,
            onSuccess: function (response) {
                appAlert.success(response.message, {duration: 10000});
            }
        });

         initWYSIWYGEditor("#estimate_scope");

        //insert contract template
        $("body").on("click", "#estimate-template-table tr", function () {
            var id = $(this).find(".estimate_template-row").attr("data-id");
            appLoader.show({container: "#insert-template-section", css: "left:0;"});

            $.ajax({
                url: "<?php echo get_uri('estimate_templates/get_template_data') ?>" + "/" + id,
                type: 'POST',
                dataType: 'json',
                success: function (result) {
                    if (result.success) {
                        
                        setWYSIWYGEditorHTML("#estimate_scope", result.template);
                        
                        //close the modal
                        $("#close-template-modal-btn").trigger("click");
                    } else {
                        appAlert.error(result.message);
                    }
                }
            });

        });

    });
    


    </script>