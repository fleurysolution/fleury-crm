<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="mb-3">
    <a href="<?= site_url("projects/{$project['id']}?tab=estimates") ?>" class="text-decoration-none text-muted">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Project Estimates
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><?= esc($estimate['title']) ?></h3>
        <span class="badge bg-<?= $estimate['status']==='Approved'?'success':($estimate['status']==='Draft'?'secondary':'warning') ?>-subtle text-<?= $estimate['status']==='Approved'?'success':($estimate['status']==='Draft'?'secondary':'warning') ?>">
            <?= esc($estimate['status']) ?>
        </span>
    </div>
    <div>
        <h3 class="fw-bold text-success mb-0">$<?= number_format($estimate['total_amount'], 2) ?></h3>
        <div class="text-muted small text-end">Total Estimated Cost</div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
        <h5 class="fw-bold mb-0">Line Items</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newItemModal">
            <i class="fa-solid fa-plus me-1"></i> Add Item
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3 pe-0" style="width: 120px;">Cost Code</th>
                    <th>Description</th>
                    <th class="text-end">Quantity</th>
                    <th>Unit</th>
                    <th class="text-end">Unit Cost</th>
                    <th class="text-end text-primary">Total</th>
                    <th class="text-end pe-3" style="width:80px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($items)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-list-check fs-3 d-block mb-3"></i>
                            No items added to this estimate yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($items as $item): ?>
                    <tr>
                        <td class="ps-3 pe-0 text-muted small fw-semibold"><?= esc($item['cost_code']) ?></td>
                        <td class="fw-medium"><?= esc($item['description']) ?></td>
                        <td class="text-end"><?= number_format($item['quantity'], 2) ?></td>
                        <td class="text-muted small"><?= esc($item['unit']) ?></td>
                        <td class="text-end">$<?= number_format($item['unit_cost'], 2) ?></td>
                        <td class="text-end text-primary fw-bold">$<?= number_format($item['total_cost'], 2) ?></td>
                        <td class="text-end pe-3">
                            <form action="<?= site_url("estimates/{$estimate['id']}/items/{$item['id']}/delete") ?>" method="POST" onsubmit="return confirm('Remove this line item?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <?php if(!empty($items)): ?>
            <tfoot class="bg-light">
                <tr>
                    <td colspan="5" class="text-end fw-bold">Grand Total:</td>
                    <td class="text-end text-success fw-bold fs-5">$<?= number_format($estimate['total_amount'], 2) ?></td>
                    <td></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="newItemModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url("estimates/{$estimate['id']}/items") ?>" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Add Line Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Cost Code <span class="text-muted fw-normal">(Optional)</span></label>
                    <input type="text" name="cost_code" class="form-control" placeholder="e.g. 03-3000">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Description</label>
                    <input type="text" name="description" class="form-control" placeholder="e.g. Concrete Slab Pour" required>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-8">
                        <label class="form-label small fw-bold">Quantity</label>
                        <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" value="1.00" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label small fw-bold">Unit</label>
                        <select name="unit" class="form-select">
                            <option value="LS">LS</option>
                            <option value="Ea">Ea</option>
                            <option value="Hr">Hr</option>
                            <option value="SqFt">SqFt</option>
                            <option value="CuYd">CuYd</option>
                            <option value="LnFt">LnFt</option>
                            <option value="Day">Day</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Unit Cost ($)</label>
                    <input type="number" step="0.01" min="0" name="unit_cost" class="form-control" placeholder="0.00" required>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Add Item</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
