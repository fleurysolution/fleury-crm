<?php
// app/Views/projects/tabs/report_inline.php
// Lightweight project report summary embedded in the project workspace tab

$rm   = new \App\Models\ReportModel();
$kpis = $rm->projectSummary($project['id']);

$tasks      = $kpis['tasks']      ?? [];
$boq        = $kpis['boq']        ?? [];
$rfis       = $kpis['rfis']       ?? [];
$punch      = $kpis['punch']      ?? [];
$milestones = $kpis['milestones'] ?? [];
$certs      = $kpis['certs']      ?? [];

$taskPct = ($tasks['total']??0) > 0 ? round(($tasks['done']??0)/($tasks['total']??0)*100) : 0;
$boqPct  = ($boq['budget']??0)  > 0 ? round(($boq['actual']??0)/($boq['budget']??0)*100,1) : 0;
$certPct = ($certs['total']??0) > 0 ? round(($certs['paid']??0)/($certs['total']??0)*100,1) : 0;
$milPct  = ($milestones['total']??0) > 0 ? round(($milestones['done']??0)/($milestones['total']??0)*100) : 0;
?>

<div class="d-flex justify-content-end mb-3 gap-2">
    <a href="<?= site_url("projects/{$project['id']}/report/print") ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-print me-1"></i>Print / PDF
    </a>
    <a href="<?= site_url("projects/{$project['id']}/report") ?>" class="btn btn-sm btn-primary">
        <i class="fa-solid fa-chart-bar me-1"></i>Full Report
    </a>
</div>

<!-- Mini KPI cards strip -->
<div class="row g-2 mb-4">
<?php $miniKpis = [
    ['Tasks Done',       "{$taskPct}%",                       $tasks['done']??0, 'of '.($tasks['total']??0).' tasks',    'success', 'fa-list-check'],
    ['Milestones',       "{$milPct}%",                        $milestones['done']??0, '/ '.($milestones['total']??0),   'info',    'fa-flag'],
    ['BOQ Progress',     "{$boqPct}%",                        '$'.number_format($boq['actual']??0,0), 'of $'.number_format($boq['budget']??0,0), $boqPct>100?'danger':'warning', 'fa-coins'],
    ['Cert Paid',        "{$certPct}%",                       '$'.number_format($certs['paid']??0,0), 'of $'.number_format($certs['total']??0,0), 'primary', 'fa-file-invoice-dollar'],
    ['Open RFIs',        $rfis['open']??0,                    $rfis['answered']??0, 'answered',                          'warning', 'fa-circle-question'],
    ['Punch Items',      ($punch['open']??0).' open',         $punch['resolved']??0, 'resolved',                        'danger',  'fa-clipboard-check'],
]; ?>
<?php foreach ($miniKpis as [$label,$main,$sub,$sublabel,$col,$icon]): ?>
<div class="col-md-2 col-6">
    <div class="card border-0 shadow-sm text-center py-3 h-100" style="border-radius:10px;border-top:3px solid var(--bs-<?= $col ?>)!important;">
        <i class="fa-solid <?= $icon ?> text-<?= $col ?> mb-1"></i>
        <div class="fw-bold fs-5"><?= $main ?></div>
        <div class="text-<?= $col ?> small"><?= $sub ?> <?= $sublabel ?></div>
        <div class="text-muted" style="font-size:11px;"><?= $label ?></div>
    </div>
</div>
<?php endforeach; ?>
</div>

<!-- Donut charts row -->
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius:10px;">
            <div class="card-body text-center">
                <h6 class="fw-semibold mb-2 small">Tasks</h6>
                <canvas id="rpTaskDonut" height="140"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius:10px;">
            <div class="card-body text-center">
                <h6 class="fw-semibold mb-2 small">RFIs</h6>
                <canvas id="rpRfiDonut" height="140"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius:10px;">
            <div class="card-body text-center">
                <h6 class="fw-semibold mb-2 small">Punch List</h6>
                <canvas id="rpPunchDonut" height="140"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Cost progress bar -->
<div class="card border-0 shadow-sm py-3 px-4" style="border-radius:10px;">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="fw-semibold small">Cost vs Budget</span>
        <span class="small text-muted">
            $<?= number_format($boq['actual']??0,0) ?> spent
            &nbsp;/&nbsp; $<?= number_format($boq['budget']??0,0) ?> BOQ
            &nbsp;|&nbsp; Budget: $<?= number_format($project['budget']??0,0) ?>
        </span>
    </div>
    <div class="progress" style="height:16px;border-radius:8px;">
        <?php $bp = min($boqPct, 100); ?>
        <div class="progress-bar <?= $boqPct>100?'bg-danger':'bg-success' ?> fw-semibold" 
             style="width:<?= $bp ?>%;font-size:11px;"><?= $boqPct ?>%</div>
    </div>
    <div class="d-flex justify-content-between mt-2 small text-muted">
        <span>Certs Paid: <strong class="text-success">$<?= number_format($certs['paid']??0,0) ?></strong></span>
        <span>Diary entries (30d): <strong><?= $kpis['diary_30d']??0 ?></strong></span>
        <span>Overdue Tasks: <strong class="text-danger"><?= $tasks['overdue']??0 ?></strong></span>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
function donut(id, labels, data, colors) {
    const el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
        type:'doughnut',
        data:{ labels, datasets:[{data, backgroundColor:colors, borderWidth:2}]},
        options:{ cutout:'65%', plugins:{legend:{position:'bottom', labels:{font:{size:10},boxWidth:10}}} }
    });
}
donut('rpTaskDonut',
    ['Done','In Progress','Todo','Overdue'],
    [<?= ($tasks['done']??0) ?>,<?= ($tasks['in_progress']??0) ?>,<?= ($tasks['todo']??0) ?>,<?= ($tasks['overdue']??0) ?>],
    ['#198754','#0d6efd','#6c757d','#dc3545']);
donut('rpRfiDonut',
    ['Open','Answered','Closed'],
    [<?= ($rfis['open']??0) ?>,<?= ($rfis['answered']??0) ?>,<?= ($rfis['closed']??0) ?>],
    ['#ffc107','#0dcaf0','#198754']);
donut('rpPunchDonut',
    ['Open','Resolved','Closed'],
    [<?= ($punch['open']??0) ?>,<?= ($punch['resolved']??0) ?>,<?= ($punch['closed']??0) ?>],
    ['#dc3545','#ffc107','#198754']);
})();
</script>
