<div class="production-control-dashboard p-1">
    
    <!-- Top KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:15px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white;">
                <div class="card-body p-4 text-center">
                    <div class="small opacity-75 mb-1">Cost Performance Index (CPI)</div>
                    <h2 class="fw-bold mb-0"><?= number_format($control_metrics['performance']['cpi'], 2) ?></h2>
                    <div class="small mt-2">
                        <?php if($control_metrics['performance']['cpi'] >= 1): ?>
                            <span class="badge bg-success bg-opacity-25 text-white border border-white border-opacity-25 px-2">UNDER BUDGET</span>
                        <?php else: ?>
                            <span class="badge bg-danger bg-opacity-25 text-white border border-white border-opacity-25 px-2">OVER BUDGET</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:15px; border-left: 5px solid #10b981 !important;">
                <div class="card-body p-4">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Earned Value (EV)</div>
                    <h3 class="fw-bold mb-0"><?= esc($project['currency'] ?? 'USD') ?> <?= number_format($control_metrics['performance']['earned_value'], 2) ?></h3>
                    <p class="text-muted small mb-0 mt-2">Value of work completed to date</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:15px; border-left: 5px solid #8b5cf6 !important;">
                <div class="card-body p-4">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Labor Efficiency</div>
                    <h3 class="fw-bold mb-0"><?= number_format($control_metrics['labor']['productivity_factor'], 2) ?></h3>
                    <p class="text-muted small mb-0 mt-2">Units per hour performance</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:15px; border-left: 5px solid #f59e0b !important;">
                <div class="card-body p-4">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Actual Cost (Labor)</div>
                    <h3 class="fw-bold mb-0"><?= esc($project['currency'] ?? 'USD') ?> <?= number_format($control_metrics['labor']['total_cost'], 2) ?></h3>
                    <p class="text-muted small mb-0 mt-2">Total investment in man-hours</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Labor Breakdown -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm" style="border-radius:15px;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-dark">Labor Cost & Performance Breakdown</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 small text-uppercase">Resource / User</th>
                                    <th class="py-3 small text-uppercase text-center">Hours</th>
                                    <th class="py-3 small text-uppercase text-center">Rate</th>
                                    <th class="py-3 small text-uppercase text-end pe-4">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($control_metrics['labor']['breakdown'])): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted small">No labor data recorded via timesheets yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($control_metrics['labor']['breakdown'] as $l): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width:32px; height:32px;">
                                                    <?= substr($l['user_name'], 0, 1) ?>
                                                </div>
                                                <div class="fw-semibold"><?= esc($l['user_name']) ?></div>
                                            </div>
                                        </td>
                                        <td class="text-center"><?= number_format($l['total_hours'], 1) ?>h</td>
                                        <td class="text-center text-muted small"><?= esc($project['currency']) ?> <?= number_format($l['hourly_rate'], 2) ?>/h</td>
                                        <td class="text-end pe-4 fw-bold">
                                            <?= esc($project['currency']) ?> <?= number_format($l['total_cost'], 2) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Summary -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm" style="border-radius:15px;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-dark">Production Insights</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="p-3 bg-light rounded-4 mb-3 border border-dashed border-primary border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-bold text-primary text-uppercase">Overall Completion</span>
                            <span class="h5 fw-bold mb-0 text-primary"><?= number_format($stats['percent'], 1) ?>%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 5px;">
                            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: <?= $stats['percent'] ?>%"></div>
                        </div>
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-0">
                            <div>
                                <h6 class="mb-0 fw-bold">Self-Performed Items</h6>
                                <p class="text-muted small mb-0">Tracked via Site Diary</p>
                            </div>
                            <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill"><?= count($control_metrics['performance']['production_items']) ?> Linked</span>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-0 border-top">
                            <div>
                                <h6 class="mb-0 fw-bold">Project Profitability Factor</h6>
                                <p class="text-muted small mb-0">Based on Labor vs BOQ Value</p>
                            </div>
                            <?php 
                                $pf = $control_metrics['performance']['earned_value'] > 0 
                                    ? $control_metrics['performance']['earned_value'] / max(1, $control_metrics['labor']['total_cost']) 
                                    : 0;
                            ?>
                            <span class="fw-bold text-<?= $pf >= 1 ? 'success' : 'danger' ?>"><?= number_format($pf, 2) ?>x</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Guardrails Alert -->
            <?php if($control_metrics['performance']['cpi'] < 0.9): ?>
            <div class="alert alert-danger border-0 shadow-sm mt-4 p-4" style="border-radius:15px;">
                <div class="d-flex gap-3">
                    <i class="fa-solid fa-triangle-exclamation fa-2x mt-1"></i>
                    <div>
                        <h6 class="fw-bold alert-heading">Critical Performance Alert</h6>
                        <p class="mb-0 small">The current Cost Performance Index (CPI) of <strong><?= number_format($control_metrics['performance']['cpi'], 2) ?></strong> indicates the project is significantly over budget relative to work progress. Immediate review of labor productivity or scope creep is required.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Production Tracker Table -->
    <div class="card border-0 shadow-sm mt-4 mb-5" style="border-radius:15px;">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">Granular Production Tracker</h5>
                <p class="text-muted small mb-0">Detailed breakdown of work items linked to Bill of Quantities</p>
            </div>
            <button class="btn btn-primary btn-sm px-3 rounded-pill">Export Report</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 small text-uppercase">Code</th>
                            <th class="py-3 small text-uppercase">Item Description</th>
                            <th class="py-3 small text-uppercase text-center">Unit</th>
                            <th class="py-3 small text-uppercase text-center">Budget Qty</th>
                            <th class="py-3 small text-uppercase text-center">Actual Qty</th>
                            <th class="py-3 small text-uppercase text-center">Progress</th>
                            <th class="py-3 small text-uppercase text-end pe-4">Earned Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($control_metrics['performance']['production_items'])): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted small">No BOQ items linked to field production yet. Approve site diaries to see data here.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($control_metrics['performance']['production_items'] as $item): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-primary small text-uppercase"><?= esc($item['item_code']) ?></td>
                                <td><?= esc(substr($item['description'], 0, 50)) ?>...</td>
                                <td class="text-center"><span class="badge bg-secondary-subtle text-secondary"><?= esc($item['unit']) ?></span></td>
                                <td class="text-center fw-semibold"><?= number_format($item['qty'], 2) ?></td>
                                <td class="text-center fw-bold text-dark"><?= number_format($item['actual_qty'] ?? 0, 2) ?></td>
                                <td class="text-center">
                                    <?php 
                                        $prog = $item['qty'] > 0 ? ($item['actual_qty'] / $item['qty']) * 100 : 0;
                                        $barColor = $prog >= 100 ? 'success' : ($prog >= 50 ? 'primary' : 'warning');
                                    ?>
                                    <div class="progress mx-auto" style="height: 6px; width: 60px; border-radius: 3px;">
                                        <div class="progress-bar bg-<?= $barColor ?>" style="width: <?= min(100, $prog) ?>%"></div>
                                    </div>
                                    <span class="small text-muted" style="font-size: 0.65rem;"><?= number_format($prog, 1) ?>%</span>
                                </td>
                                <td class="text-end pe-4 fw-bold">
                                    <?= esc($project['currency']) ?> <?= number_format(($item['actual_qty'] ?? 0) * $item['unit_rate'], 2) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.production-control-dashboard .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.production-control-dashboard .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
}
.production-control-dashboard .avatar-sm {
    font-size: 0.8rem;
}
</style>
