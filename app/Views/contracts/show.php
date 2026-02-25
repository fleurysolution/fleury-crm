<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-file-contract me-2 text-primary"></i><?= esc($contract['contract_number']) ?></h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('projects/' . $project['id']) ?>" class="text-decoration-none"><?= esc($project['title']) ?></a></li>
            <li class="breadcrumb-item active"><?= esc($contract['contract_number']) ?></li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('projects/' . $project['id'] . '?tab=contracts') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Contract Details Card -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-semibold mb-0"><?= esc($contract['title']) ?></h5>
                <?php
                    $statusMap = [
                        'draft'      => 'bg-secondary',
                        'active'     => 'bg-success',
                        'on_hold'    => 'bg-warning text-dark',
                        'completed'  => 'bg-info',
                        'terminated' => 'bg-danger',
                    ];
                    $cls = $statusMap[$contract['status']] ?? 'bg-secondary';
                ?>
                <span class="badge <?= $cls ?> px-3 py-2"><?= ucfirst(str_replace('_', ' ', $contract['status'])) ?></span>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <span class="text-muted small">Contractor</span>
                        <div class="fw-semibold"><?= esc($contract['contractor_name'] ?? '—') ?></div>
                    </div>
                    <div class="col-sm-3">
                        <span class="text-muted small">Type</span>
                        <div class="fw-semibold"><?= ucfirst($contract['type'] ?? 'main') ?></div>
                    </div>
                    <div class="col-sm-3">
                        <span class="text-muted small">Currency</span>
                        <div class="fw-semibold"><?= esc($contract['currency'] ?? 'USD') ?></div>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-4">
                        <span class="text-muted small">Original Value</span>
                        <div class="fw-bold fs-5 text-primary"><?= number_format($contract['value'] ?? 0, 2) ?></div>
                    </div>
                    <div class="col-sm-4">
                        <span class="text-muted small">Approved Changes</span>
                        <div class="fw-bold fs-5 <?= $totalChg >= 0 ? 'text-success' : 'text-danger' ?>"><?= ($totalChg >= 0 ? '+' : '') . number_format($totalChg, 2) ?></div>
                    </div>
                    <div class="col-sm-4">
                        <span class="text-muted small">Current Value</span>
                        <div class="fw-bold fs-5"><?= number_format($currentVal, 2) ?></div>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-4">
                        <span class="text-muted small">Start Date</span>
                        <div class="fw-semibold"><?= $contract['start_date'] ?? '—' ?></div>
                    </div>
                    <div class="col-sm-4">
                        <span class="text-muted small">End Date</span>
                        <div class="fw-semibold"><?= $contract['end_date'] ?? '—' ?></div>
                    </div>
                    <div class="col-sm-4">
                        <span class="text-muted small">Retention %</span>
                        <div class="fw-semibold"><?= number_format($contract['retention_pct'] ?? 10, 1) ?>%</div>
                    </div>
                </div>
                <?php if (!empty($contract['scope'])): ?>
                <div class="mb-3">
                    <span class="text-muted small">Scope of Work</span>
                    <div class="mt-1"><?= nl2br(esc($contract['scope'])) ?></div>
                </div>
                <?php endif; ?>

                <!-- Status Actions -->
                <div class="d-flex gap-2 mt-3 pt-3 border-top">
                    <?php
                    $transitions = [
                        'draft'      => ['active' => 'Activate'],
                        'active'     => ['on_hold' => 'Pause', 'completed' => 'Complete', 'terminated' => 'Terminate'],
                        'on_hold'    => ['active' => 'Resume'],
                        'completed'  => [],
                        'terminated' => [],
                    ];
                    foreach ($transitions[$contract['status']] ?? [] as $next => $label):
                    ?>
                    <button class="btn btn-sm btn-outline-primary" onclick="updateContractStatus(<?= $contract['id'] ?>, '<?= $next ?>')">
                        <?= $label ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Amendments Sidebar -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">Variation Orders</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAmendModal">
                    <i class="fa-solid fa-plus me-1"></i>Add
                </button>
            </div>
            <div class="card-body px-4 pb-4">
                <?php if (empty($amendments)): ?>
                    <p class="text-muted small mb-0">No variation orders yet.</p>
                <?php else: ?>
                    <?php foreach ($amendments as $a): ?>
                    <div class="border rounded-3 p-3 mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold">VO #<?= $a['amendment_no'] ?></span>
                            <?php
                                $aCls = match($a['status']) {
                                    'approved' => 'bg-success', 'rejected' => 'bg-danger',
                                    default    => 'bg-warning text-dark'
                                };
                            ?>
                            <span class="badge <?= $aCls ?> small"><?= ucfirst($a['status']) ?></span>
                        </div>
                        <div class="small"><?= esc($a['title']) ?></div>
                        <div class="small text-muted mt-1">
                            Value: <span class="<?= $a['value_change'] >= 0 ? 'text-success' : 'text-danger' ?> fw-semibold"><?= ($a['value_change'] >= 0 ? '+' : '') . number_format($a['value_change'], 2) ?></span>
                            <?php if ($a['time_change']): ?> | Time: <?= $a['time_change'] > 0 ? '+' : '' ?><?= $a['time_change'] ?> days<?php endif; ?>
                        </div>
                        <?php if ($a['status'] === 'pending'): ?>
                        <div class="mt-2">
                            <button class="btn btn-xs btn-outline-success" onclick="approveAmendment(<?= $a['id'] ?>, this)">Approve</button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Amendment Modal -->
<div class="modal fade" id="addAmendModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content border-0 shadow" style="border-radius:14px;">
    <div class="modal-header border-0"><h5 class="modal-title fw-semibold">New Variation Order</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="mb-3"><label class="form-label fw-semibold">Title</label><input type="text" id="vo_title" class="form-control"></div>
        <div class="mb-3"><label class="form-label fw-semibold">Description</label><textarea id="vo_desc" class="form-control" rows="3"></textarea></div>
        <div class="row g-3">
            <div class="col-6"><label class="form-label fw-semibold">Value Change</label><input type="number" id="vo_value" class="form-control" step="0.01"></div>
            <div class="col-6"><label class="form-label fw-semibold">Time Change (days)</label><input type="number" id="vo_time" class="form-control" value="0"></div>
        </div>
    </div>
    <div class="modal-footer border-0">
        <button class="btn btn-primary" onclick="submitAmendment()"><i class="fa-solid fa-check me-1"></i>Submit</button>
    </div>
</div>
</div>
</div>

<script>
function updateContractStatus(id, status) {
    fetch(`<?= site_url('contracts/') ?>${id}/status`, {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body: `<?= csrf_token() ?>=<?= csrf_hash() ?>&status=${status}`
    }).then(r=>r.json()).then(d=> { if(d.success) location.reload(); });
}

function submitAmendment() {
    const body = new URLSearchParams({
        '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
        title: document.getElementById('vo_title').value,
        description: document.getElementById('vo_desc').value,
        value_change: document.getElementById('vo_value').value,
        time_change: document.getElementById('vo_time').value,
    });
    fetch(`<?= site_url('contracts/' . $contract['id'] . '/amend') ?>`, {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body
    }).then(r=>r.json()).then(d=> { if(d.success) location.reload(); });
}

function approveAmendment(id, btn) {
    fetch(`<?= site_url('contracts/amendments/') ?>${id}/approve`, {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body: `<?= csrf_token() ?>=<?= csrf_hash() ?>`
    }).then(r=>r.json()).then(d=> { if(d.success) location.reload(); });
}
</script>

<?= $this->endSection() ?>
