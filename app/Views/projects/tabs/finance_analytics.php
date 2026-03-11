<div class="row g-4">
    <!-- Financial Overview Cards -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white" style="border-radius:12px;">
            <div class="card-body">
                <div class="small opacity-75">Revised Budget</div>
                <h3 class="fw-bold mb-0"><?= number_to_currency($budget['revised_budget'], $budget['currency']) ?></h3>
                <div class="smallest mt-2">Orig: <?= number_to_currency($budget['original_budget'], $budget['currency']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white" style="border-radius:12px;">
            <div class="card-body">
                <div class="small text-muted">Actual Spend</div>
                <h3 class="fw-bold mb-0 text-dark"><?= number_to_currency($budget['actual_spend'], $budget['currency']) ?></h3>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-<?= $budget['percent_spent'] > 100 ? 'danger' : 'success' ?>" style="width: <?= min(100, $budget['percent_spent']) ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-white" style="border-radius:12px;">
            <div class="card-body">
                <div class="small text-muted">Committed (POs)</div>
                <h3 class="fw-bold mb-0 text-dark"><?= number_to_currency($budget['committed'], $budget['currency']) ?></h3>
                <div class="smallest text-muted mt-2">Approved Purchase Orders</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-<?= $budget['variance'] < 0 ? 'danger-subtle' : 'success-subtle' ?>" style="border-radius:12px;">
            <div class="card-body">
                <div class="small text-muted">Variance</div>
                <h3 class="fw-bold mb-0 text-<?= $budget['variance'] < 0 ? 'danger' : 'success' ?>"><?= number_to_currency($budget['variance'], $budget['currency']) ?></h3>
                <div class="smallest mt-2"><?= $budget['percent_spent'] ?>% of budget used</div>
            </div>
        </div>
    </div>

    <!-- Charts / Breakdowns placeholder -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0">Budget vs Actual Trend</h6>
            </div>
            <div class="card-body text-center py-5">
                <i class="fa-solid fa-chart-line fa-3x text-light mb-3"></i>
                <p class="text-muted small">Financial trend visualization will appear here once more data is logged.</p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0">Change Orders Impact</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-muted">Approved COs</span>
                    <span class="small fw-bold text-success">+ <?= number_to_currency($budget['approved_cos'], $budget['currency']) ?></span>
                </div>
                <hr>
                <div class="text-muted small">
                    Change orders have increased your original budget by <?= $budget['original_budget'] > 0 ? round(($budget['approved_cos'] / $budget['original_budget']) * 100, 1) : 0 ?>%.
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Schedule of Values (Budget Breakdown) -->
    <div class="col-12 mt-4">
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Schedule of Values (Budget Breakdown)</h6>
                <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#addBudgetItemModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Line Item
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="smallest text-muted text-uppercase">
                                <th class="ps-4">Cost Code</th>
                                <th>Item Description</th>
                                <th class="text-end">Qty</th>
                                <th>Unit</th>
                                <th class="text-end">Unit Cost</th>
                                <th class="text-end">Total Price</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($budget_items)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted small">No detailed budget line-items defined. Sum uses flat project budget.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($budget_items as $item): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark border small fw-normal">
                                        <?= esc($item['cost_code'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold small"><?= esc($item['title']) ?></div>
                                    <div class="smallest text-muted"><?= esc($item['description']) ?></div>
                                </td>
                                <td class="text-end small"><?= number_format($item['quantity'], 2) ?></td>
                                <td class="small"><?= esc($item['unit']) ?></td>
                                <td class="text-end small"><?= number_format($item['unit_cost'], 2) ?></td>
                                <td class="text-end fw-bold small"><?= number_format($item['total_cost'], 2) ?></td>
                                <td class="text-end pe-4">
                                    <form action="<?= site_url("budget-items/delete/{$item['id']}") ?>" method="post" onsubmit="return confirm('Remove this line item?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-link btn-sm text-danger p-0">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="table-light">
                                <td colspan="5" class="ps-4 fw-bold text-end small">Total SOV Value:</td>
                                <td class="text-end fw-bold text-primary"><?= number_to_currency($budget['original_budget'], $budget['currency']) ?></td>
                                <td class="pe-4"></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Budget Item -->
<div class="modal fade" id="addBudgetItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius:15px;">
            <div class="modal-header border-0 pb-0 ps-4">
                <h5 class="modal-title fw-bold">Add Budget Line Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url("budget-items/store/{$project['id']}") ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">Cost Code</label>
                            <select name="cost_code_id" class="form-select select2-modal">
                                <option value="">— Select Cost Code —</option>
                                <?php foreach ($cost_codes as $cc): ?>
                                <option value="<?= $cc['id'] ?>"><?= esc($cc['code']) ?> - <?= esc($cc['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Item Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. Concrete Pouring">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Quantity</label>
                            <input type="number" name="quantity" class="form-control" step="0.01" value="1.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Unit</label>
                            <input type="text" name="unit" class="form-control" placeholder="LS, LF, SF...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">UnitPrice</label>
                            <input type="number" name="unit_cost" class="form-control" step="0.01" value="0.00" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pe-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark px-4">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
