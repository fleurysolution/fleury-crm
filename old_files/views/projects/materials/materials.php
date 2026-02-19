<div class="card">
    <div class="tab-title clearfix">
              <h4><?php echo app_lang('area_list');  ?></h4>
            <div class="title-button-group">
			<?php if ($can_edit_invoices) {
                echo modal_anchor(get_uri("projects/area_modal_form/".$project_id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_area'), array("class" => "btn btn-default", "title" => app_lang('add_area'), "data-post-project_id" => $project_id));
				echo modal_anchor(get_uri("projects/category_modal_form/".$project_id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_category'), array("class" => "btn btn-default", "title" => app_lang('add_categories')));
				
				echo modal_anchor(get_uri("projects/materials_modal_form/".$project_id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_material'), array("class" => "btn btn-default", "title" => app_lang('add_item'))); 
				
            } ?>
		
                
            </div>
        </div>
        
         <div class="table-responsive">
            <div class="row"> 
            <div class="col-sm-4">
                <div class="form-group">
                <label><?php echo app_lang('area'); ?> </label> 
                <select name="area_name" class='select2 validate-hidden  form-control' id='area_id' data-rule-required='true', data-msg-required='<?php app_lang('field_required');?>' required>
				<option value=""> Select Area</option>
                    <?php foreach($area_data as $areaData){ ?> 
                        <option value="<?php echo $areaData->id; ?>"> <?php echo $areaData->areaname; ?></option>
                    <?php } ?>
                </select>            
            </div> 
        </div> 
            <div class="col-sm-4">
			<div class="form-group"><label><?php echo app_lang('category'); ?> </label> 
                <select name="category" class='select2 validate-hidden form-control' id='category_id' data-rule-required='true', data-msg-required='<?php app_lang('field_required');?>' required>
				<option value=""> Select Category</option>
                <?php foreach($category_data as $categoryData){ ?> 
                    <option value="<?php echo $categoryData->id; ?>"> <?php echo $categoryData->title; ?></option>
                <?php } ?>
            </select> 

        </div> 
		</div>  
        </div>
            <table id="item-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
<!-- </div> -->

<script type="text/javascript">
    $(document).ready(function () {
    var project_id = '<?php echo $project_id ?>';

    // Initialize select2 dropdowns
    $("#area_id .select2").select2();
    $("#category_id .select2").select2();

    // Function to update the item table with selected area and category filters
    function updateTableData() {
        var area_id = $('#area_id').val();
        var category_id = $('#category_id').val();

        // Destroy the existing DataTable before reinitializing it
        if ($.fn.DataTable.isDataTable('#item-table')) {
            $('#item-table').DataTable().destroy();
        }

        // Reinitialize the table with new data
        $("#item-table").appTable({
            source: '<?php echo_uri("projects/material_list_data/") ?>' + project_id + '?area_id=' + area_id + '&category_id=' + category_id, // Pass area_id and category_id in the URL
            order: [[0, 'desc']],
            columns: [
                {title: "<?php echo app_lang('title') ?> ", "class": "w20p all"},
                {title: "<?php echo app_lang('area') ?>", "class": "text-right w100"},
                {title: "<?php echo app_lang('category') ?>"},
                {title: "<?php echo app_lang('unit_type') ?>", "class": "w100"},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3, 4],
            xlsColumns: [0, 1, 2, 3, 4]
        });
    }

    // Initial table load with default values
    updateTableData();

    // When the area or category selection changes, update the table data
    $('#area_id, #category_id').change(function() {
        updateTableData(); // Update the table with selected filters
    });

    // When the area selection changes
    $('#area_id').change(function() {
        var area_id = $(this).val(); // Get the selected area ID

        if (area_id) {
            $.ajax({
                url: '<?php echo_uri("projects/getcategoryData/") ?>',
                type: 'post',
                data: { area_id: area_id },
                success: function(response) {
                    if (typeof response === 'string') {
                        try {
                            response = JSON.parse(response);
                        } catch (e) {
                            alert('Invalid JSON response');
                            return;
                        }
                    }

                    // Clear the category dropdown before adding new options
                    $('#category_id').empty().append('<option value="">Select Category</option>');

                    // Check if the response is an array and has data
                    if (response && Array.isArray(response) && response.length > 0) {
                        // Add new categories to the select dropdown
                        $.each(response, function(index, category) {
                            $('#category_id').append('<option value="' + category.id + '">' + category.title + '</option>');
                        });
                    } else {
                        // If no categories are available, show a message
                        $('#category_id').append('<option value="">No categories available</option>');
                    }
                },
                error: function() {
                    alert('Failed to fetch categories.');
                }
            });
        } else {
            // Clear the category dropdown if no area is selected
            $('#category_id').html('<option value="">Select Category</option>');
        }
    });
});


</script>



<script type="text/javascript">
    // $(document).ready(function () {
        // $("#item-table").appTable({
			// var area_id=1;
            // source: '<?php echo_uri("projects/material_list_data/") ?>' + area_id ',
            // order: [[0, 'desc']],  
           
            // columns: [
                // {title: "<?php echo app_lang('title') ?> ", "class": "w20p all"},
                // {title: "<?php echo app_lang('description') ?>"},
                // {title: "<?php echo app_lang('category') ?>"},
                // {title: "<?php echo app_lang('unit_type') ?>", "class": "w100"},
                // {title: "<?php echo app_lang('area') ?>", "class": "text-right w100"},
                // {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
            // ],
            // printColumns: [0, 1, 2, 3, 4],
            // xlsColumns: [0, 1, 2, 3, 4]
        // });
        // $("#area_id .select2").select2();
         // $("#category_id .select2").select2();
    // });
</script>