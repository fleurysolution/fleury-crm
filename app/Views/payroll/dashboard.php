<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h4 class="mb-0">Crew Payroll Dashboard</h4>
        <p class="text-muted mb-0">Manage crew wages, tax brackets, and distribute pay runs.</p>
    </div>
    <div class="col-md-6 text-md-end gap-2 d-flex justify-content-md-end flex-wrap mt-3 mt-md-0">
        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#taxProfileModal">
            <i class="fa-solid fa-percent"></i> Tax Profiles
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateRunModal">
            <i class="fa-solid fa-gears"></i> Run Payroll
        </button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h6 class="mb-0 fw-bold">Recent Pay Runs (Division Ledger)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive mt-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Run ID</th>
                                <th>Period Start</th>
                                <th>Period End</th>
                                <th>Status</th>
                                <th>Total Gross</th>
                                <th>Total Net</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($runs)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-money-check-dollar fa-3x mb-3 text-light"></i><br>
                                    No crew payroll runs have been generated yet.
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($runs as $r): ?>
                                <tr>
                                    <td class="ps-4"><span class="badge bg-secondary">PR-<?= str_pad($r['id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                                    <td><?= esc($r['pay_period_start']) ?></td>
                                    <td><?= esc($r['pay_period_end']) ?></td>
                                    <td>
                                        <?php if ($r['status'] === 'Draft'): ?>
                                            <span class="badge bg-warning text-dark">Draft pending review</span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><i class="fa-solid fa-check"></i> <?= esc($r['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>$<?= number_format((float)$r['total_gross'], 2) ?></td>
                                    <td>$<?= number_format((float)$r['total_net'], 2) ?></td>
                                    <td class="pe-4 text-end">
                                        <?php if ($r['status'] === 'Draft'): ?>
                                            <form action="<?= site_url('payroll/runs/' . $r['id'] . '/approve') ?>" method="post" class="d-inline">
                                                <button type="submit" class="btn btn-sm btn-outline-success">Approve & Issue</button>
                                            </form>
                                        <?php endif; ?>
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
</div>

<!-- Run Payroll Modal -->
<div class="modal fade" id="generateRunModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('payroll/runs/generate') ?>" method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate New Pay Run</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    The Payroll Engine will automatically sweep all <strong>approved timesheets</strong> within the date range that have not yet been processed.
                </p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Period Start</label>
                        <input type="date" name="period_start" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Period End</label>
                        <input type="date" name="period_end" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer pb-0 border-0">
                <button type="submit" class="btn btn-primary w-100 mb-3"><i class="fa-solid fa-gears me-2"></i> Trigger Payroll Engine</button>
            </div>
        </form>
    </div>
</div>

<!-- Tax Profile Mapping Modal -->
<div class="modal fade" id="taxProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('payroll/tax-profiles') ?>" method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configure Tax Bracket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Profile Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Standard Local Tier" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tax Rate / Withholding (%)</label>
                        <input type="number" step="0.01" name="tax_rate" class="form-control" placeholder="e.g. 15.00" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Region/Jurisdiction Code</label>
                        <input type="text" name="region_code" class="form-control" placeholder="e.g. US-CA">
                    </div>
                </div>
            </div>
            <div class="modal-footer pb-0 border-0">
                <button type="submit" class="btn btn-secondary w-100 mb-3">Save Profile</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
