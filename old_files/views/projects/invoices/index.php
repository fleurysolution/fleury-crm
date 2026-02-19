<div class="card">
    <div class="tab-title clearfix">
        <h4><?php echo app_lang('invoices');  ?></h4>
        <div class="title-button-group">
            <?php
            if ($can_edit_invoices) {
                echo modal_anchor(get_uri("invoices/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_invoice'), array("class" => "btn btn-default", "title" => app_lang('add_invoice'), "data-post-project_id" => $project_id));
            }
            ?>
        </div>
    </div>

    <div class="table-responsive">
        <table id="invoice-table" class="display" width="100%">       
        </table>
    </div>
    <div class="tab-title clearfix">
        <div class="page-title clearfix">
                <h1><?php echo app_lang('cancelled_invoices'); ?></h1>
        </div>
    </div>
        <div class="table-responsive">
            <table id="cancelled-invoice-table" class="display" width="100%">
            </table>
        </div>

</div>

<?php  if(empty($project_info->currency_symbol)){ echo $project_info->currency_symbol='$'; } ?>
<script type="text/javascript">
    $(document).ready(function () {
         let previousBalance = 0;
         let totalBalance = 0; 
        let currencySymbol = "<?php echo $project_info->currency_symbol; ?>";
        let projectPrice=<?php echo $project_info->price; ?>;
        $("#invoice-table").appTable({
            source: '<?php echo_uri("invoices/active_invoice_list_data_of_project/" . $project_id . "/" . $project_info->client_id) ?>',
            order: [[0, "desc"]],
            filterDropdown: [{name: "status", class: "w150", options: <?php echo view("invoices/invoice_statuses_dropdown"); ?>}, <?php echo $custom_field_filters; ?>],
            columns: [
            { visible: false, searchable: false },
            { title: "<?php echo app_lang("invoice_id") ?>", "class": "w10p all", "iDataSort": 0 },
            { targets: [2], visible: false, searchable: false },
            { targets: [3], visible: false, searchable: false },
            { visible: false, searchable: false },
            { title: "<?php echo app_lang("bill_date") ?>", "class": "w10p all", "iDataSort": 4 },
            { visible: false, searchable: false },
            { title: "<?php echo app_lang("due_date") ?>", "class": "w10p", "iDataSort": 6 },
            { title: "<?php echo app_lang("project_price") ?>", "class": "w10p", "iDataSort": 6 },
            { title: "<?php echo app_lang("total_invoiced") ?>", "class": "w10p text-right" },
            { title: "<?php echo app_lang("payment_received") ?>", "class": "w10p text-right" },
            { title: "<?php echo app_lang("due") ?>", "class": "w10p text-right" },
            {
                title: "<?php echo app_lang("balance_amount") ?>",
                class: "w10p text-right",
                render: function(data, type, row, meta) {
                    var projectValue = parseFloat(row[8].replace(/[$,]/g, '')) || 0;
                    var totalInvoiced = parseFloat(row[9].replace(/[$,]/g, '')) || 0;
                    var paymentReceived = parseFloat(row[10].replace(/[$,]/g, '')) || 0;

                    if (meta.row === 0) {
                        previousBalance = projectValue - paymentReceived;
                    } else {
                        previousBalance -= paymentReceived;
                    }

                    totalBalance += previousBalance; // Accumulate balance for total summation
                    return currencySymbol + previousBalance.toFixed(2);
                }
            },
             { title: "<?php echo app_lang("status") ?>", "class": "w10p text-center" },
            { title: "<?php echo app_lang("actions") ?>", "class": "w10p text-center" }
            <?php echo $custom_field_headers; ?>
        ],
        printColumns: combineCustomFieldsColumns([1, 5, 7, 8, 9, 10, 11], '<?php echo $custom_field_headers; ?>'),
        xlsColumns: combineCustomFieldsColumns([1, 5, 7, 8, 9, 10, 11], '<?php echo $custom_field_headers; ?>'),
        summation: [
            { column: 9, dataType: 'currency', currencySymbol: currencySymbol },
            { column: 10, dataType: 'currency', currencySymbol: currencySymbol },
            { column: 11, dataType: 'currency', currencySymbol: currencySymbol },
        ],
        footerCallback: function(row, data, start, end, display) {
            var api = $('#invoice-table').DataTable(); // Access DataTables API directly
            $(api.column(7).footer()).html('Total: ');
            $(api.column(12).footer()).html(currencySymbol + previousBalance.toFixed(2));

            // Display the hardcoded project price in the footer
            $(api.column(8).footer()).html(currencySymbol + projectPrice.toFixed(2));
        },
        createdRow: function(row, data, dataIndex) {
        var dueAmount = parseFloat(data[10].replace(/[$,]/g, '')); // Assuming column 10 is the due amount
        if (dueAmount === 0) {
            $(row).css('background-color', 'green'); // Color rows green if due amount is 0
        } else {
            $(row).css('background-color', 'blue'); // Color other rows blue
        }
    },
        });


        // Cancelled invoice table 

        $("#cancelled-invoice-table").appTable({
            source: '<?php echo_uri("invoices/cancelled_invoice_list_data_of_project/" . $project_id . "/" . $project_info->client_id) ?>',
            order: [[0, "desc"]],
            filterDropdown: [{name: "status", class: "w150", options: <?php echo view("invoices/invoice_statuses_dropdown"); ?>}, <?php echo $custom_field_filters; ?>],
            columns: [
                {visible: false, searchable: false},
                {title: "<?php echo app_lang("invoice_id") ?>", "class": "w10p all", "iDataSort": 0},
                {targets: [2], visible: false, searchable: false},
                {targets: [3], visible: false, searchable: false},
                {visible: false, searchable: false},
                {title: "<?php echo app_lang("bill_date") ?>", "class": "w10p all", "iDataSort": 4},
                {visible: false, searchable: false},
                {title: "<?php echo app_lang("due_date") ?>", "class": "w10p", "iDataSort": 6},
                {title: "<?php echo app_lang("project_price") ?>", "class": "w10p", "iDataSort": 6},
                {title: "<?php echo app_lang("total_invoiced") ?>", "class": "w10p text-right"},
                {title: "<?php echo app_lang("payment_received") ?>", "class": "w10p text-right"},
                {title: "<?php echo app_lang("due") ?>", "class": "w10p text-right"},
                {title: "<?php echo app_lang("status") ?>", "class": "w10p text-center"}
<?php echo $custom_field_headers; ?>
            ],
            printColumns: combineCustomFieldsColumns([1, 5, 7, 8, 9, 10, 11], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 5, 7, 8, 9, 10, 11], '<?php echo $custom_field_headers; ?>'),
            /*summation: [
                 {column: 8, dataType: 'currency', currencySymbol: currencySymbol},
                {column: 9, dataType: 'currency', currencySymbol: currencySymbol},
                {column: 10, dataType: 'currency', currencySymbol: currencySymbol},
                {column: 11, dataType: 'currency', currencySymbol: currencySymbol},
            ]*/
        });
    });
</script>