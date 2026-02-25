<?php
// app/Views/reports/print.php — Print/PDF-friendly project report
$tasks      = $kpis['tasks']      ?? [];
$boq        = $kpis['boq']        ?? [];
$rfis       = $kpis['rfis']       ?? [];
$punch      = $kpis['punch']      ?? [];
$milestones = $kpis['milestones'] ?? [];
$certs      = $kpis['certs']      ?? [];
$diary30    = (int)($kpis['diary_30d'] ?? 0);

$taskPct    = ($tasks['total']??0)>0 ? round(($tasks['done']??0)/($tasks['total']??0)*100) : 0;
$boqPct     = ($boq['budget']??0)>0  ? round(($boq['actual']??0)/($boq['budget']??0)*100,1) : 0;
$certPct    = ($certs['total']??0)>0  ? round(($certs['paid']??0)/($certs['total']??0)*100,1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Project Report — <?= esc($project['title']??'') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: 'Segoe UI', sans-serif; font-size:13px; color:#333; }
@media print {
    .no-print { display:none!important; }
    .card { break-inside:avoid; }
}
.stat-box { border:1px solid #dee2e6; border-radius:8px; padding:12px; text-align:center; }
.stat-num { font-size:1.4rem; font-weight:700; }
h1 { font-size:1.4rem; }
</style>
</head>
<body class="p-4">

<div class="no-print text-end mb-3">
    <button onclick="window.print()" class="btn btn-sm btn-primary me-2">🖨 Print / Save PDF</button>
    <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary">← Back</a>
</div>

<!-- Header -->
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h1><?= esc($project['title']??'') ?> — Project Report</h1>
        <div class="text-muted small">
            Generated: <?= date('d M Y H:i') ?>
            &nbsp;|&nbsp; Status: <strong><?= ucfirst($project['status']??'') ?></strong>
            <?php if ($project['start_date']??null): ?>
            &nbsp;|&nbsp; <?= date('d M Y', strtotime($project['start_date'])) ?> → <?= $project['end_date'] ? date('d M Y', strtotime($project['end_date'])) : 'TBD' ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="text-end small text-muted">
        <div>Budget: <strong>$<?= number_format($project['budget']??0,0) ?></strong></div>
    </div>
</div>

<hr>

<!-- KPI summary row -->
<div class="row g-2 mb-3">
    <?php foreach ([
        ['Tasks',       "{$tasks['done']} / {$tasks['total']}",       $taskPct.'%',  'success'],
        ['Milestones',  ($milestones['done']??0).' / '.($milestones['total']??0), ($milestones['total']??0)>0?round(($milestones['done']??0)/($milestones['total']??0)*100).'%':'—', 'info'],
        ['BOQ Actual',  '$'.number_format($boq['actual']??0,0),       $boqPct.'%',   'warning'],
        ['Certs Paid',  '$'.number_format($certs['paid']??0,0),       $certPct.'%',  'primary'],
        ['Open RFIs',   $rfis['open']??0,                              'of '.($rfis['total']??0), 'warning'],
        ['Punch Open',  ($punch['open']??0),                           'of '.($punch['total']??0), 'danger'],
        ['Diary (30d)', $diary30,                                      'entries',      'secondary'],
        ['Overdue Tasks',$tasks['overdue']??0,                        '',             'danger'],
    ] as [$lbl,$val,$sub,$col]): ?>
    <div class="col-3"><div class="stat-box">
        <div class="stat-num text-<?= $col ?>"><?= $val ?></div>
        <?php if ($sub): ?><div class="small text-<?= $col ?>"><?= $sub ?></div><?php endif; ?>
        <div class="text-muted" style="font-size:11px;"><?= $lbl ?></div>
    </div></div>
    <?php endforeach; ?>
</div>

<!-- Detailed tables -->
<div class="row g-3">
    <!-- Tasks -->
    <div class="col-6">
        <h6 class="fw-semibold">Task Status</h6>
        <table class="table table-sm table-bordered">
            <tbody>
            <?php foreach ([
                ['Done',        $tasks['done']??0,        'success'],
                ['In Progress', $tasks['in_progress']??0, 'primary'],
                ['Todo',        $tasks['todo']??0,        'secondary'],
                ['Overdue',     $tasks['overdue']??0,     'danger'],
                ['Total',       $tasks['total']??0,       'dark'],
            ] as [$lbl,$v,$c]): ?>
            <tr><td><?= $lbl ?></td><td class="text-end fw-semibold text-<?= $c ?>"><?= $v ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- RFIs & Punch -->
    <div class="col-6">
        <h6 class="fw-semibold">RFI Summary</h6>
        <table class="table table-sm table-bordered">
            <tbody>
            <?php foreach ([
                ['Open',     $rfis['open']??0,     'warning'],
                ['Answered', $rfis['answered']??0,  'info'],
                ['Closed',   $rfis['closed']??0,    'success'],
                ['Total',    $rfis['total']??0,     'dark'],
            ] as [$lbl,$v,$c]): ?>
            <tr><td><?= $lbl ?></td><td class="text-end fw-semibold text-<?= $c ?>"><?= $v ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h6 class="fw-semibold mt-2">Punch List</h6>
        <table class="table table-sm table-bordered">
            <tbody>
            <?php foreach ([
                ['Open',     $punch['open']??0,      'danger'],
                ['Resolved', $punch['resolved']??0,  'warning'],
                ['Closed',   $punch['closed']??0,    'success'],
                ['Total',    $punch['total']??0,     'dark'],
            ] as [$lbl,$v,$c]): ?>
            <tr><td><?= $lbl ?></td><td class="text-end fw-semibold text-<?= $c ?>"><?= $v ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Finance -->
<h6 class="fw-semibold mt-2">Finance Summary</h6>
<table class="table table-sm table-bordered">
    <tbody>
    <tr><td>Project Budget</td><td class="text-end">$<?= number_format($project['budget']??0,0) ?></td></tr>
    <tr><td>BOQ Total (Planned)</td><td class="text-end">$<?= number_format($boq['budget']??0,0) ?></td></tr>
    <tr><td>BOQ Actual Spend</td><td class="text-end text-<?= $boqPct>100?'danger':'success' ?>">$<?= number_format($boq['actual']??0,0) ?> (<?= $boqPct ?>%)</td></tr>
    <tr><td>Payment Cert Total</td><td class="text-end">$<?= number_format($certs['total']??0,0) ?></td></tr>
    <tr><td>Certified &amp; Paid</td><td class="text-end text-primary">$<?= number_format($certs['paid']??0,0) ?> (<?= $certPct ?>%)</td></tr>
    </tbody>
</table>

<p class="text-muted mt-4" style="font-size:11px;">This report is auto-generated from live project data. Figures are as at <?= date('d M Y H:i') ?>.</p>
</body>
</html>
