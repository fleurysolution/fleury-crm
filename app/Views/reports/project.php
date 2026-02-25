<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?php
// app/Views/reports/project.php — Single Project Report Page
$tasks      = $kpis['tasks']      ?? [];
$boq        = $kpis['boq']        ?? [];
$rfis       = $kpis['rfis']       ?? [];
$punch      = $kpis['punch']      ?? [];
$milestones = $kpis['milestones'] ?? [];
$certs      = $kpis['certs']      ?? [];
$diary30    = (int)($kpis['diary_30d'] ?? 0);

$taskPct    = ($tasks['total']??0) > 0 ? round(($tasks['done']??0)/($tasks['total']??0)*100) : 0;
$boqPct     = ($boq['budget']??0) > 0  ? round(($boq['actual']??0)/($boq['budget']??0)*100, 1) : 0;
$certPct    = ($certs['total']??0) > 0  ? round(($certs['paid']??0)/($certs['total']??0)*100, 1) : 0;
$milPct     = ($milestones['total']??0) > 0 ? round(($milestones['done']??0)/($milestones['total']??0)*100) : 0;
?>
<div class="px-4 pt-4 pb-0">

<div class="content-header px-4 pt-4 pb-0">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <a href="<?= site_url("projects/{$project['id']}") ?>" class="text-muted small text-decoration-none">
                <i class="fa-solid fa-arrow-left me-1"></i><?= esc($project['title']) ?>
            </a>
            <h1 class="h4 fw-bold mb-0 mt-1"><i class="fa-solid fa-chart-bar me-2 text-primary"></i>Project Report</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url("projects/{$project['id']}/report/print") ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-print me-1"></i>Print / PDF
            </a>
            <a href="<?= site_url("projects/{$project['id']}") ?>" class="btn btn-sm btn-primary">
                <i class="fa-solid fa-arrow-left me-1"></i>Back to Project
            </a>
        </div>
    </div>
</div>

