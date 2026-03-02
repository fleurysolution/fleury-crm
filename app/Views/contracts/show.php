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
        <a href="<?= site_url('contracts/' . $contract['id'] . '/pdf') ?>" class="btn btn-primary btn-sm" target="_blank">
            <i class="fa-solid fa-file-pdf me-1"></i>Download PDF
        </a>
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

    </div>

    <!-- Signatures Sidebar -->
    <div class="col-lg-4">
        <!-- Amendments Sidebar Moved Inside the Same Column for Layout Consistency -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
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
                        
                        <?php if (!empty($a['signature_data'])): ?>
                            <div class="mt-2 text-center p-2 rounded bg-light border">
                                <img src="<?= esc($a['signature_data']) ?>" alt="Signature" style="max-height: 40px;" class="mb-1">
                                <div style="font-size: 0.65rem;" class="text-muted">
                                    Signed: <?= date('M d, Y H:i', strtotime($a['signed_at'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($a['status'] === 'pending'): ?>
                        <div class="mt-2 d-flex gap-2">
                            <button class="btn btn-xs btn-outline-success" onclick="openSignVoModal(<?= $a['id'] ?>)">Approve</button>
                            <button class="btn btn-xs btn-outline-danger" onclick="openRejectVoModal(<?= $a['id'] ?>)">Decline</button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Digital Signatures Card -->
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-semibold mb-0 text-primary"><i class="fa-solid fa-file-signature me-2"></i>Signatures</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <!-- Client Signature -->
                <div class="mb-4">
                    <span class="text-muted small d-block mb-2">Client Signature</span>
                    <?php if (!empty($contract['client_signature_data'])): ?>
                        <div class="border rounded-3 p-3 text-center bg-light">
                            <img src="<?= esc($contract['client_signature_data']) ?>" alt="Client Signature" class="img-fluid" style="max-height: 100px;">
                            <div class="small text-muted mt-2">Signed: <?= date('M d, Y H:i', strtotime($contract['client_signed_at'])) ?></div>
                            <div class="small text-muted">IP: <?= esc($contract['client_ip_address']) ?></div>
                        </div>
                    <?php else: ?>
                        <div class="border rounded-3 p-3 text-center bg-light text-muted small">
                            Pending Client Signature
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Contractor Signature -->
                <div class="mb-4">
                    <span class="text-muted small d-block mb-2">Contractor Signature</span>
                    <?php if (!empty($contract['contractor_signature_data'])): ?>
                        <div class="border rounded-3 p-3 text-center bg-light">
                            <img src="<?= esc($contract['contractor_signature_data']) ?>" alt="Contractor Signature" class="img-fluid" style="max-height: 100px;">
                            <div class="small text-muted mt-2">Signed: <?= date('M d, Y H:i', strtotime($contract['contractor_signed_at'])) ?></div>
                            <div class="small text-muted">IP: <?= esc($contract['contractor_ip_address']) ?></div>
                        </div>
                    <?php else: ?>
                        <div class="border rounded-3 p-3 text-center bg-light text-muted small">
                            Pending Contractor Signature
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sign Action Button -->
                <?php
                    $roleSlug = session()->get('role_slug') ?? 'employee';
                    $canSign = false;
                    
                    if ($roleSlug === 'client' && empty($contract['client_signature_data'])) {
                        $canSign = true;
                    } elseif ($roleSlug !== 'client' && empty($contract['contractor_signature_data'])) {
                        $canSign = true;
                    }
                ?>
                
                <?php if ($canSign && $contract['status'] !== 'terminated'): ?>
                    <button class="btn btn-primary w-100 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#signContractModal">
                        <i class="fa-solid fa-pen-nib me-2"></i>Sign Now
                    </button>
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

<!-- Digital Signature Modal -->
<div class="modal fade" id="signContractModal" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content border-0 shadow" style="border-radius:14px;">
    <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-pen-nib me-2"></i>Sign Contract</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body pt-3">
        <div class="alert alert-info border-0 rounded-3 small">
            <i class="fa-solid fa-info-circle me-1"></i>
            By signing this document, you acknowledge and agree to the terms set forth in the contract provisions and scope of work.
        </div>
        <div class="bg-light rounded-3 p-3 border text-center">
            <label class="form-label fw-semibold mb-2">Please draw your signature below:</label>
            <div class="border bg-white rounded-3 overflow-hidden shadow-sm mx-auto" style="width: 100%; max-width: 600px; height: 300px;">
                <canvas id="signatureCanvas" style="width: 100%; height: 100%; touch-action: none;"></canvas>
            </div>
            <div class="mt-3">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="signaturePad.clear()">
                    <i class="fa-solid fa-eraser me-1"></i>Clear Signature
                </button>
            </div>
        </div>
    </div>
    <div class="modal-footer border-0 bg-light rounded-bottom-4">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary fw-semibold shadow-sm" onclick="submitSignature()">
            <i class="fa-solid fa-check me-1"></i>Confirm Signature
        </button>
    </div>
</div>
</div>
</div>

<!-- Variation Order Signature Modal -->
<div class="modal fade" id="signVoModal" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content border-0 shadow" style="border-radius:14px;">
    <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-pen-nib me-2"></i>Sign Variation Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body pt-3">
        <div class="alert alert-info border-0 rounded-3 small">
            <i class="fa-solid fa-info-circle me-1"></i>
            By signing, you formally approve this Variation Order and confirm its financial/schedule impact.
        </div>
        <div class="bg-light rounded-3 p-3 border text-center">
            <label class="form-label fw-semibold mb-2">Please draw your signature below:</label>
            <div class="border bg-white rounded-3 overflow-hidden shadow-sm mx-auto" style="width: 100%; max-width: 600px; height: 300px;">
                <canvas id="voSignatureCanvas" style="width: 100%; height: 100%; touch-action: none;"></canvas>
            </div>
            <div class="mt-3">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="voSignaturePad.clear()">
                    <i class="fa-solid fa-eraser me-1"></i>Clear Signature
                </button>
            </div>
        </div>
    </div>
    <div class="modal-footer border-0 bg-light rounded-bottom-4">
        <input type="hidden" id="pendingVoId" value="">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success fw-semibold shadow-sm" onclick="submitVoSignature()">
            <i class="fa-solid fa-check me-1"></i>Confirm & Approve
        </button>
    </div>
</div>
</div>
</div>

<!-- Reject Variation Order Modal -->
<div class="modal fade" id="rejectVoModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content border-0 shadow" style="border-radius:14px;">
    <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-danger"><i class="fa-solid fa-xmark me-2"></i>Decline Variation Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body pt-3">
        <div class="mb-3">
            <label class="form-label fw-semibold">Reason for Declining <span class="text-danger">*</span></label>
            <textarea id="rejectVoReason" class="form-control" rows="3" placeholder="Explain why you are declining this variation order..." required></textarea>
        </div>
    </div>
    <div class="modal-footer border-0 bg-light rounded-bottom-4">
        <input type="hidden" id="rejectPendingVoId" value="">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger fw-semibold shadow-sm" onclick="submitRejectVo()">
            <i class="fa-solid fa-ban me-1"></i>Decline
        </button>
    </div>
</div>
</div>
</div>

<!-- Signature Pad Library -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

<script>
let signaturePad;
let voSignaturePad;

document.addEventListener("DOMContentLoaded", function() {
    // Initialize Main Contract Signature Pad
    const signModal = document.getElementById('signContractModal');
    if (signModal) {
        signModal.addEventListener('shown.bs.modal', function () {
            const canvas = document.getElementById('signatureCanvas');
            function resizeCanvas() {
                const ratio =  Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
            }
            resizeCanvas();
            if (!signaturePad) {
                signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)', penColor: 'rgb(0, 0, 0)' });
            } else { signaturePad.clear(); }
        });
    }

    // Initialize Variation Order Signature Pad
    const voModalEl = document.getElementById('signVoModal');
    if (voModalEl) {
        voModalEl.addEventListener('shown.bs.modal', function () {
            const canvas = document.getElementById('voSignatureCanvas');
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
            }
            resizeCanvas();
            if (!voSignaturePad) {
                voSignaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)', penColor: 'rgb(0, 0, 0)' });
            } else { voSignaturePad.clear(); }
        });
    }
});

