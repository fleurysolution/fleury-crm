
<div class="card rounded-bottom">
    <div class="tab-title clearfix">
        <h4><?php echo app_lang('availability'); ?></h4>
        <div class="title-button-group">
            <?php echo modal_anchor(get_uri("notes/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_availability'), array("class" => "btn btn-default", "title" => app_lang('add_availability'), "data-post-user_id" => $user_id)); ?>           
        </div>
    </div>
    <div class="table-responsive">
        <table id="availability-table" class="display" cellspacing="0" width="100%">            
        </table>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        $("#availability-table").appTable({
            source: '<?php echo_uri("team_members/availability_list_data/" . $user_id) ?>',
            order: [[0, 'desc']],
            columns: [
                {targets: [1], visible: false},
                {title: '<?php echo app_lang("weekday"); ?>'},
                {title: '<?php echo app_lang("start_time"); ?>'},
                {title: '<?php echo app_lang("end_time"); ?>'},
                {visible: false, searchable: false},
               // {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });
    });
</script>