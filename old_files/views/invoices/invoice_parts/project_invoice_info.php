<?php   if (get_setting("invoice_style") == "style_3") { ?>

<div style="font-size: 25px; margin-bottom: 10px;"><?php echo app_lang("invoice"); ?></div>

<div style="line-height: 5px;"></div>
<span class="invoice-meta text-default"><?php echo app_lang("invoice_number") . "invoice_number: Proj" . $invoice_info->id; ?></span><br />
<?php } else { ?>
<?php //if ($invoice_info->type == "credit_note") { ?>

<?php //} else { ?>
<span class="invoice-info-title" style="font-size:20px; font-weight: bold;background-color: <?php echo $color; ?>; color: #fff;">Invoice #&nbsp;Proj<?php echo $invoice_info->id; ?>&nbsp;</span><br />
<div style="line-height: 1px;"></div>
    <?php //} ?>
<?php } ?>
<span class="invoice-meta text-default"><?php
    

        echo app_lang("bill_date") . ": " . format_to_date($invoice_info->start_date, false);
        ?><br /><?php
        echo app_lang("due_date") . ": " . format_to_date($invoice_info->deadline, false);
  
    ?></span>

