<div class="card">
    <div class="tab-title clearfix">
        <h4><?php echo app_lang('area_list');  ?></h4>
        <div class="title-button-group">
            <?php
            if ($can_edit_invoices) {
                echo modal_anchor(get_uri("projects/category_modal_form/".$area_id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_category'), array("class" => "btn btn-default", "title" => app_lang('add_categories'), "data-post-project_id" => $project_id, "data-post-area_id" => $area_id));

            }
            ?>
        </div>
    </div>

    <div class="table-responsive">
        <table id="categories-table" class="display" width="100%">       
        </table>
    </div>
    
       

</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#categories-table").appTable({
            source: '<?php echo_uri("projects/categories_list_data/$area_id") ?>',
            request: {
                    extra_data: {area_id: <?php echo $area_id; ?>}
                    },
            columns: [
                {title: '<?php echo app_lang("title") ?>'},
                {title: '<?php echo app_lang("description") ?>'},
                {title: '<?php echo app_lang("materials") ?>'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });
    });
</script>