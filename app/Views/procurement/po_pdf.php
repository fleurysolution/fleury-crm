<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($po['po_number']) ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .header-table {
            width: 100%;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-info {
            font-size: 12px;
            color: #555;
        }
        .title {
            text-align: right;
        }
        .title h1 {
            margin: 0;
            font-size: 28px;
            color: #2c3e50;
        }
        .po-meta {
            margin-top: 10px;
            font-size: 14px;
        }
        
        .parties-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .parties-table td {
            width: 50%;
            vertical-align: top;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        .parties-table h4 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background: #2c3e50;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .items-table th.right, .items-table td.right {
            text-align: right;
        }
        .items-table th.center, .items-table td.center {
            text-align: center;
        }
        
        .totals-table {
            width: 40%;
            float: right;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .totals-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .totals-table .grand-total td {
            font-weight: bold;
            font-size: 16px;
            color: #28a745;
            border-top: 2px solid #2c3e50;
            border-bottom: 0;
        }

        .notes {
            clear: both;
            margin-top: 40px;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #17a2b8;
        }

        .signatures {
            margin-top: 60px;
            width: 100%;
        }
        .signatures td {
            width: 50%;
            vertical-align: bottom;
            padding: 20px;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            height: 40px;
        }
    </style>
</head>
<body>

<div class="header">
    <table class="header-table">
        <tr>
            <td width="50%">
                <h2>Fleury CRM Contracting</h2>
                <div class="company-info">
                    123 Builder Lane<br>
                    Suite 400<br>
                    Construction City, ST 12345<br>
                    procurement@fleurycrm.local
                </div>
            </td>
            <td width="50%" class="title">
                <h1>PURCHASE ORDER</h1>
                <div class="po-meta">
                    <strong>PO Number:</strong> <?= esc($po['po_number']) ?><br>
                    <strong>Date Issued:</strong> <?= date('F d, Y', strtotime($po['created_at'])) ?><br>
                    <strong>Requisitioner:</strong> <?= esc($po['creator_name']) ?>
                </div>
            </td>
        </tr>
    </table>
</div>

<table class="parties-table">
    <tr>
        <td>
            <h4>TO VENDOR:</h4>
            <strong><?= esc($po['vendor_name'] ?: 'Company Not Provided') ?></strong><br>
            <em>Please ship / provide services as directed.</em>
        </td>
        <td>
            <h4>SHIP TO PROJECT:</h4>
            <strong><?= esc($project['title']) ?></strong><br>
            <?= esc($project['location'] ?? 'No specific site address provided') ?><br><br>
            <strong>Required Delivery / Start:</strong><br>
            <?= $po['delivery_date'] ? date('F d, Y', strtotime($po['delivery_date'])) : 'TBD' ?>
        </td>
    </tr>
</table>

<table class="items-table">
    <thead>
        <tr>
            <th width="50%">DESCRIPTION</th>
            <th width="10%" class="center">QTY</th>
            <th width="15%" class="center">UNIT</th>
            <th width="10%" class="right">UNIT PRICE</th>
            <th width="15%" class="right">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($items)): ?>
            <tr><td colspan="5" class="center">No items listed.</td></tr>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= esc($item['description']) ?></td>
                    <td class="center"><?= number_format($item['quantity'], 2) ?></td>
                    <td class="center"><?= esc($item['unit']) ?></td>
                    <td class="right">$<?= number_format($item['unit_price'], 2) ?></td>
                    <td class="right">$<?= number_format($item['total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<table class="totals-table">
    <tr class="grand-total">
        <td>ORDER TOTAL:</td>
        <td class="right">$<?= number_format($po['total_amount'], 2) ?></td>
    </tr>
</table>

<div class="notes">
    <strong>Terms & Notes:</strong><br><br>
    <?= nl2br(esc($po['notes'] ?: 'No additional notes provided.')) ?>
</div>

<table class="signatures">
    <tr>
        <td>
            <div class="sig-line"></div>
            <strong>Authorized GC Signature</strong><br>
            Date:
        </td>
        <td>
            <div class="sig-line"></div>
            <strong>Vendor Acceptance Signature</strong><br>
            Date:
        </td>
    </tr>
</table>

</body>
</html>
