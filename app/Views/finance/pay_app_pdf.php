<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application and Certificate for Payment</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        h2 { margin: 0; padding: 0; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .meta-table td { padding: 5px; border: 1px solid #ccc; }
        .grid-table { width: 100%; border-collapse: collapse; }
        .grid-table th, .grid-table td { border: 1px solid #000; padding: 5px; text-align: right; }
        .grid-table th { background-color: #f4f4f4; text-align: center; font-size: 10px; }
        .text-left { text-align: left !important; }
        .text-center { text-align: center !important; }
        .totals { font-weight: bold; background-color: #fdfdfd; }
    </style>
</head>
<body>

<?php
$totalScheduled = 0;
$totalPrev      = 0;
$totalThisPeriod= 0;
$totalStored    = 0;
?>

<div class="header">
    <h2>APPLICATION AND CERTIFICATE FOR PAYMENT</h2>
</div>

<table class="meta-table">
    <tr>
        <td><strong>Project:</strong> <?= esc($project['title']) ?><br><?= isset($project['location']) ? esc($project['location']) : '' ?></td>
        <td><strong>Application No:</strong> <?= str_pad($payApp['application_no'], 3, '0', STR_PAD_LEFT) ?><br><strong>Period To:</strong> <?= date('F d, Y', strtotime($payApp['period_to'])) ?></td>
    </tr>
</table>

<table class="grid-table">
    <thead>
        <tr>
            <th class="text-left" style="width: 5%">ITEM NO.</th>
            <th class="text-left" style="width: 25%">DESCRIPTION OF WORK</th>
            <th style="width: 10%">SCHEDULED VALUE</th>
            <th style="width: 10%">PREVIOUS APPLICATIONS</th>
            <th style="width: 10%">WORK THIS PERIOD</th>
            <th style="width: 10%">MATERIALS STORED</th>
            <th style="width: 10%">TOTAL COMPLETED & STORED</th>
            <th style="width: 10%">% (G/C)</th>
            <th style="width: 10%">BALANCE TO FINISH</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sovLines as $line): 
            $id = $line['id'];
            $scheduled = (float)$line['scheduled_value'];
            $prev = isset($previousProgress[$id]) ? (float)$previousProgress[$id] : 0.00;
            
            $thisWork = isset($mappedItems[$id]) ? (float)$mappedItems[$id]['work_completed_this_period'] : 0.00;
            $thisMat  = isset($mappedItems[$id]) ? (float)$mappedItems[$id]['materials_presently_stored'] : 0.00;
            
            $totalScheduled += $scheduled;
            $totalPrev += $prev;
            $totalThisPeriod += $thisWork;
            $totalStored += $thisMat;

            $toDate = $prev + $thisWork + $thisMat;
            $balance = $scheduled - $toDate;
            $pct = $scheduled > 0 ? ($toDate / $scheduled) * 100 : 0;
        ?>
        <tr>
            <td class="text-left"><?= esc($line['item_no']) ?></td>
            <td class="text-left"><?= esc($line['description']) ?></td>
            <td>$<?= number_format($scheduled, 2) ?></td>
            <td>$<?= number_format($prev, 2) ?></td>
            <td>$<?= number_format($thisWork, 2) ?></td>
            <td>$<?= number_format($thisMat, 2) ?></td>
            <td>$<?= number_format($toDate, 2) ?></td>
            <td class="text-center"><?= number_format($pct, 2) ?>%</td>
            <td>$<?= number_format($balance, 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    
    <?php
        $globalToDate = $totalPrev + $totalThisPeriod + $totalStored;
        $globalBalance = $totalScheduled - $globalToDate;
        $globalPct = $totalScheduled > 0 ? ($globalToDate / $totalScheduled) * 100 : 0;
        
        $retainageAmount = $globalToDate * ($payApp['retainage_percentage'] / 100);
        $totalEarned = $globalToDate - $retainageAmount;
    ?>
    <tfoot class="totals">
        <tr>
            <td colspan="2" class="text-left">GRAND TOTALS</td>
            <td>$<?= number_format($totalScheduled, 2) ?></td>
            <td>$<?= number_format($totalPrev, 2) ?></td>
            <td>$<?= number_format($totalThisPeriod, 2) ?></td>
            <td>$<?= number_format($totalStored, 2) ?></td>
            <td>$<?= number_format($globalToDate, 2) ?></td>
            <td class="text-center"><?= number_format($globalPct, 2) ?>%</td>
            <td>$<?= number_format($globalBalance, 2) ?></td>
        </tr>
        <tr>
            <td colspan="6" class="text-left">LESS <?= number_format($payApp['retainage_percentage'], 2) ?>% RETAINAGE</td>
            <td style="color: red;">-$<?= number_format($retainageAmount, 2) ?></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="6" class="text-left">TOTAL EARNED LESS RETAINAGE</td>
            <td>$<?= number_format($totalEarned, 2) ?></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

</body>
</html>
