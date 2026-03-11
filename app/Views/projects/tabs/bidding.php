<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Subcontractor Bidding Packages</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPackageModal">
                    <i class="fa-solid fa-file-signature me-1"></i> Create Bid Package
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Package Title</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Bids Recv</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bid_packages)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">No active tender packages.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($bid_packages as $pkg): ?>
                            <tr>
                                <td class="ps-4 fw-semibold text-primary"><?= esc($pkg['title']) ?></td>
                                <td>
                                    <small><?= $pkg['due_date'] ? date('M d, Y', strtotime($pkg['due_date'])) : 'Open' ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $pkg['status'] === 'open' ? 'success' : 'secondary' ?>"><?= ucfirst($pkg['status']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= count($bids_per_package[$pkg['id']] ?? []) ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewTender(<?= $pkg['id'] ?>)">
                                        <i class="fa-solid fa-list-check me-1"></i> Leveling
                                    </button>
                                    <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#submitBidModal<?= $pkg['id'] ?>">
                                        <i class="fa-solid fa-plus"></i> Bid
                                    </button>
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

<!-- Modal: Create Package -->
<div class="modal fade" id="addPackageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">New Bid Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url("bidding/storePackage/{$project['id']}") ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label">Package Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Electrical & Low Voltage">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Scope Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary px-4">Publish Package</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Leveling Modals (One for each pkg) -->
<?php foreach ($bid_packages as $pkg): ?>
<div class="modal fade" id="submitBidModal<?= $pkg['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Simulate Vendor Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url("bidding/submitBid/{$pkg['id']}") ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body py-4">
                    <p class="small text-muted mb-3">Submitting bid for: <strong><?= esc($pkg['title']) ?></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Vendor Name</label>
                        <input type="text" name="vendor_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bid Amount</label>
                        <input type="number" name="amount" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-dark px-4">Submit Bid</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="levelingModal<?= $pkg['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Bid Leveling: <?= esc($pkg['title']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4">
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr class="bg-light">
                                <th>Vendor</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $bids = $bids_per_package[$pkg['id']] ?? [];
                                foreach ($bids as $b): 
                            ?>
                            <tr class="<?= $b['status'] === 'awarded' ? 'table-success' : '' ?>">
                                <td>
                                    <div class="fw-bold"><?= esc($b['vendor_name']) ?></div>
                                    <small class="text-muted"><?= esc($b['notes']) ?></small>
                                </td>
                                <td><strong><?= number_format($b['amount'], 2) ?></strong></td>
                                <td><span class="badge bg-<?= $b['status'] === 'submitted' ? 'info' : ($b['status'] === 'awarded' ? 'success' : 'danger') ?>"><?= $b['status'] ?></span></td>
                                <td class="text-end">
                                    <?php if ($pkg['status'] === 'open' && $b['status'] === 'submitted'): ?>
                                    <a href="<?= site_url("bidding/award/{$b['id']}") ?>" class="btn btn-xs btn-success text-white px-2">Award</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; if (empty($bids)): ?>
                            <tr><td colspan="4" class="text-center py-4 text-muted small">No bids received yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
function viewTender(id) {
    const modal = new bootstrap.Modal(document.getElementById('levelingModal' + id));
    modal.show();
}
</script>
