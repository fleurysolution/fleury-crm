<div id="page-content" class="page-wrapper clearfix grid-button">
    <div class="card">
        <div class="page-title clearfix materials-page-title">
            <h1> <?php echo app_lang('appointments'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("appointment_services/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_appointments'), array("class" => "btn btn-default", "title" => app_lang('add_item'))); ?>
                <a href="<?php echo get_uri("appointment_services/services"); ?>" class="btn btn-default"> Services Manager</a>
                <a href="<?php echo get_uri("appointment_services/appointment_categories"); ?>" class="btn btn-default"> Services Categories Manager</a> 
    
            </div>
        </div>
        <?php  if(!$category_id){
            $category_id='';
        } ?>
         <?php  if(!$service_id){
            $service_id='';
        } ?>
         <div class="table-responsive">
            <table id="item-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#item-table").appTable({
            source: '<?php echo_uri("appointment_services/list_data/".$service_id) ?>',
            order: [[0, 'desc']],
            /*filterDropdown: [
               // {name: "category_id", class: "w200", options: <?php echo $categories_dropdown; ?>},
                {name: "service_id", class: "w200", options: <?php echo $services_dropdown; ?>}
            ],*/
            columns: [

                {title: "<?php echo app_lang('id') ?>"},
                {title: "<?php echo app_lang('meeting_for') ?>"},
                {title: "<?php echo app_lang('name') ?> ", "class": "w20p all"},
                {title: "<?php echo app_lang('email') ?>"},
                {title: "<?php echo app_lang('phone') ?>"},
                {title: "<?php echo app_lang('start_time') ?>", "class": "w100"},
                {title: "<?php echo app_lang('end_time') ?>", "class": "w100"},
                {title: "<?php echo app_lang('status') ?>", "class": "text-right w100"},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3, 4],
            xlsColumns: [0, 1, 2, 3, 4]
        });
    });
</script>