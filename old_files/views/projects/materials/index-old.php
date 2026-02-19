<div class="card">
    <div class="tab-title clearfix">
        <h4><?php echo app_lang('area_list');  ?></h4>
        <div class="title-button-group">
            <?php 
            //print_r($project_info); die;
            if ($can_edit_invoices) {
                echo modal_anchor(get_uri("projects/area_modal_form/".$project_id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_area'), array("class" => "btn btn-default", "title" => app_lang('add_area'), "data-post-project_id" => $project_id));

            }
            ?>
        </div>
    </div>

    <div class="table-responsive">
        <table id="area-table" class="display" width="100%">       
        </table>
    </div>
    
       

</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#area-table").appTable({
            source: '<?php echo_uri("projects/area_list_data/$project_id") ?>',
            columns: [
                {title: '<?php echo app_lang("title") ?>'},
                {title: '<?php echo app_lang("description") ?>'},
                {title: '<?php echo app_lang("material_categories") ?>'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });
    });
</script>