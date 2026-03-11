<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h4 class="mb-0">Equipment & Fleet Management</h4>
        <p class="text-muted mb-0">Track construction vehicles, heavy equipment, and job-site tools.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAssetModal">
            <i class="fa-solid fa-plus"></i> Register Equipment
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Equipment Tag</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Current Job Site / Yard</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($assets)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-truck-ramp-box fa-3x mb-3 text-light"></i><br>
                            No equipment registered in this division.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($assets as $a): ?>
                        <tr>
                            <td class="ps-4"><span class="badge bg-secondary"><?= esc($a['asset_tag']) ?></span></td>
                            <td class="fw-semibold text-dark"><?= esc($a['name']) ?></td>
                            <td><?= esc($a['category']) ?></td>
                            <td>
                                <?php 
                                    $bg = 'bg-success';
                                    if ($a['status'] === 'In Use') $bg = 'bg-primary';
                                    if ($a['status'] === 'Under Maintenance') $bg = 'bg-warning text-dark';
                                    if ($a['status'] === 'Retired') $bg = 'bg-danger';
                                ?>
                                <span class="badge <?= $bg ?>"><?= esc($a['status']) ?></span>
                            </td>
                            <td><?= $a['current_location_project_id'] ? 'Project #' . esc($a['current_location_project_id']) : 'Storage Yard/HQ' ?></td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assignModal-<?= $a['id'] ?>">Dispatch/Transfer</button>
                            </td>
                        </tr>

                        <!-- Assign Modal -->
                        <div class="modal fade" id="assignModal-<?= $a['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="<?= site_url('assets/' . $a['id'] . '/assign') ?>" method="post" class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Dispatch Equipment: <?= esc($a['name']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Destination Project (Enter Project ID)</label>
                                            <input type="number" name="project_id" class="form-control" placeholder="Leave empty to return to Storage Yard">
                                        </div>
                                    </div>
                                    <div class="modal-footer pb-0 border-0">
                                        <button type="submit" class="btn btn-primary w-100 mb-3">Confirm Transfer</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- New Asset Modal -->
<div class="modal fade" id="newAssetModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('assets') ?>" method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Register New Equipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Equipment Tag / VIN</label>
                    <input type="text" name="asset_tag" class="form-control" required placeholder="e.g. TRK-001">
                </div>
                <div class="mb-3">
                    <label class="form-label">Equipment Make/Model</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. CAT Excavator 320">
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="Heavy Machinery">Heavy Machinery</option>
                        <option value="Vehicles">Vehicles</option>
                        <option value="Tools">Tools</option>
                        <option value="IT Equipment">IT Equipment</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Purchase Price</label>
                        <input type="number" step="0.01" name="purchase_price" class="form-control" placeholder="Optional">
                    </div>
                </div>
            </div>
            <div class="modal-footer pb-0 border-0">
                <button type="submit" class="btn btn-primary w-100 mb-3">Register</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
