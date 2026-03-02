<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($contract['contract_number']) ?> - <?= esc($contract['title']) ?></title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 14px;
            margin: 0;
            padding: 20px;
        }
        h1, h2, h3 { color: #1a1a1a; margin-top: 0; }
        .header {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header-table { width: 100%; }
        .header-table td { vertical-align: top; }
        .project-details { text-align: right; }
        
        .section { margin-bottom: 30px; }
        .section-title {
            background-color: #f8f9fa;
            padding: 8px 12px;
            font-weight: bold;
            border-left: 4px solid #0d6efd;
            margin-bottom: 15px;
        }
        
        table.details { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.details th, table.details td {
            padding: 10px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        table.details th { background-color: #f8f9fa; width: 35%; }
        
        .scope-content {
            border: 1px solid #dee2e6;
            padding: 15px;
            background-color: #fff;
            min-height: 100px;
        }
        
        .signature-grid {
            width: 100%;
            margin-top: 50px;
            border-collapse: collapse;
        }
        .signature-grid td {
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }
        .signature-box {
            border: 1px solid #ccc;
            padding: 20px;
            min-height: 150px;
            text-align: center;
        }
        .signature-img {
            max-width: 200px;
            max-height: 80px;
            margin-bottom: 15px;
        }
        .signature-meta {
            font-size: 11px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        /* Helpers */
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .mt-4 { margin-top: 30px; }
    </style>
</head>
<body>

    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h2>CONTRACT AGREEMENT</h2>
                    <div><strong>Contract No:</strong> <?= esc($contract['contract_number']) ?></div>
                    <div><strong>Date Issued:</strong> <?= date('F j, Y', strtotime($contract['created_at'])) ?></div>
                    <div><strong>Status:</strong> <?= strtoupper(str_replace('_', ' ', $contract['status'])) ?></div>
                </td>
                <td class="project-details">
                    <h3>Project Details</h3>
                    <div><strong><?= esc($project['title']) ?></strong></div>
                    <div><?= esc($project['address']) ?? 'Address Not Provided' ?></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">1. Contract Information</div>
        <table class="details">
            <tr>
                <th>Contract Title</th>
                <td><?= esc($contract['title']) ?></td>
            </tr>
            <tr>
                <th>Contract Type</th>
                <td><?= ucfirst($contract['type']) ?></td>
            </tr>
            <tr>
                <th>Contractor / Builder</th>
                <td><?= esc($contract['contractor_name'] ?? 'Not Specified') ?></td>
            </tr>
            <tr>
                <th>Start Date</th>
                <td><?= $contract['start_date'] ? date('M j, Y', strtotime($contract['start_date'])) : 'TBD' ?></td>
            </tr>
            <tr>
                <th>End Date</th>
                <td><?= $contract['end_date'] ? date('M j, Y', strtotime($contract['end_date'])) : 'TBD' ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">2. Financial Summary</div>
        <table class="details">
            <tr>
                <th>Original Contract Value</th>
                <td class="fw-bold"><?= number_format($contract['value'], 2) ?> <?= esc($contract['currency']) ?></td>
            </tr>
            <tr>
                <th>Approved Variation Orders</th>
                <td><?= ($totalChg >= 0 ? '+' : '') . number_format($totalChg, 2) ?> <?= esc($contract['currency']) ?></td>
            </tr>
            <tr>
                <th>Current Contract Value</th>
                <td class="fw-bold" style="font-size: 16px;"><?= number_format($currentVal, 2) ?> <?= esc($contract['currency']) ?></td>
            </tr>
            <tr>
                <th>Retention Percentage</th>
                <td><?= number_format($contract['retention_pct'] ?? 10, 1) ?>%</td>
            </tr>
        </table>
    </div>

    <?php if (!empty($contract['scope'])): ?>
    <div class="section">
        <div class="section-title">3. Scope of Work</div>
        <div class="scope-content">
            <?= nl2br(esc($contract['scope'])) ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="mt-4" style="page-break-inside: avoid;">
        <div class="section-title">4. Signatures & Acceptance</div>
        <p>IN WITNESS WHEREOF, the parties hereto have caused this Agreement to be executed by their duly authorized representatives.</p>
        
        <table class="signature-grid">
            <tr>
                <td>
                    <strong>Client Representative</strong>
                    <div class="signature-box mt-4">
                        <?php if (!empty($contract['client_signature_data'])): ?>
                            <img src="<?= esc($contract['client_signature_data']) ?>" class="signature-img" alt="Client Signature">
                            <div class="signature-meta">
                                Signed Date: <?= date('M j, Y g:i A', strtotime($contract['client_signed_at'])) ?><br>
                                IP Address: <?= esc($contract['client_ip_address']) ?>
                            </div>
                        <?php else: ?>
                            <div style="padding-top: 50px; color: #999;">PENDING SIGNATURE</div>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <strong>Contractor / Builder Representative</strong>
                    <div class="signature-box mt-4">
                        <?php if (!empty($contract['contractor_signature_data'])): ?>
                            <img src="<?= esc($contract['contractor_signature_data']) ?>" class="signature-img" alt="Contractor Signature">
                            <div class="signature-meta">
                                Signed Date: <?= date('M j, Y g:i A', strtotime($contract['contractor_signed_at'])) ?><br>
                                IP Address: <?= esc($contract['contractor_ip_address']) ?>
                            </div>
                        <?php else: ?>
                            <div style="padding-top: 50px; color: #999;">PENDING SIGNATURE</div>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
