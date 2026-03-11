<?php
$sModel = new \App\Models\SovItemModel();
$pModel = new \App\Models\PayAppModel();
$eModel = new \App\Models\ProjectExpenseModel();

$sovItems = $sModel->forProject($project['id']);
$payApps  = $pModel->forProject($project['id']);
$expenses  = $eModel->forProject($project['id']);

$totalScheduledValue = array_sum(array_column($sovItems, 'scheduled_value'));
$totalExpenses = array_sum(array_column($expenses, 'amount'));
?>

<div class="row g-4">
    <!-- Left Column: Master Schedule of Values (SOV) -->
    <div class="col-lg-7">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Schedule of Values (Contract Breakdown)</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newSovModal">
                <i class="fa-solid fa-plus me-1"></i> Add SOV Line
            </button>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 pe-0" style="width: 80px;">Item No.</th>
                            <th>Description of Work</th>
                            <th class="text-end text-primary" style="width: 150px;">Scheduled Value</th>
                            <th class="text-end pe-3" style="width: 70px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sovItems)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-list-ul fs-3 d-block mb-2"></i>
                                    No SOV items established for this contract yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sovItems as $item): ?>
                                <tr>
                                    <td class="ps-3 pe-0 fw-bold text-muted"><?= esc($item['item_no']) ?></td>
                                    <td class="fw-medium"><?= esc($item['description']) ?></td>
                                    <td class="text-end fw-bold text-dark">$<?= number_format($item['scheduled_value'], 2) ?></td>
                                    <td class="text-end pe-3">
                                        <form action="<?= site_url("projects/{$project['id']}/sov/{$item['id']}/delete") ?>" method="POST" onsubmit="return confirm('Delete this SOV line? Note: Do not delete lines that have progress billed against them.');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($sovItems)): ?>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="2" class="text-end fw-bold">Original Contract Total:</td>
                            <td class="text-end fw-bold fs-5 text-success">$<?= number_format($totalScheduledValue, 2) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Project Expenses Section -->
        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
            <h5 class="fw-bold mb-0">Project Expenses</h5>
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newExpenseModal">
                <i class="fa-solid fa-receipt me-1"></i> Log Expense
            </button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Vendor</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenses)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted small">No expenses logged for this project yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($expenses as $exp): ?>
                                <tr>
                                    <td class="ps-3 small"><?= date('M d, Y', strtotime($exp['expense_date'])) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= esc($exp['category']) ?></span></td>
                                    <td class="small"><?= esc($exp['description']) ?></td>
                                    <td class="small"><?= esc($exp['vendor']) ?></td>
                                    <td class="text-end fw-bold">$<?= number_format($exp['amount'], 2) ?></td>
                                    <td class="text-center">
                                        <?php 
                                            $eStatusClass = 'bg-secondary';
                                            if ($exp['status'] === 'approved') $eStatusClass = 'bg-success';
                                            if ($exp['status'] === 'rejected') $eStatusClass = 'bg-danger';
                                            if ($exp['status'] === 'submitted' || $exp['status'] === 'pending') $eStatusClass = 'bg-primary';
                                        ?>
                                        <span class="badge <?= $eStatusClass ?>"><?= ucfirst($exp['status']) ?></span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <?php if ($exp['receipt_path']): ?>
                                                <a href="<?= site_url($exp['receipt_path']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary border-0"><i class="fa-solid fa-paperclip"></i></a>
                                            <?php endif; ?>
                                            <form action="<?= site_url("finance/expenses/{$exp['id']}/delete") ?>" method="POST" onsubmit="return confirm('Delete this expense?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Payment Applications (Pay Apps) -->
    <div class="col-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Payment Applications</h5>
            <?php if (!empty($sovItems)): ?>
            <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#newPayAppModal">
                <i class="fa-solid fa-file-invoice-dollar me-1"></i> Generate Pay App
            </button>
            <?php endif; ?>
        </div>

        <?php if (empty($sovItems)): ?>
            <div class="alert alert-warning border-0 small">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> You must build the Contract Schedule of Values (SOV) before generating progress invoices.
            </div>
        <?php elseif (empty($payApps)): ?>
            <div class="card border-0 shadow-sm p-4 text-center text-muted">
                <i class="fa-solid fa-receipt fs-3 d-block mb-3"></i>
                No payment applications generated yet.
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush shadow-sm rounded-3">
                <?php foreach ($payApps as $app): ?>
                    <div class="list-group-item p-3 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1">
                                    <a href="<?= site_url("finance/pay-apps/{$app['id']}") ?>" class="text-decoration-none">Application #<?= str_pad($app['application_no'], 3, '0', STR_PAD_LEFT) ?></a>
                                </h6>
                                <div class="small text-muted">Period To: <?= date('M d, Y', strtotime($app['period_to'])) ?></div>
                            </div>
                            <?php 
                                $badgeClass = 'bg-secondary';
                                if ($app['status'] === 'Submitted') $badgeClass = 'bg-primary-subtle text-primary';
                                if ($app['status'] === 'Approved') $badgeClass = 'bg-success-subtle text-success';
                                if ($app['status'] === 'Paid') $badgeClass = 'bg-success';
                                if ($app['status'] === 'Rejected') $badgeClass = 'bg-danger';
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= esc($app['status']) ?></span>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <a href="<?= site_url("finance/pay-apps/{$app['id']}") ?>" class="btn btn-sm btn-outline-primary w-100">
                                Open Worksheet
                            </a>
                            <a href="<?= site_url("finance/pay-apps/{$app['id']}/pdf") ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Export PDF">
                                <i class="fa-solid fa-print"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- New SOV Line Modal -->
