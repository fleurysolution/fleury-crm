<?php $this->extend('layouts/dashboard'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Automated Bid Leveling</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('projects/'.$project['id']) ?>"><?= esc($project['title']) ?></a></li>
                    <li class="breadcrumb-item active">Bid Leveling</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-download me-1"></i>Export Matrix</button>
            <button class="btn btn-sm btn-primary"><i class="fa-solid fa-file-import me-1"></i>Import Bids (Excel)</button>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small fw-bold text-uppercase mb-1">Budget Target</div>
                <div class="h4 mb-0 fw-bold">$<?= number_format($project['budget'], 2) ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-success">
                <div class="text-muted small fw-bold text-uppercase mb-1">Lowest Qualified Bid</div>
                <div class="h4 mb-0 fw-bold text-success">$<?= number_format($project['budget'] * 0.92, 2) ?></div>
                <div class="small text-muted mt-1">Vendor: ABC Electric (-8%)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small fw-bold text-uppercase mb-1">Variance average</div>
                <div class="h4 mb-0 fw-bold">+2.4%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-warning">
                <div class="text-muted small fw-bold text-uppercase mb-1">Identified Scope Gaps</div>
                <div class="h4 mb-0 fw-bold text-warning">3 Items</div>
                <div class="small text-muted mt-1">Found in 2 of 4 bidders</div>
            </div>
        </div>
    </div>

    <!-- Matrix -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">Bid Comparison Matrix (Live)</h6>
            <div class="badge bg-info-subtle text-info">3 Bidders Analyzed</div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0" style="min-width: 1000px;">
                <thead class="bg-light text-center">
                    <tr>
                        <th rowspan="2" class="align-middle text-start ps-3" style="width: 300px;">Scope of Work / BOQ Item</th>
                        <th rowspan="2" class="align-middle" style="width: 150px;">Target Budget</th>
                        <th colspan="2" class="bg-primary-subtle py-2">Vendor A (Preferred)</th>
                        <th colspan="2" class="bg-secondary-subtle py-2">Vendor B</th>
                        <th colspan="2" class="bg-secondary-subtle py-2">Vendor C</th>
                    </tr>
                    <tr class="small fw-bold">
                        <th class="bg-primary-subtle">Price</th>
                        <th class="bg-primary-subtle">Diff %</th>
                        <th>Price</th>
                        <th>Diff %</th>
                        <th>Price</th>
                        <th>Diff %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $items = [
                        ['code'=>'16.01', 'item'=>'Main Switchgear Installation', 'budget'=>120000, 'v1'=>115000, 'v2'=>125000, 'v3'=>118000],
                        ['code'=>'16.02', 'item'=>'Secondary Transformers', 'budget'=>45000, 'v1'=>42000, 'v2'=>48000, 'v3'=>44000],
                        ['code'=>'16.03', 'item'=>'Emergency Generator Backup', 'budget'=>85000, 'v1'=>92000, 'v2'=>84000, 'v3'=>0], // V3 missed scope
                        ['code'=>'16.04', 'item'=>'UPS System & Batteries', 'budget'=>210000, 'v1'=>205000, 'v2'=>220000, 'v3'=>208000],
                    ];
                    foreach($items as $row): 
                        $gap_v3 = ($row['v3'] == 0);
                    ?>
                    <tr>
                        <td class="ps-3 fw-medium">
                            <span class="text-muted small me-2"><?= $row['code'] ?></span>
                            <?= $row['item'] ?>
                        </td>
                        <td class="text-center fw-bold">$<?= number_format($row['budget']) ?></td>
                        
                        <!-- Vendor A -->
                        <td class="text-center text-success fw-bold">$<?= number_format($row['v1']) ?></td>
                        <td class="text-center small"><?= round((($row['v1']-$row['budget'])/$row['budget'])*100, 1) ?>%</td>
                        
                        <!-- Vendor B -->
                        <td class="text-center">$<?= number_format($row['v2']) ?></td>
                        <td class="text-center small <?= ($row['v2']>$row['budget'])?'text-danger':'' ?>"><?= round((($row['v2']-$row['budget'])/$row['budget'])*100, 1) ?>%</td>
                        
                        <!-- Vendor C -->
                        <td class="text-center <?= $gap_v3 ? 'bg-danger-subtle text-danger' : '' ?>">
                            <?= $gap_v3 ? '<i class="fa-solid fa-triangle-exclamation"></i> MISSING' : '$'.number_format($row['v3']) ?>
                        </td>
                        <td class="text-center small">
                            <?= $gap_v3 ? '—' : round((($row['v3']-$row['budget'])/$row['budget'])*100, 1).'%' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-light fw-bold">
                    <tr>
                        <td class="ps-3 align-middle">PROJECT TOTAL BASE BID</td>
                        <td class="text-center align-middle">$460,000</td>
                        <td class="text-center align-middle text-primary fw-bold" style="font-size: 1.1rem;">$454,000</td>
                        <td class="text-center align-middle small text-success">-1.3%</td>
                        <td class="text-center align-middle">$477,000</td>
                        <td class="text-center align-middle small text-danger">+3.7%</td>
                        <td class="text-center align-middle text-danger">$370,000*</td>
                        <td class="text-center align-middle small text-warning">Scoped Gap</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Automation / AI Flagging -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3"><i class="fa-solid fa-robot me-2 text-primary"></i>AI Insight: Scope Gap Analysis</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex align-items-center gap-3 py-3">
                            <i class="fa-solid fa-circle-exclamation text-danger fs-4"></i>
                            <div>
                                <div class="fw-bold">Missing Emergency Generator (Vendor C)</div>
                                <div class="text-muted">Vendor C's bid is 20% lower than total, but specifically omits item 16.03. Real cost with leveling is $455,000.</div>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center gap-3 py-3">
                            <i class="fa-solid fa-circle-info text-info fs-4"></i>
                            <div>
                                <div class="fw-bold">Potential Over-Estimate (Vendor B)</div>
                                <div class="text-muted">UPS System pricing is 15% above market average for this region. Recommendation: Renegotiate line item 16.04.</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3"><i class="fa-solid fa-envelope-circle-check me-2 text-success"></i>Pre-Qualification & ITB Status</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush small">
                        <?php 
                        $vendors = [
                            ['name'=>'ABC Electric', 'status'=>'Qualified', 'badge'=>'success', 'itb'=>'Accepted'],
                            ['name'=>'Power Systems Inc', 'status'=>'Pending Exp. Ins.', 'badge'=>'warning', 'itb'=>'Accepted'],
                            ['name'=>'Global Gen Group', 'status'=>'Qualified', 'badge'=>'success', 'itb'=>'Reviewing'],
                        ];
                        foreach($vendors as $v):
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <div class="fw-bold"><?= $v['name'] ?></div>
                                <div class="text-muted" style="font-size: 0.7rem;">Last updated 2 days ago</div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-<?= $v['badge'] ?>-subtle text-<?= $v['badge'] ?> mb-1 d-block"><?= $v['status'] ?></span>
                                <span class="text-muted"><?= $v['itb'] ?></span>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-bordered th, .table-bordered td {
        border-color: #f0f0f0 !important;
    }
    .bg-primary-subtle { background-color: #e7f1ff !important; }
</style>
<?php $this->endSection(); ?>
