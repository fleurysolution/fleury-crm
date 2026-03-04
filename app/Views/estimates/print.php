<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimate #<?= esc($estimate['id']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #fff; color: #333; line-height: 1.5; margin: 0; padding: 0; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); font-size: 16px; min-height: 1056px; }
        .invoice-box table { width: 100%; text-align: left; }
        .invoice-box table.item-table { border-collapse: collapse; margin-top: 20px; }
        .invoice-box table.item-table th, .invoice-box table.item-table td { border: 1px solid #eee; padding: 12px; }
        .invoice-box table.item-table th { background: #f8f9fa; border-bottom: 2px solid #ddd; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mt-4 { margin-top: 1.5rem; }
        .mt-5 { margin-top: 3rem; }
        h1, h2, h3, h4, h5, h6 { margin-top: 0; }
        @media only screen and (max-width: 600px) {
            .invoice-box { padding: 15px; border: none; box-shadow: none; }
        }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: transparent; }
            .invoice-box { border: 0; box-shadow: none; margin: 0; padding: 0; max-width: 100%; min-height: auto; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print" style="text-align: center; margin-bottom: 20px; padding: 10px; background: #f8f9fa; border-bottom: 1px solid #ddd;">
    <button onclick="window.print()" class="btn btn-primary">Print PDF</button>
    <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
</div>

<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td class="title">
                            <h2 style="font-size: 45px; line-height: 45px; color: #333; font-weight: 800; letter-spacing: -2px; text-transform: uppercase;">Estimate</h2>
                            <p style="color:#777; font-size: 14px; margin-top: 5px;">#<?= esc($estimate['id']) ?></p>
                        </td>
                        <td class="text-right">
                            <h4 class="mb-1" style="font-size:18px;">Fleury Solution LLC</h4>
                            <div style="font-size: 14px; color: #666;">
                                1540 Hwy 138 SE, Suite 3K Conyers, GA 30013<br>
                                Phone: +1 770 410 8378<br>
                                Email: admin@fleurysolutions.com<br>
                                Website: https://fleurysolutions.com
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr class="information">
            <td colspan="2">
                <table style="margin-top: 40px; margin-bottom: 20px;">
                    <tr>
                        <td>
                            <strong style="color: #888; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Estimate To</strong><br>
                            <?php if ($client): ?>
                                <h4 class="mb-1 fw-bold" style="font-size:18px; color:#222; margin-top:8px;"><?= esc($client['company_name']) ?></h4>
                                <div style="font-size: 14px; color: #555;">
                                    <?= esc($client['address']) ?><br>
                                    <?= esc($client['city']) ?>, <?= esc($client['state']) ?> <?= esc($client['zip']) ?><br>
                                    <?= esc($client['country']) ?>
                                </div>
                            <?php else: ?>
                                <span style="color:red;">Client missing or deleted.</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right" style="vertical-align: top;">
                            <table style="width: auto; float: right; font-size:14px;">
                                <tr>
                                    <td class="fw-bold" style="padding-right: 15px; color:#555;">Issue Date:</td>
                                    <td><?= date('Y-m-d', strtotime($estimate['estimate_date'])) ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold" style="padding-right: 15px; color:#555;">Valid Until:</td>
                                    <td><?= date('Y-m-d', strtotime($estimate['valid_until'])) ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold" style="padding-right: 15px; color:#555;">Status:</td>
                                    <td><strong><?= ucfirst($estimate['status']) ?></strong></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="item-table mt-4" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right" style="width: 80px;">Qty</th>
                <th class="text-right" style="width: 120px;">Rate</th>
                <th class="text-right" style="width: 120px;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $subtotal = 0; ?>
            <?php if (empty($items)): ?>
                <tr>
                    <td colspan="4" class="text-center text-muted" style="padding: 30px;">No items exist on this estimate.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($items as $item): 
                    $itemTotal = $item['quantity'] * $item['rate']; 
                    $subtotal += $itemTotal; 
                ?>
                    <tr>
                        <td>
                            <strong style="color:#222; display:block;"><?= esc($item['title']) ?></strong>
                            <?php if (!empty($item['description'])): ?>
                                <span style="color:#777; font-size: 13px; display:block; margin-top:4px;"><?= nl2br(esc($item['description'])) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right"><?= $item['quantity'] ?></td>
                        <td class="text-right"><?= number_format($item['rate'], 2) ?></td>
                        <td class="text-right fw-bold"><?= number_format($itemTotal, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right" style="padding-top:20px;">Sub Total:</td>
                <td class="text-right fw-bold" style="padding-top:20px;"><?= number_format($subtotal, 2) ?></td>
            </tr>
            <tr>
                <td colspan="3" class="text-right fw-bold" style="font-size: 20px; color: #222; border-top: 2px solid #ddd; padding-top: 15px;">TOTAL:</td>
                <td class="text-right fw-bold" style="font-size: 20px; color: #0d6efd; border-top: 2px solid #ddd; padding-top: 15px;">
                    <?= esc($estimate['currency_symbol'] ?? '$') ?><?= number_format($subtotal, 2) ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <?php if (!empty($estimate['note'])): ?>
        <div class="mt-5 p-4" style="background:#f9f9f9; border-left: 4px solid #ddd; font-size: 14px; color: #555;">
            <strong style="display:block; margin-bottom:8px; color:#333;">Terms / Notes:</strong>
            <?= nl2br(esc($estimate['note'])) ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
