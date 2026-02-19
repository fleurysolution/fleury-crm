<div id="page-content" class="page-wrapper clearfix grid-button">
    <div class="card">
        <div class="page-title clearfix materials-page-title">
            <h1><?php echo app_lang('appointments_categories'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(
                    get_uri("appointment_services/modal_form_categories"),
                    "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_category'),
                    array("class" => "btn btn-default", "title" => app_lang('add_category'))
                ); ?>

                <a href="<?php echo get_uri("appointment_services/services"); ?>" class="btn btn-default">Services Manager</a>
                <a href="<?php echo get_uri("appointment_services/"); ?>" class="btn btn-default">Appointment Manager</a>
            </div>
        </div>

        <?php if (!$category_id) { $category_id=''; } ?>

        <div class="table-responsive">
            <table id="service-categories-table" class="display" cellspacing="0" width="100%"></table>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {

    const statusOptions = <?php echo json_encode([
        ["id" => "", "text" => "- Status -"],
        ["id" => "1", "text" => "Active"],
        ["id" => "0", "text" => "Inactive"],
    ]); ?>;

    const freeOptions = <?php echo json_encode([
        ["id" => "", "text" => "- Free Policy -"],
        ["id" => "1", "text" => "Free Allowed"],
        ["id" => "0", "text" => "Paid Default"],
    ]); ?>;

    const assignmentOptions = <?php echo json_encode([
        ["id" => "", "text" => "- Assignment -"],
        ["id" => "round_robin", "text" => "Round-robin"],
        ["id" => "manual", "text" => "Manual"],
    ]); ?>;

    $("#service-categories-table").appTable({
        source: '<?php echo_uri("appointment_services/categories_list_data/".$category_id) ?>',
        order: [[2, 'asc']],
        filterDropdown: [
            {name: "is_active", class: "w150", options: statusOptions},
            {name: "default_allow_free", class: "w200", options: freeOptions},
            {name: "default_assignment_mode", class: "w200", options: assignmentOptions}
        ],
        columns: [
            {title: "<?php echo app_lang('id') ?>", class: "w60 all"},
            {title: "<?php echo app_lang('name') ?>"},
            {title: "<?php echo app_lang('display_order') ?>", class: "w120"},
            {title: "Active", class: "w90 text-center"},
            {title: "Default Payment", class: "w140"},
            {title: "Default Assignment", class: "w160"},
            {title: "<i data-feather='menu' class='icon-16'></i>", class: "text-center option w100"}
        ],
        printColumns: [0, 1, 2, 3, 4, 5],
        xlsColumns: [0, 1, 2, 3, 4, 5]
    });
});
</script>