<div class="content px-4 pt-3 pb-4">

    <!-- Header meta strip -->
    <div class="card border-0 shadow-sm mb-4 px-4 py-3" style="border-radius:12px;">
        <div class="row g-3 text-center">
            <?php
            $statusClr = ['active'=>'success','planning'=>'secondary','on_hold'=>'warning','completed'=>'info','archived'=>'dark'];
            $sc = $statusClr[$project['status']??'']??'secondary';
            ?>
            <div class="col-md-3 border-end">
                <div class="text-muted small">Status</div>
                <div class="fw-semibold mt-1">
                    <span class="badge bg-<?= $sc ?>-subtle text-<?= $sc ?> fs-6"><?= ucfirst($project['status']??'') ?></span>
                </div>
            </div>
            <div class="col-md-3 border-end">
                <div class="text-muted small">Budget</div>
                <div class="fw-bold mt-1 fs-5"><?= $project['budget'] ? '$'.number_format($project['budget'],0) : '—' ?></div>
            </div>
            <div class="col-md-3 border-end">
                <div class="text-muted small">Start → End</div>
                <div class="fw-semibold mt-1 small">
                    <?= $project['start_date'] ? date('d M Y', strtotime($project['start_date'])) : '—' ?>
                    &nbsp;→&nbsp;
                    <?= $project['end_date']   ? date('d M Y', strtotime($project['end_date']))   : '—' ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Report Date</div>
                <div class="fw-semibold mt-1"><?= date('d M Y') ?></div>
            </div>
        </div>
    </div>

    <!-- KPI ring cards row -->
    <div class="row g-3 mb-4">
        <?php $rings = [
            ['Tasks',       $tasks['done']??0, $tasks['total']??0,        $taskPct, 'success',   'fa-list-check'],
            ['Milestones',  $milestones['done']??0, $milestones['total']??0, $milPct, 'info',    'fa-flag'],
            ['BOQ Spent',   '$'.number_format($boq['actual']??0,0), '$'.number_format($boq['budget']??0,0), $boqPct, $boqPct>100?'danger':'warning', 'fa-coins'],
            ['Certs Paid',  '$'.number_format($certs['paid']??0,0), '$'.number_format($certs['total']??0,0), $certPct, 'primary', 'fa-file-invoice-dollar'],
        ]; ?>
        <?php foreach ($rings as [$label,$done,$total,$pct,$col,$icon]): ?>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-4" style="border-radius:12px;">
                <div style="position:relative;width:80px;height:80px;margin:0 auto 8px;">
                    <canvas id="ring_<?= md5($label) ?>" width="80" height="80"></canvas>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:13px;font-weight:700;"><?= $pct ?>%</div>
                </div>
                <i class="fa-solid <?= $icon ?> text-<?= $col ?> mb-1"></i>
                <div class="fw-semibold"><?= $done ?> / <?= $total ?></div>
                <div class="text-muted small"><?= $label ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-3">
        <!-- Task status breakdown -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="fa-solid fa-list-check me-2 text-primary"></i>Task Breakdown</h6>
                    <canvas id="taskDonut" height="200"></canvas>
                    <ul class="list-unstyled small mt-3 mb-0">
                        <?php foreach ([
                            ['Done',        $tasks['done']??0,        'success'],
                            ['In Progress', $tasks['in_progress']??0, 'primary'],
                            ['Todo',        $tasks['todo']??0,        'secondary'],
                            ['Overdue',     $tasks['overdue']??0,     'danger'],
                        ] as [$lbl,$val,$col]): ?>
                        <li class="d-flex justify-content-between border-bottom py-1">
                            <span><span class="badge bg-<?= $col ?> me-1">&nbsp;</span><?= $lbl ?></span>
                            <strong><?= $val ?></strong>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- RFI status breakdown -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="fa-solid fa-circle-question me-2 text-warning"></i>RFI Summary</h6>
                    <canvas id="rfiDonut" height="200"></canvas>
                    <ul class="list-unstyled small mt-3 mb-0">
                        <?php foreach ([
                            ['Open',     ($rfis['open']??0),     'warning'],
                            ['Answered', $rfis['answered']??0,   'info'],
                            ['Closed',   $rfis['closed']??0,     'success'],
                        ] as [$lbl,$val,$col]): ?>
                        <li class="d-flex justify-content-between border-bottom py-1">
                            <span><span class="badge bg-<?= $col ?> me-1">&nbsp;</span><?= $lbl ?></span>
                            <strong><?= $val ?></strong>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="text-muted small mt-2">Total: <strong><?= $rfis['total']??0 ?></strong></div>
                </div>
            </div>
        </div>

        <!-- Punch list summary -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="fa-solid fa-clipboard-check me-2 text-danger"></i>Punch List</h6>
                    <canvas id="punchDonut" height="200"></canvas>
                    <ul class="list-unstyled small mt-3 mb-0">
                        <?php foreach ([
                            ['Open',     $punch['open']??0,     'danger'],
                            ['Resolved', $punch['resolved']??0, 'warning'],
                            ['Closed',   $punch['closed']??0,   'success'],
                        ] as [$lbl,$val,$col]): ?>
                        <li class="d-flex justify-content-between border-bottom py-1">
                            <span><span class="badge bg-<?= $col ?> me-1">&nbsp;</span><?= $lbl ?></span>
                            <strong><?= $val ?></strong>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="text-muted small mt-2">Total: <strong><?= $punch['total']??0 ?></strong> &nbsp;|&nbsp; Diary entries (30d): <strong><?= $diary30 ?></strong></div>
                </div>
            </div>
        </div>
    </div><!-- row -->

    <!-- Cost vs Budget bar -->
    <div class="row g-3 mt-0">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="fa-solid fa-coins me-2 text-warning"></i>Cost vs Budget</h6>
                    <div class="d-flex align-items-center gap-4 mb-2">
                        <?php
                        $budget = (float)($project['budget'] ?? 0);
                        $actual = (float)($boq['actual']    ?? 0);
                        $variance = $budget - $actual;
                        ?>
                        <div class="text-center"><div class="fw-bold fs-5 text-primary">$<?= number_format($budget,0) ?></div><div class="text-muted small">Budget</div></div>
                        <div class="text-center"><div class="fw-bold fs-5 text-<?= $actual>$budget?'danger':'success' ?>">$<?= number_format($actual,0) ?></div><div class="text-muted small">Actual BOQ</div></div>
                        <div class="text-center"><div class="fw-bold fs-5 text-<?= $variance>=0?'success':'danger' ?>"><?= $variance>=0?'+':'' ?>$<?= number_format(abs($variance),0) ?></div><div class="text-muted small">Variance</div></div>
                        <div class="text-center"><div class="fw-bold fs-5 text-primary">$<?= number_format($certs['paid']??0,0) ?></div><div class="text-muted small">Certified Paid</div></div>
                    </div>
                    <div class="progress" style="height:20px;border-radius:8px;">
                        <?php $bpct = $budget>0 ? min(round($actual/$budget*100),100) : 0; ?>
                        <div class="progress-bar <?= $actual>$budget?'bg-danger':'bg-success' ?> fw-semibold" style="width:<?= $bpct ?>%;font-size:12px;">
                            <?= $bpct ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div><!-- .content -->
