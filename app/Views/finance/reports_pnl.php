<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h4 class="mb-0">Profit & Loss (P&L)</h4>
        <p class="text-muted mb-0">Live division financial health overview.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <form action="<?= site_url('reports/financial/pnl') ?>" method="get" class="d-flex justify-content-md-end gap-2 align-items-center mt-3 mt-md-0 flex-wrap">
            <input type="date" name="start_date" class="form-control form-control-sm w-auto" value="<?= esc($startDate ?? '') ?>">
            <span>to</span>
            <input type="date" name="end_date" class="form-control form-control-sm w-auto" value="<?= esc($endDate ?? '') ?>">
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Summary Cards -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white h-100">
            <div class="card-body text-center py-4">
                <i class="fa-solid fa-arrow-trend-up fa-2x mb-2 text-white-50"></i>
                <h6 class="text-white-50 text-uppercase mb-2">Total Income</h6>
                <h3 class="mb-0 fw-bold">$<?= number_format($pnlData['revenue']['invoiced_income'], 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-danger text-white h-100">
            <div class="card-body text-center py-4">
                <i class="fa-solid fa-arrow-trend-down fa-2x mb-2 text-white-50"></i>
                <h6 class="text-white-50 text-uppercase mb-2">Total Costs</h6>
                <h3 class="mb-0 fw-bold">$<?= number_format($pnlData['costs']['total_costs'], 2) ?></h3>
                <small class="text-white-50">Includes Expenses & Payroll</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <?php $isProfitable = $pnlData['profitability']['net_profit'] >= 0; ?>
        <div class="card border-0 shadow-sm <?= $isProfitable ? 'bg-primary' : 'bg-warning text-dark' ?> h-100">
            <div class="card-body text-center py-4 <?= $isProfitable ? 'text-white' : '' ?>">
                <i class="fa-solid fa-scale-balanced fa-2x mb-2 <?= $isProfitable ? 'text-white-50' : 'text-dark text-opacity-50' ?>"></i>
                <h6 class="<?= $isProfitable ? 'text-white-50' : 'text-dark text-opacity-75' ?> text-uppercase mb-2">Net Profit</h6>
                <h3 class="mb-0 fw-bold">$<?= number_format($pnlData['profitability']['net_profit'], 2) ?></h3>
                <small class="<?= $isProfitable ? 'text-white-50' : 'text-dark fw-bold' ?>">Margin: <?= $pnlData['profitability']['margin_pct'] ?>%</small>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h5 class="mb-4 fw-bold">Financial Breakdown</h5>
        
        <!-- Revenue -->
        <h6 class="text-muted text-uppercase mb-3 border-bottom pb-2"><i class="fa-solid fa-money-bill-wave me-2"></i> Revenue Pipeline</h6>
        <div class="d-flex justify-content-between mb-2">
            <span class="ps-4">Invoiced Field Income</span>
            <span class="text-success fw-bold">+$<?= number_format($pnlData['revenue']['invoiced_income'], 2) ?></span>
        </div>

        <!-- Costs -->
        <h6 class="text-muted text-uppercase mt-5 mb-3 border-bottom pb-2"><i class="fa-solid fa-coins me-2"></i> Costs & Expenses</h6>
        <div class="d-flex justify-content-between mb-2">
            <span class="ps-4">Direct Project Expenses & Subcontractor Payables</span>
            <span class="text-danger">-$<?= number_format($pnlData['costs']['project_expenses'], 2) ?></span>
        </div>
        <div class="d-flex justify-content-between mb-3">
            <span class="ps-4">Crew Payroll & Labor Wages (Gross Liability)</span>
            <span class="text-danger">-$<?= number_format($pnlData['costs']['payroll_expenses'], 2) ?></span>
        </div>

        <!-- Totals -->
        <div class="d-flex justify-content-between mt-5 border-top pt-4">
            <span class="fs-5 fw-bold text-uppercase">Gross Operating Profit</span>
            <span class="fs-4 fw-bold <?= $pnlData['profitability']['gross_profit'] >= 0 ? 'text-success' : 'text-danger' ?>">
                $<?= number_format($pnlData['profitability']['gross_profit'], 2) ?>
            </span>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
