<div class="card border-top-0 rounded-top-0">
    <div class="tab-title clearfix">
        <h4><?php echo app_lang('expenses'); ?></h4>
        <div class="title-button-group">
            <?php echo modal_anchor(get_uri("team_members/add_w2_form/<?php echo $user_id; ?>"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_expense'), array("class" => "btn btn-default mb0", "title" => app_lang('add_w2_form'), "data-post-user_id" => $user_id)); ?>
        </div>
    </div>
    <div class="table-responsive">
        <table id="w2form-table" class="display" cellspacing="0" width="100%">
        </table>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $EXPENSE_TABLE = $("#w2form-table");

        $EXPENSE_TABLE.appTable({
            source: '<?php echo_uri("team_members/w2_list_data/") ?>',
            filterParams: {user_id: "<?php echo $user_id; ?>"},
            order: [[0, "asc"]],
            columns: [
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("date") ?>', "class": "all", "iDataSort": 0},
                {title: '<?php echo app_lang("name") ?>'},
                {title: '<?php echo app_lang("title") ?>'},
                {title: '<?php echo app_lang("description") ?>'},
                {title: '<?php echo app_lang("file") ?>'},
                {title: '<?php echo app_lang("amount") ?>', "class": "text-right"},
                {title: '<?php echo app_lang("tax") ?>', "class": "text-right"},
                {title: '<?php echo app_lang("second_tax") ?>', "class": "text-right"},
                {title: '<?php echo app_lang("total") ?>', "class": "text-right all"}
<?php echo $custom_field_headers; ?>,
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: combineCustomFieldsColumns([1, 2, 3, 4, 6, 7, 8, 9], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 2, 3, 4, 6, 7, 8, 9], '<?php echo $custom_field_headers; ?>'),
            summation: [{column: 6, dataType: 'currency'}, {column: 7, dataType: 'currency'}, {column: 8, dataType: 'currency'}, {column: 9, dataType: 'currency'}]
        });
    });
</script>