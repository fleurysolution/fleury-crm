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
        <div class="row"> 
            <div class="col-sm-4"><div class="form-group">
                <label>Area </label> 
                <select name="area_name"  class='select2 validate-hidden' id='area_id' data-rule-required='true', data-msg-required='<?php app_lang('field_required');?>' required>
                <?php foreach($area_data as $areaData){ ?> 
                    <option value="<?php echo $areaData->id; ?>"> <?php echo $areaData->areaname; ?></option>
                <?php } ?>
            </select> </div> </div> 
            <div class="col-sm-4"><div class="form-group"><label>Category </label> 
                <select name="category_id" class='select2 validate-hidden' id='area_id' data-rule-required='true', data-msg-required='<?php app_lang('field_required');?>' required>
                <?php foreach($category_data as $categoryData){ ?> 
                    <option value="<?php echo $categoryData->id; ?>"> <?php echo $categoryData->title; ?></option>
                <?php } ?>
            </select> </div> </div>  
        </div>
        <table id="materials-table" class="display" width="100%">       
        </table>
    </div>
    
       

</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#materials-table").appTable({
            source: '<?php echo_uri("projects/area_list_data/$project_id") ?>',
            columns: [
                {title: '<?php echo app_lang("title") ?>'},
                {title: '<?php echo app_lang("description") ?>'},
                {title: '<?php echo app_lang("material_categories") ?>'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });

         $("#area_id .select2").select2();
         $("#category_id .select2").select2();
    });
</script>