<?= $this->endSection() ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Ring chart helper
function makeRing(id, pct, color) {
    const ctx = document.getElementById(id);
    if (!ctx) return;
    new Chart(ctx, {
        type:'doughnut',
        data:{ datasets:[{ data:[pct,100-pct], backgroundColor:[color,'#e9ecef'], borderWidth:0 }] },
        options:{ cutout:'72%', plugins:{legend:{display:false},tooltip:{enabled:false}} }
    });
}
<?php foreach ($rings as [$label,$done,$total,$pct,$col,$icon]): ?>
makeRing('ring_<?= md5($label) ?>', <?= $pct ?>, getComputedStyle(document.documentElement).getPropertyValue('--bs-<?= $col ?>') || '#0d6efd');
<?php endforeach; ?>

// Task donut
new Chart(document.getElementById('taskDonut'),{
    type:'doughnut',
    data:{labels:['Done','In Progress','Todo','Overdue'],
        datasets:[{data:[<?= ($tasks['done']??0) ?>,<?= ($tasks['in_progress']??0) ?>,<?= ($tasks['todo']??0) ?>,<?= ($tasks['overdue']??0) ?>],
        backgroundColor:['#198754','#0d6efd','#6c757d','#dc3545'],borderWidth:2}]},
    options:{cutout:'65%',plugins:{legend:{position:'bottom'}}}
});

// RFI donut
new Chart(document.getElementById('rfiDonut'),{
    type:'doughnut',
    data:{labels:['Open','Answered','Closed'],
        datasets:[{data:[<?= ($rfis['open']??0) ?>,<?= ($rfis['answered']??0) ?>,<?= ($rfis['closed']??0) ?>],
        backgroundColor:['#ffc107','#0dcaf0','#198754'],borderWidth:2}]},
    options:{cutout:'65%',plugins:{legend:{position:'bottom'}}}
});

// Punch donut
new Chart(document.getElementById('punchDonut'),{
    type:'doughnut',
    data:{labels:['Open','Resolved','Closed'],
        datasets:[{data:[<?= ($punch['open']??0) ?>,<?= ($punch['resolved']??0) ?>,<?= ($punch['closed']??0) ?>],
        backgroundColor:['#dc3545','#ffc107','#198754'],borderWidth:2}]},
    options:{cutout:'65%',plugins:{legend:{position:'bottom'}}}
});
</script>


