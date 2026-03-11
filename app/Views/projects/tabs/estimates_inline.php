<?php
$eModel = new \App\Models\ProjectEstimateModel();
$bModel = new \App\Models\ProjectBidModel();

$estimates = $eModel->forProject($project['id']);
$bids      = $bModel->forProject($project['id']);

// Group Bids by Trade Package for Bid Leveling
$groupedBids = [];
foreach ($bids as $b) {
    if (!isset($groupedBids[$b['trade_package']])) {
        $groupedBids[$b['trade_package']] = [];
    }
    $groupedBids[$b['trade_package']][] = $b;
}
?>

<div class="row g-4">
    <!-- Left Column: Master Project Estimates -->
    <div class="col-lg-6">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Project Estimates (Budgets)</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newEstimateModal">
                <i class="fa-solid fa-plus me-1"></i> New Estimate
            </button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="list-group list-group-flush">
                <?php if (empty($estimates)): ?>
                    <div class="list-group-item text-center py-4 text-muted">
                        <i class="fa-solid fa-calculator fs-3 d-block mb-2"></i>
                        No estimates created yet.
                    </div>
                <?php else: ?>
                    <?php foreach ($estimates as $est): ?>
                        <div class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold mb-1">
                                        <a href="<?= site_url("estimates/{$est['id']}") ?>" class="text-decoration-none"><?= esc($est['title']) ?></a>
                                    </h6>
                                    <div class="small text-muted">Created <?= date('M d, Y', strtotime($est['created_at'])) ?> by <?= esc($est['creator_name']) ?></div>
                                </div>
                                <span class="badge bg-<?= $est['status']==='Approved'?'success':($est['status']==='Draft'?'secondary':'warning') ?>-subtle text-<?= $est['status']==='Approved'?'success':($est['status']==='Draft'?'secondary':'warning') ?>">
                                    <?= esc($est['status']) ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <strong class="text-primary fs-5">$<?= number_format($est['total_amount'], 2) ?></strong>
                                <div class="d-flex gap-2">
                                    <a href="<?= site_url("estimates/{$est['id']}") ?>" class="btn btn-sm btn-outline-primary">Open Worksheet</a>
                                    <form action="<?= site_url("estimates/{$est['id']}/delete") ?>" method="POST" onsubmit="return confirm('Delete this estimate completely?');">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Bid Leveling Board -->
    <div class="col-lg-6">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Bid Leveling</h5>
            <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#newBidModal">
                <i class="fa-solid fa-file-invoice-dollar me-1"></i> Log Vendor Quote
            </button>
        </div>

        <?php if (empty($groupedBids)): ?>
            <div class="card border-0 shadow-sm p-4 text-center text-muted">
                <i class="fa-solid fa-scale-balanced fs-3 d-block mb-2"></i>
                No vendor quotes logged yet.
            </div>
        <?php else: ?>
            <div class="row g-3">
            <?php foreach ($groupedBids as $trade => $tradeBids): ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="fa-solid fa-toolbox text-secondary"></i> <?= esc($trade) ?>
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($tradeBids as $bid): ?>
                                <li class="list-group-item p-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold"><?= esc($bid['vendor_name']) ?></div>
                                        <div class="small text-muted mt-1">Amount: <strong class="text-dark">$<?= number_format($bid['bid_amount'], 2) ?></strong></div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if ($bid['status'] === 'Awarded'): ?>
                                            <span class="badge bg-success"><i class="fa-solid fa-trophy me-1"></i> Awarded</span>
                                        <?php elseif ($bid['status'] === 'Rejected'): ?>
                                            <span class="badge bg-danger">Rejected</span>
                                        <?php else: ?>
                                            <!-- Action Buttons for Pending -->
                                            <button type="button" class="btn btn-sm btn-success" title="Award" onclick="openBidActionModal(<?= $bid['id'] ?>, 'Awarded')">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" title="Reject" onclick="openBidActionModal(<?= $bid['id'] ?>, 'Rejected')">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($bid['quote_filepath']): ?>
                                            <a href="<?= base_url('uploads/' . esc($bid['quote_filepath'])) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="View Quote PDF">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Delete Bid -->
                                        <form action="<?= site_url("bids/{$bid['id']}/delete") ?>" method="POST" onsubmit="return confirm('Delete this quote?');" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-light text-muted border-0"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- New Estimate Modal -->
<div class="modal fade" id="newEstimateModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url("projects/{$project['id']}/estimates") ?>" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Create New Estimate Workspace</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Estimate Title / Phase</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Rough Order of Magnitude, Phase 1 Budget" required>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="fa-solid fa-calculator"></i> Create Estimate
                </button>
            </div>
        </form>
    </div>
</div>

<!-- New Bid Modal -->
<div class="modal fade" id="newBidModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url("projects/{$project['id']}/bids") ?>" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Log Vendor Quote</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Trade Package</label>
                    <input type="text" name="trade_package" class="form-control" placeholder="e.g. Electrical, HVAC, Concrete" required>
                    <div class="form-text">Quotes with the exact same Trade Package name will be grouped together for leveling.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Vendor / Subcontractor Name</label>
                    <input type="text" name="vendor_name" class="form-control" placeholder="Company Name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Bid Amount ($)</label>
                    <input type="number" step="0.01" min="0" name="bid_amount" class="form-control" placeholder="0.00" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Quote File (Optional)</label>
                    <input type="file" name="quote_file" class="form-control" accept=".pdf,.png,.jpg">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-dark d-flex align-items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Log Quote
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bid Action Modal (Award/Reject with Remarks) -->
<div class="modal fade" id="bidActionModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="bidActionForm" action="#" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="status" id="bidActionStatus">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold" id="bidActionTitle">Review Bid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3" id="bidActionMessage">Please provide any remarks or reasons for this decision.</p>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Remarks <span class="text-danger">*</span></label>
                    <textarea name="remarks" class="form-control" rows="3" placeholder="Enter remarks..." required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="bidActionSubmitBtn">Submit Decision</button>
            </div>
        </form>
    </div>
</div>

<script>
function openBidActionModal(bidId, status) {
    const form = document.getElementById('bidActionForm');
    form.action = "<?= site_url('bids/') ?>" + bidId + "/status";
    
    document.getElementById('bidActionStatus').value = status;
    
    const title = document.getElementById('bidActionTitle');
    const submitBtn = document.getElementById('bidActionSubmitBtn');
    
    if (status === 'Awarded') {
        title.innerHTML = '<i class="fa-solid fa-trophy text-success me-2"></i>Award Quote';
        submitBtn.className = 'btn btn-success';
        submitBtn.innerHTML = 'Confirm Award';
    } else {
        title.innerHTML = '<i class="fa-solid fa-xmark text-danger me-2"></i>Reject Quote';
        submitBtn.className = 'btn btn-danger';
        submitBtn.innerHTML = 'Confirm Rejection';
    }
    
    new bootstrap.Modal(document.getElementById('bidActionModal')).show();
}
</script>
