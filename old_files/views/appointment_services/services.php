<div id="page-content" class="page-wrapper clearfix grid-button">
    <div class="card">
        <div class="page-title clearfix materials-page-title">
            <h1><?php echo app_lang('services_manager'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(
                    get_uri("appointment_services/modal_form_services"),
                    "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_services'),
                    array("class" => "btn btn-default", "title" => app_lang('add_services'))
                ); ?>

                <a href="<?php echo get_uri("appointment_services/appointment_categories"); ?>" class="btn btn-default">Categories Manager</a>
                <a href="<?php echo get_uri("appointment_services"); ?>" class="btn btn-default">Appointment Manager</a>
            </div>
        </div>

        <?php if (!$services_id) { $services_id = ''; } ?>

        <div class="table-responsive">
            <table id="services-table" class="display" cellspacing="0" width="100%"></table>
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

    const paymentOptions = <?php echo json_encode([
        ["id" => "", "text" => "- Payment -"],
        ["id" => "required", "text" => "Payment Required"],
        ["id" => "free_allowed", "text" => "Free Allowed"],
        ["id" => "free_only", "text" => "Free Only (price=0)"],
    ]); ?>;

    const assignmentOptions = <?php echo json_encode([
        ["id" => "", "text" => "- Assignment -"],
        ["id" => "round_robin", "text" => "Round-robin"],
        ["id" => "manual", "text" => "Manual"],
        ["id" => "inherit", "text" => "Inherit / Default"],
    ]); ?>;

    $("#services-table").appTable({
        source: '<?php echo_uri("appointment_services/services_list_data/".$services_id) ?>',
        order: [[0, 'desc']],
        filterDropdown: [
            {name: "category_id", class: "w200", options: <?php echo $categories_dropdown; ?>},
            {name: "is_active", class: "w150", options: statusOptions},
            {name: "payment_policy", class: "w200", options: paymentOptions},
            {name: "assignment_mode", class: "w200", options: assignmentOptions}
        ],
        columns: [
            {title: "<?php echo app_lang('id') ?>", class: "w60 all"},
            {title: "<?php echo app_lang('name') ?>", class: "w200"},
            {title: "<?php echo app_lang('categories') ?>", class: "w150"},
            {title: "<?php echo app_lang('duration_minutes') ?>", class: "w120"},
            {title: "<?php echo app_lang('price') ?>", class: "w100"},
            {title: "Active", class: "w90 text-center"},
            {title: "Payment Policy", class: "w160"},
            {title: "Assignment", class: "w140"},
            {title: "<i data-feather='menu' class='icon-16'></i>", class: "text-center option w100"}
        ],
        printColumns: [0, 1, 2, 3, 4, 5, 6, 7],
        xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7]
    });
});
</script>
