<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<div class="px-4 pt-4 pb-0">
<div class="content-header px-4 pt-4 pb-0">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h1 class="h4 fw-bold mb-0"><i class="fa-solid fa-chart-column me-2 text-primary"></i>Executive Dashboard</h1>
            <p class="text-muted small mb-0 mt-1">Cross-project KPIs &amp; progress — live</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                <i class="fa-solid fa-rotate me-1"></i>Refresh
            </button>
            <a href="<?= site_url('projects') ?>" class="btn btn-sm btn-primary">
                <i class="fa-solid fa-folder-open me-1"></i>All Projects
            </a>
        </div>
    </div>
</div>

<div class="content px-4 pt-3 pb-4">

    <!-- Global KPI cards -->
    <?php
    $t      = $totals ?? [];
    $tk     = (isset($taskKpi) && !empty($taskKpi)) ? $taskKpi : ['total' => 0, 'done' => 0, 'overdue' => 0];
    if (!isset($tk['total'])) $tk['total'] = 0;
    if (!isset($tk['done']))  $tk['done']  = 0;
    $donePct = $tk['total'] > 0 ? round($tk['done'] / $tk['total'] * 100) : 0;
    ?>
    <div class="row g-3 mb-4">
        <?php $kpiCards = [
            ['Total Projects',      $t['total_projects']??0,      'primary', 'fa-folder-open',   ''],
            ['Active',              $t['active']??0,               'success', 'fa-play-circle',   ''],
            ['On Hold',             $t['on_hold']??0,              'warning', 'fa-pause-circle',  ''],
            ['Tasks Done',          ($tk['done']??0).' / '.($tk['total']??0), 'info', 'fa-list-check', $donePct.'% complete'],
            ['Overdue Tasks',       $tk['overdue']??0,             'danger',  'fa-triangle-exclamation', ''],
            ['Budget Portfolio',    '$'.number_format($t['total_budget']??0,0), 'dark', 'fa-coins', ''],
        ]; ?>
        <?php foreach ($kpiCards as [$label,$value,$color,$icon,$sub]): ?>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100 text-center py-3 px-2" style="border-radius:12px;border-top:3px solid var(--bs-<?= $color ?>) !important;">
                <i class="fa-solid <?= $icon ?> fa-lg text-<?= $color ?> mb-2"></i>
                <div class="fw-bold fs-5"><?= $value ?></div>
                <div class="text-muted small"><?= $label ?></div>
                <?php if ($sub): ?><div class="text-<?= $color ?> small mt-1"><?= $sub ?></div><?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Task completion donut + project list side by side -->
    <div class="row g-3 mb-4">
        <!-- Donut chart -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Overall Task Status</h6>
                    <canvas id="taskDonut" height="200"></canvas>
                    <div class="mt-3">
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar bg-success" style="width:<?= $donePct ?>%"></div>
                        </div>
                        <div class="small text-muted mt-1 text-center"><?= $donePct ?>% of all tasks completed</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-body p-0">
                    <div class="px-3 pt-3 pb-2 d-flex align-items-center justify-content-between">
                        <h6 class="fw-semibold mb-0">Active Projects</h6>
                        <a href="<?= site_url('projects') ?>" class="btn btn-xs btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                    <table class="table table-hover small align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Project</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th class="text-end">Budget</th>
                                <th class="text-end">Actual</th>
                                <th class="text-center">RFIs</th>
                                <th class="text-center">Punch</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($projects as $p):
                            $pct    = $p['tasks_total'] > 0 ? round($p['tasks_done']/$p['tasks_total']*100) : 0;
                            $budget = (float)($p['budget'] ?? 0);
                            $actual = (float)($p['cost_actual'] ?? 0);
                            $overBudget = $budget > 0 && $actual > $budget;
                            $statusClr  = ['active'=>'success','on_hold'=>'warning','planning'=>'secondary','completed'=>'info'];
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rounded-circle d-inline-block flex-shrink-0" style="width:8px;height:8px;background:<?= esc($p['color']??'#6c757d') ?>;"></span>
                                    <a href="<?= site_url("projects/{$p['id']}") ?>" class="fw-semibold text-decoration-none text-dark">
                                        <?= esc($p['title']) ?>
                                    </a>
                                </div>
                            </td>
                            <td><span class="badge bg-<?= $statusClr[$p['status']]??'secondary' ?>-subtle text-<?= $statusClr[$p['status']]??'secondary' ?>"><?= ucfirst($p['status']) ?></span></td>
                            <td style="min-width:90px;">
                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
                                </div>
                                <div class="text-muted" style="font-size:10px;"><?= $pct ?>%</div>
                            </td>
                            <td class="text-end"><?= $budget ? '$'.number_format($budget,0) : '—' ?></td>
                            <td class="text-end <?= $overBudget?'text-danger fw-semibold':'' ?>">
                                <?= $actual ? '$'.number_format($actual,0) : '—' ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $p['rfis_open']>0?'bg-warning text-dark':'bg-secondary' ?>">
                                    <?= (int)$p['rfis_open'] ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $p['punch_open']>0?'bg-danger':'bg-secondary' ?>">
                                    <?= (int)$p['punch_open'] ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= site_url("projects/{$p['id']}/report") ?>" class="btn btn-xs btn-outline-secondary" title="Project Report">
                                    <i class="fa-solid fa-chart-bar"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($projects)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">No active projects.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bar chart: RFIs open & Punch open per project -->
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="fa-solid fa-circle-question me-2 text-warning"></i>Open RFIs by Project</h6>
                    <canvas id="rfiBar" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="fa-solid fa-clipboard-check me-2 text-danger"></i>Open Punch Items by Project</h6>
                    <canvas id="punchBar" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

</div><!-- .content -->
<?= $this->endSection() ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const projectLabels = <?= json_encode(array_column($projects, 'title')) ?>;
const rfiData   = <?= json_encode(array_column($projects, 'rfis_open')) ?>;
const punchData = <?= json_encode(array_column($projects, 'punch_open')) ?>;
const tasksDone = <?= json_encode(array_column($projects, 'tasks_done')) ?>;
const tasksAll  = <?= json_encode(array_column($projects, 'tasks_total')) ?>;

// Task donut
new Chart(document.getElementById('taskDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Done', 'In Progress', 'Todo'],
        datasets: [{
            data: [<?= $taskKpi['done']??0 ?>, <?= ($taskKpi['total']??0) - ($taskKpi['done']??0) - ($taskKpi['overdue']??0) ?>, <?= $taskKpi['overdue']??0 ?>],
            backgroundColor: ['#198754','#0d6efd','#ffc107'],
            borderWidth: 2,
        }]
    },
    options: { cutout:'70%', plugins:{ legend:{ position:'bottom' } } }
});

// RFI bar
new Chart(document.getElementById('rfiBar'), {
    type: 'bar',
    data: { labels: projectLabels, datasets:[{
        label:'Open RFIs',
        data: rfiData,
        backgroundColor:'rgba(255,193,7,0.7)',
        borderRadius:4,
    }]},
    options: { indexAxis:'y', plugins:{legend:{display:false}}, scales:{x:{beginAtZero:true,ticks:{stepSize:1}}} }
});

// Punch bar
new Chart(document.getElementById('punchBar'), {
    type: 'bar',
    data: { labels: projectLabels, datasets:[{
        label:'Open Punch Items',
        data: punchData,
        backgroundColor:'rgba(220,53,69,0.7)',
        borderRadius:4,
    }]},
    options: { indexAxis:'y', plugins:{legend:{display:false}}, scales:{x:{beginAtZero:true,ticks:{stepSize:1}}} }
});
</script>