<div class="modal fade" id="newSovModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url("projects/{$project['id']}/sov") ?>" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Add Schedule of Values Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-4">
                        <label class="form-label small fw-bold">Item No.</label>
                        <input type="text" name="item_no" class="form-control" placeholder="1, 2, A..." required>
                    </div>
                    <div class="col-8">
                        <label class="form-label small fw-bold">Scheduled Value ($)</label>
                        <input type="number" step="0.01" min="0" name="scheduled_value" class="form-control" placeholder="0.00" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Description of Work</label>
                    <input type="text" name="description" class="form-control" placeholder="e.g. Demolition & Site Grading" required>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Row</button>
            </div>
        </form>
    </div>
</div>

<!-- New Expense Modal -->
<div class="modal fade" id="newExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url("projects/{$project['id']}/finance/expenses") ?>" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Log Project Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Expense Date</label>
                        <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Amount ($)</label>
                        <input type="number" step="0.01" min="0" name="amount" class="form-control" placeholder="0.00" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="Materials">Materials</option>
                        <option value="Equipment Rental">Equipment Rental</option>
                        <option value="Subcontractor">Subcontractor</option>
                        <option value="Permits/Fees">Permits/Fees</option>
                        <option value="Travel">Travel</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Vendor/Supplier</label>
                    <input type="text" name="vendor" class="form-control" placeholder="e.g. Home Depot, Sunbelt Rentals" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="What was purchased?"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Receipt/Invoice (Optional)</label>
                    <input type="file" name="receipt" class="form-control">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="fa-solid fa-save"></i> Submit Expense
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Generate Pay App Modal -->
<div class="modal fade" id="newPayAppModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url("projects/{$project['id']}/pay-apps") ?>" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Generate Payment Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3">This will create a new draft invoice allowing you to bill against your Master SOV lines.</p>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Period To (Date)</label>
                    <input type="date" name="period_to" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Retainage Percentage (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="retainage_percentage" class="form-control" value="10.00" required>
                    <div class="form-text">Standard retention held back on this invoice (e.g. 10%).</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-dark d-flex align-items-center gap-2">
                    <i class="fa-solid fa-file-invoice"></i> Create Draft
                </button>
            </div>
        </form>
    </div>
</div>
