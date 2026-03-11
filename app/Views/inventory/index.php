<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="row align-items-center mb-4">
    <div class="col-md-5">
        <h4 class="mb-0">Materials Inventory</h4>
        <p class="text-muted mb-0">Manage building materials across yards and job sites.</p>
    </div>
    <div class="col-md-7 text-md-end gap-2 d-flex justify-content-md-end flex-wrap mt-3 mt-md-0">
        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#newLocationModal">
            <i class="fa-solid fa-map-location-dot"></i> Add Yard/Site
        </button>
        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#newItemModal">
            <i class="fa-solid fa-box"></i> New Material
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#transactionModal">
            <i class="fa-solid fa-right-left"></i> Transfer Materials
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">SKU</th>
                        <th>Name</th>
                        <th>Yard/Site</th>
                        <th>Quantity on Hand</th>
                        <th class="pe-4 text-end">UoM</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stocks)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-boxes-stacked fa-3x mb-3 text-light"></i><br>
                            No materials recorded in this division. Time to bring in some pallets/supplies.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($stocks as $s): ?>
                        <tr>
                            <td class="ps-4"><span class="badge bg-secondary"><?= esc($s['sku']) ?></span></td>
                            <td class="fw-semibold text-dark"><?= esc($s['item_name']) ?></td>
                            <td><i class="fa-solid fa-warehouse text-muted me-2"></i><?= esc($s['location_name']) ?></td>
                            <td>
                                <span class="badge <?= $s['quantity'] <= 0 ? 'bg-danger' : 'bg-primary' ?> fs-6">
                                    <?= floatval($s['quantity']) ?>
                                </span>
                            </td>
                            <td class="pe-4 text-end text-muted"><?= esc($s['unit_of_measure']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Transaction Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('inventory/transactions') ?>" method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Material Transfer (In/Out)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Building Material / Supply</label>
                    <select name="item_id" class="form-select" required>
                        <?php foreach($items as $itm): ?>
                            <option value="<?= $itm['id'] ?>"><?= esc($itm['name']) ?> (<?= esc($itm['sku']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Storage Yard / Job Site</label>
                    <select name="location_id" class="form-select" required>
                        <?php foreach($locations as $loc): ?>
                            <option value="<?= $loc['id'] ?>"><?= esc($loc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Transaction Type</label>
                        <select name="transaction_type" class="form-select" required>
                            <option value="In">Receive (In)</option>
                            <option value="Out">Disburse (Out)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" step="0.01" name="quantity" class="form-control" required>
                    </div>
                </div>
                <!-- Optional Project Assignment -->
                <div class="mb-3">
                    <label class="form-label text-muted">Send to Project ID (If Disbursing)</label>
                    <input type="number" name="project_id" class="form-control" placeholder="Optional">
                </div>
            </div>
            <div class="modal-footer pb-0 border-0">
                <button type="submit" class="btn btn-primary w-100 mb-3">Log Movement</button>
            </div>
        </form>
    </div>
</div>

<!-- New Item & Location Modals can be added similar to Transaction -->

<?= $this->endSection() ?>
