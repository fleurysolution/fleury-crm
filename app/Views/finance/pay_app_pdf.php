<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AIA G702 & G703 Pay App</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; margin: 0; padding: 20px; color: #000; }
        
        /* Utility */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .b-bottom { border-bottom: 1px solid #000; }
        
        /* G702 Cover Sheet */
        .g702-header-table { width: 100%; margin-bottom: 20px; }
        .g702-header-table td { vertical-align: top; }
        .g702-title { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        
        .g702-meta { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .g702-meta td { border: 1px solid #000; padding: 5px; vertical-align: top; width: 25%; }
        
        .g702-body { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .g702-body td { padding: 4px; vertical-align: bottom; }
        .g702-line-num { width: 20px; text-align: right; padding-right: 5px; font-weight: bold; }
        .g702-line-desc { width: 45%; }
        .g702-line-value { width: 20%; text-align: right; border-bottom: 1px solid #000; }
        
        .g702-signatures { width: 100%; margin-top: 30px; border-collapse: collapse; page-break-after: always; }
        .g702-signatures td { vertical-align: top; padding: 10px; }
        .sig-block { border-top: 1px solid #000; padding-top: 5px; margin-top: 40px; }
        
        /* G703 Continuation Sheet */
        .g703-header { width: 100%; margin-bottom: 15px; }
        
        .g703-grid { width: 100%; border-collapse: collapse; font-size: 9px; }
        .g703-grid th, .g703-grid td { border: 1px solid #000; padding: 4px; text-align: right; vertical-align: middle; }
        .g703-grid th { background-color: #f4f4f4; text-align: center; font-weight: bold; }
        .g703-grid .col-a { text-align: center; width: 4%; }
        .g703-grid .col-b { text-align: left; width: 20%; }
        .g703-grid .col-rest { width: 10%; }
        .g703-grid td.col-a, .g703-grid td.col-b { font-weight: bold; }
        
        .totals-row td { font-weight: bold; background-color: #eee; }
    </style>
</head>
<body>

<?php
// --- Calculate Header Mathematics ---
$originalContractSum = 0;
$totalPrev      = 0;
$totalThisPeriod= 0;
$totalStored    = 0;

foreach ($sovLines as $line) {
    $id = $line['id'];
    $originalContractSum += (float)$line['scheduled_value'];
    
    $prev = isset($previousProgress[$id]) ? (float)$previousProgress[$id] : 0.00;
    $thisWork = isset($mappedItems[$id]) ? (float)$mappedItems[$id]['work_completed_this_period'] : 0.00;
    $thisMat  = isset($mappedItems[$id]) ? (float)$mappedItems[$id]['materials_presently_stored'] : 0.00;
    
    $totalPrev += $prev;
    $totalThisPeriod += $thisWork;
    $totalStored += $thisMat;
}

// Line 1:
$line1 = $originalContractSum;
// Line 2: (No Change Orders yet)
$line2 = 0.00; 
// Line 3:
$line3 = $line1 + $line2;
// Line 4: Total Completed and Stored
$line4 = $totalPrev + $totalThisPeriod + $totalStored;
// Line 5: Retainage
$retainagePct = (float)$payApp['retainage_percentage'];
$line5 = $line4 * ($retainagePct / 100);
// Line 6: Total Earned Less Retainage
$line6 = $line4 - $line5;
// Line 7: Less Previous Certificates (Since we haven't strictly tracked paid certs, we assume previous earned less previous retainage)
// For a true G702, this is a hard-coded historical value. We will estimate it based on totalPrev.
$previousRetainage = $totalPrev * ($retainagePct / 100);
$line7 = $totalPrev - $previousRetainage;
// Line 8: Current Payment Due
$line8 = $line6 - $line7;
// Line 9: Balance to Finish Inclusive of Retainage
$line9 = $line3 - $line6;
?>

<!-- ========================================== -->
<!-- PAGE 1: AIA G702 SUMMARY FORM              -->
<!-- ========================================== -->

<table class="g702-header-table">
    <tr>
        <td style="width: 50%;">
            <div class="g702-title">APPLICATION AND CERTIFICATE FOR PAYMENT</div>
            <div>AIA Document G702</div>
        </td>
        <td style="width: 50%;" class="text-right">
            <strong>APPLICATION NO:</strong> <?= str_pad($payApp['application_no'], 3, '0', STR_PAD_LEFT) ?><br>
            <strong>PERIOD TO:</strong> <?= date('F d, Y', strtotime($payApp['period_to'])) ?><br>
            <strong>PROJECT NOS:</strong> <?= esc($project['id']) ?>
        </td>
    </tr>
</table>

<table class="g702-meta">
    <tr>
        <td>
            <strong>TO OWNER:</strong><br>
            Fleury CRM Client<br>
            123 Owner Avenue<br>
            City, ST 12345
        </td>
        <td>
            <strong>FROM CONTRACTOR:</strong><br>
            Fleury Construction LLC<br>
            456 Builder Lane<br>
            City, ST 12345
        </td>
        <td>
            <strong>PROJECT:</strong><br>
            <?= esc($project['title']) ?><br>
            <?= esc($project['location'] ?? '') ?>
        </td>
        <td>
            <strong>VIA ARCHITECT:</strong><br>
            Master Architecture Group<br>
            789 Design Blvd<br>
            City, ST 12345
        </td>
    </tr>
</table>

<h3>CONTRACTOR'S APPLICATION FOR PAYMENT</h3>
<p>Application is made for payment, as shown below, in connection with the Contract.<br>Continuation Sheet, AIA Document G703, is attached.</p>

<table class="g702-body">
    <tr>
        <td class="g702-line-num">1.</td>
        <td class="g702-line-desc">ORIGINAL CONTRACT SUM</td>
        <td class="g702-line-value">$<?= number_format($line1, 2) ?></td>
        <td></td>
    </tr>
    <tr>
        <td class="g702-line-num">2.</td>
        <td class="g702-line-desc">Net change by Change Orders</td>
        <td class="g702-line-value">$<?= number_format($line2, 2) ?></td>
        <td></td>
    </tr>
    <tr>
        <td class="g702-line-num">3.</td>
        <td class="g702-line-desc">CONTRACT SUM TO DATE (Line 1 &plusmn; 2)</td>
        <td class="g702-line-value">$<?= number_format($line3, 2) ?></td>
        <td></td>
    </tr>
    <tr>
        <td class="g702-line-num">4.</td>
        <td class="g702-line-desc">TOTAL COMPLETED & STORED TO DATE<br><em>(Column G on G703)</em></td>
        <td class="g702-line-value">$<?= number_format($line4, 2) ?></td>
        <td></td>
    </tr>
    <tr>
        <td class="g702-line-num">5.</td>
        <td class="g702-line-desc">
            RETAINAGE:<br>
            a. <?= number_format($retainagePct, 2) ?>% of Completed Work<br>
            b. <?= number_format($retainagePct, 2) ?>% of Stored Material<br>
            Total Retainage (Lines 5a + 5b or Total in Column I of G703)
        </td>
        <td class="g702-line-value"><br><br><br>$<?= number_format($line5, 2) ?></td>
        <td></td>
    </tr>
    <tr>
        <td class="g702-line-num">6.</td>
        <td class="g702-line-desc">TOTAL EARNED LESS RETAINAGE<br><em>(Line 4 Less Line 5 Total)</em></td>
        <td class="g702-line-value">$<?= number_format($line6, 2) ?></td>
        <td></td>
    </tr>
    <tr>
        <td class="g702-line-num">7.</td>
        <td class="g702-line-desc">LESS PREVIOUS CERTIFICATES FOR PAYMENT<br><em>(Line 6 from prior Certificate)</em></td>
        <td class="g702-line-value">$<?= number_format($line7, 2) ?></td>
        <td></td>
    </tr>
    <tr>
        <td class="g702-line-num">8.</td>
        <td class="g702-line-desc bold">CURRENT PAYMENT DUE</td>
        <td class="g702-line-value bold text-right" style="border-bottom: 3px double #000; font-size: 14px;">$<?= number_format($line8, 2) ?></td>
        <td></td>
    </tr>
    <tr>
        <td class="g702-line-num">9.</td>
        <td class="g702-line-desc">BALANCE TO FINISH, INCLUDING RETAINAGE<br><em>(Line 3 Less Line 6)</em></td>
        <td class="g702-line-value">$<?= number_format($line9, 2) ?></td>
        <td></td>
    </tr>
</table>

<table class="g702-signatures">
    <tr>
        <td style="width: 50%; border-right: 1px solid #ccc;">
            <p>The undersigned Contractor certifies that to the best of the Contractor's knowledge, information and belief the Work covered by this Application for Payment has been completed in accordance with the Contract Documents, that all amounts have been paid by the Contractor for Work for which previous Certificates for Payment were issued and payments received from the Owner, and that current payment shown herein is now due.</p>
            
            <strong>CONTRACTOR:</strong> Fleury Construction LLC
            <div class="sig-block">By: ____________________________________ Date: ______________</div>
            
            <p style="margin-top: 20px;">
                State of: _________________<br>
                County of: _________________<br>
                Subscribed and sworn to before me this _____ day of ________, 20____<br><br>
                Notary Public: _________________________________<br>
                My Commission expires: _________________________
            </p>
        </td>
        <td style="width: 50%;">
            <strong>ARCHITECT'S CERTIFICATE FOR PAYMENT</strong>
            <p>In accordance with the Contract Documents, based on on-site observations and the data comprising the application, the Architect certifies to the Owner that to the best of the Architect's knowledge, information and belief the Work has progressed as indicated, the quality of the Work is in accordance with the Contract Documents, and the Contractor is entitled to payment of the AMOUNT CERTIFIED.</p>
            
            <p><strong>AMOUNT CERTIFIED ......... $________________________</strong><br>
            <em>(Attach explanation if amount certified differs from the amount applied. Initial all figures on this Application and on the Continuation Sheet that are changed to conform with the amount certified.)</em></p>
            
            <strong>ARCHITECT:</strong> Master Architecture Group
            <div class="sig-block">By: ____________________________________ Date: ______________</div>
            <p><em>This Certificate is not negotiable. The AMOUNT CERTIFIED is payable only to the Contractor named herein. Issuance, payment and acceptance of payment are without prejudice to any rights of the Owner or Contractor under this Contract.</em></p>
        </td>
    </tr>
</table>

<!-- ========================================== -->
<!-- PAGE 2: AIA G703 CONTINUATION SHEET        -->
<!-- ========================================== -->

<table class="g703-header">
    <tr>
        <td style="width: 50%;">
            <strong style="font-size: 14px;">CONTINUATION SHEET</strong><br>
            AIA Document G703<br>
            Page 2 of 2
        </td>
        <td style="width: 50%; text-align: right;">
            <strong>APPLICATION NO:</strong> <?= str_pad($payApp['application_no'], 3, '0', STR_PAD_LEFT) ?><br>
            <strong>APPLICATION DATE:</strong> <?= date('M d, Y') ?><br>
            <strong>PERIOD TO:</strong> <?= date('F d, Y', strtotime($payApp['period_to'])) ?>
        </td>
    </tr>
</table>

<table class="g703-grid">
    <thead>
        <tr>
            <th class="col-a" rowspan="2">A<br>ITEM NO.</th>
            <th class="col-b" rowspan="2">B<br>DESCRIPTION OF WORK</th>
            <th class="col-rest" rowspan="2">C<br>SCHEDULED VALUE</th>
            <th colspan="2" class="text-center">WORK COMPLETED</th>
            <th class="col-rest" rowspan="2">F<br>MATERIALS PRESENTLY STORED<br>(Not in D or E)</th>
            <th class="col-rest" rowspan="2">G<br>TOTAL COMPLETED AND STORED TO DATE<br>(D+E+F)</th>
            <th class="col-rest" rowspan="2">H<br>%<br>(G &divide; C)</th>
            <th class="col-rest" rowspan="2">I<br>BALANCE TO FINISH<br>(C - G)</th>
            <th class="col-rest" rowspan="2">J<br>RETAINAGE<br>(If Variable Rate)</th>
        </tr>
        <tr>
            <th class="col-rest">D<br>FROM PREVIOUS APPLICATION (D+E)</th>
            <th class="col-rest">E<br>THIS PERIOD</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sovLines as $line): 
            $id = $line['id'];
            $scheduled = (float)$line['scheduled_value'];
            $prev = isset($previousProgress[$id]) ? (float)$previousProgress[$id] : 0.00;
            $thisWork = isset($mappedItems[$id]) ? (float)$mappedItems[$id]['work_completed_this_period'] : 0.00;
            $thisMat  = isset($mappedItems[$id]) ? (float)$mappedItems[$id]['materials_presently_stored'] : 0.00;
            
            $toDate = $prev + $thisWork + $thisMat;
            $pct = $scheduled > 0 ? ($toDate / $scheduled) * 100 : 0;
            $balance = $scheduled - $toDate;
            $rowRetainage = $toDate * ($retainagePct / 100);
        ?>
        <tr>
            <td class="col-a"><?= esc($line['item_no']) ?></td>
            <td class="col-b"><?= esc($line['description']) ?></td>
            <td><?= number_format($scheduled, 2) ?></td>
            <td><?= number_format($prev, 2) ?></td>
            <td><?= number_format($thisWork, 2) ?></td>
            <td><?= number_format($thisMat, 2) ?></td>
            <td><?= number_format($toDate, 2) ?></td>
            <td class="text-center"><?= number_format($pct, 2) ?>%</td>
            <td><?= number_format($balance, 2) ?></td>
            <td><?= number_format($rowRetainage, 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr class="totals-row">
            <td colspan="2" class="text-right">GRAND TOTALS:</td>
            <td>$<?= number_format($originalContractSum, 2) ?></td>
            <td>$<?= number_format($totalPrev, 2) ?></td>
            <td>$<?= number_format($totalThisPeriod, 2) ?></td>
            <td>$<?= number_format($totalStored, 2) ?></td>
            <td>$<?= number_format($line4, 2) ?></td>
            <td class="text-center"><?= $originalContractSum > 0 ? number_format(($line4 / $originalContractSum) * 100, 2) : '0.00' ?>%</td>
            <td>$<?= number_format($originalContractSum - $line4, 2) ?></td>
            <td>$<?= number_format($line5, 2) ?></td>
        </tr>
    </tfoot>
</table>

</body>
</html>
