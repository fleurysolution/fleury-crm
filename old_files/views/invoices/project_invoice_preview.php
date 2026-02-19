<div id="page-content" class="page-wrapper clearfix">
    <?php
    load_css(array(
        "assets/css/invoice.css",
    ));
    $invoice_total_summary->balance_due=0;
    ?>

    <div class="invoice-preview">
        <?php if ($login_user->user_type === "client" && $invoice_total_summary->balance_due >= 1 && count($payment_methods) && !$client_info->disable_online_payment && $invoice_info->status !== "credited" && $invoice_info->status !== "cancelled") { ?>
            <div class="card d-block p15 no-border clearfix invoice-payment-button pb-0">
                <div class="inline-block strong float-start pt5 pr15">
                    <?php echo app_lang("pay_invoice"); ?>:
                </div>
                <div class="mr15 strong float-start general-form" style="width: 145px;" >
                    <?php if (get_setting("allow_partial_invoice_payment_from_clients")) { ?>
                        <span class="invoice-payment-amount-section" style="background-color: #f6f8f9; display: inline-block; padding: 8px 2px 7px 10px;"><?php echo $invoice_total_summary->currency; ?></span><input type="text" id="payment-amount" value="<?php echo to_decimal_format($invoice_total_summary->balance_due); ?>" class="form-control inline-block fw-bold" style="padding-left: 3px; width: 100px"  oninput="checkPaymentAmount(this.value)"/>
                        <span id="error-message" style="color: red; display: none;">Amount exceeds due balance</span>

                    <?php } else { ?>
                        <span class="pt5 inline-block">
                            <?php echo to_currency($invoice_total_summary->price, $invoice_total_summary->currency . " "); ?>
                        </span>
                    <?php } ?>
                </div>

                <?php
                
                ?>
                
                <div class="float-end">
                    <?php
                    echo "<div class='text-center'>" . anchor("invoices/download_pdf/" . $invoice_info->id, app_lang("download_pdf"), array("class" => "btn btn-default round")) . "</div>"
                    ?>
                </div>

                <!-- <div class="float-end">
                            <?php //if ($invoice_status !== "cancelled" && $invoice_info->status !== "credited") { ?>
                                <?php //echo modal_anchor(get_uri("invoice_payments/payment_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_payment'), array("class" => "btn btn-default round", "title" => app_lang('add_payment'), "data-post-invoice_id" => $invoice_info->id)); ?>
                            <?php //} ?>
                        </div>
            </div> -->
            <?php
        } else if ($login_user->user_type === "client") {
            echo "<div class='text-center'>" . anchor("invoices/project_download_pdf/" . $invoice_info->id, app_lang("download_pdf"), array("class" => "btn btn-default round")) . "</div>";
        }

        $show_close_preview='0';
        if ($show_close_preview) {
            echo "<div class='text-center'>" . anchor("invoices/view/" . $invoice_info->id, app_lang("close_preview"), array("class" => "btn btn-default round")) . "</div>";
        }
        ?>

        <div id="invoice-preview" class="invoice-preview-container bg-white mt15">
            <?php //if ($invoice_info->type == "invoice") { ?>
                <div class="row">
                    <div class="col-md-12 position-relative">
                        <div class="ribbon"><?php echo $invoice_status_label; ?></div>
                    </div>
                </div>
                <?php
          //  }
            echo $invoice_preview;
            ?>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#payment-amount").change(function () {
            var value = $(this).val();
            $(".payment-amount-field").each(function () {
                $(this).val(value);
            });
        });
    });



</script>
<script>
const balanceDue = <?php echo to_decimal_format($invoice_total_summary->balance_due); ?>;

function checkPaymentAmount(value) {
    const paymentAmount = parseFloat(value) || 0;
    
    if (paymentAmount > balanceDue) {        
        document.getElementById('payment-amount').value = balanceDue;        
        document.getElementById('error-message').style.display = 'inline';
    } else {
        document.getElementById('error-message').style.display = 'none';
    }
}
</script>