function submitSignature() {
    if (signaturePad.isEmpty()) { alert("Please provide a signature first."); return; }
    const dataURL = signaturePad.toDataURL("image/png");
    const body = new URLSearchParams({ '<?= csrf_token() ?>': '<?= csrf_hash() ?>', 'signature_data': dataURL });

    fetch(`<?= site_url('contracts/' . $contract['id'] . '/sign') ?>`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: body
    }).then(r => r.json()).then(d => {
        if (d.success) location.reload(); else alert(d.message || "An error occurred while saving the signature.");
    }).catch(e => { alert("Failed to submit signature."); });
}

function openSignVoModal(id) {
    document.getElementById('pendingVoId').value = id;
    new bootstrap.Modal(document.getElementById('signVoModal')).show();
}

function submitVoSignature() {
    if (voSignaturePad.isEmpty()) { alert("Please provide a signature first."); return; }
    
    const id = document.getElementById('pendingVoId').value;
    const dataURL = voSignaturePad.toDataURL("image/png");
    const body = new URLSearchParams({
        '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
        'signature_data': dataURL
    });

    fetch(`<?= site_url('contracts/amendments/') ?>${id}/approve`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: body
    }).then(r => r.json()).then(d => {
        if (d.success) location.reload(); else alert(d.message || "An error occurred.");
    }).catch(e => { alert("Failed to submit VO signature."); });
}

function openRejectVoModal(id) {
    document.getElementById('rejectPendingVoId').value = id;
    document.getElementById('rejectVoReason').value = '';
    new bootstrap.Modal(document.getElementById('rejectVoModal')).show();
}

function submitRejectVo() {
    const id = document.getElementById('rejectPendingVoId').value;
    const reason = document.getElementById('rejectVoReason').value.trim();
    if (!reason) {
        alert("Please provide a reason for declining.");
        return;
    }

    const body = new URLSearchParams({
        '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
        'reason': reason
    });

    fetch(`<?= site_url('contracts/amendments/') ?>${id}/reject`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: body
    }).then(r => r.json()).then(d => {
        if (d.success) location.reload(); else alert(d.message || "An error occurred.");
    }).catch(e => { alert("Failed to decline VO."); });
}

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
