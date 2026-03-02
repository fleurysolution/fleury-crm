<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1"><?= esc(t('approval_request_title')) ?> #<?= (int)($request['id'] ?? 0) ?></h4>
        <p class="text-muted mb-0"><?= esc(t('request_summary')) ?></p>
    </div>
    <a href="<?= site_url('approval/requests') ?>" class="btn btn-outline-secondary"><?= esc(t('back')) ?></a>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row mb-2"><div class="col-4 text-muted"><?= esc(t('request_key')) ?></div><div class="col-8"><?= esc($request['request_key'] ?? '-') ?></div></div>
                <div class="row mb-2"><div class="col-4 text-muted"><?= esc(t('module')) ?></div><div class="col-8"><?= esc($request['module_key'] ?? '-') ?></div></div>
                <div class="row mb-2"><div class="col-4 text-muted"><?= esc(t('entity')) ?></div><div class="col-8"><?= esc(($request['entity_type'] ?? '-') . ' #' . ($request['entity_id'] ?? '-')) ?></div></div>
                <div class="row mb-2"><div class="col-4 text-muted"><?= esc(t('status')) ?></div><div class="col-8"><?= esc($request['status'] ?? '-') ?></div></div>
                <div class="row"><div class="col-4 text-muted"><?= esc(t('current_step')) ?></div><div class="col-8"><?= esc($request['current_step_no'] ?? '-') ?></div></div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <h6 class="mb-3"><?= esc(t('workflow_steps')) ?></h6>
                <?php if (!empty($request['steps'])): ?>
                    <div class="list-group">
                        <?php foreach ($request['steps'] as $step): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <strong>Step <?= (int)$step['step_no'] ?></strong>
                                    <span class="badge bg-light text-dark border"><?= esc($step['status']) ?></span>
                                </div>
                                <small class="text-muted">
                                    Acted by: <?= esc($step['acted_by'] ?? '-') ?> |
                                    At: <?= esc($step['acted_at'] ?? '-') ?>
                                </small>
                                <?php if (!empty($step['action_note'])): ?>
                                    <div class="mt-2"><em><?= esc($step['action_note']) ?></em></div>
                                <?php endif; ?>
                                
                                <?php if (!empty($step['signature_data'])): ?>
                                    <div class="mt-3 p-2 border rounded bg-light text-center" style="max-width: 300px;">
                                        <img src="<?= esc($step['signature_data']) ?>" alt="Signature" class="img-fluid mb-2" style="max-height: 60px;">
                                        <div class="small text-muted" style="font-size: 0.75rem;">
                                            Signed: <?= date('M d, Y H:i', strtotime($step['signed_at'])) ?><br>
                                            IP: <?= esc($step['signature_ip']) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No steps found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <?php if (($request['status'] ?? '') === 'pending'): ?>
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-3"><?= esc(t('take_action')) ?></h6>

                    <form id="approveForm" method="post" action="<?= site_url('approval/requests/' . (int)$request['id'] . '/approve') ?>" class="mb-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="signature_data" id="signatureDataInput">
                        <label class="form-label"><?= esc(t('approval_note')) ?></label>
                        <textarea id="approveNote" name="note" class="form-control mb-2" rows="3"></textarea>
                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#signApprovalModal">
                            <i class="fa-solid fa-pen-nib me-1"></i><?= esc(t('approve_and_sign')) ?>
                        </button>
                    </form>

                    <form method="post" action="<?= site_url('approval/requests/' . (int)$request['id'] . '/reject') ?>">
                        <?= csrf_field() ?>
                        <label class="form-label"><?= esc(t('rejection_reason')) ?></label>
                        <textarea name="note" class="form-control mb-2" rows="3" required></textarea>
                        <button type="submit" class="btn btn-danger w-100"><?= esc(t('reject')) ?></button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Digital Signature Modal for Approvals -->
<div class="modal fade" id="signApprovalModal" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content border-0 shadow" style="border-radius:14px;">
    <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-pen-nib me-2"></i>Sign Approval</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body pt-3">
        <div class="alert alert-info border-0 rounded-3 small">
            <i class="fa-solid fa-info-circle me-1"></i>
            By signing, you formally approve this request step.
        </div>
        <div class="bg-light rounded-3 p-3 border text-center">
            <label class="form-label fw-semibold mb-2">Please draw your signature below:</label>
            <div class="border bg-white rounded-3 overflow-hidden shadow-sm mx-auto" style="width: 100%; max-width: 600px; height: 300px;">
                <canvas id="approvalSignatureCanvas" style="width: 100%; height: 100%; touch-action: none;"></canvas>
            </div>
            <div class="mt-3">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="approvalSignaturePad.clear()">
                    <i class="fa-solid fa-eraser me-1"></i>Clear Signature
                </button>
            </div>
        </div>
    </div>
    <div class="modal-footer border-0 bg-light rounded-bottom-4">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success fw-semibold shadow-sm" onclick="submitApprovalSignature()">
            <i class="fa-solid fa-check me-1"></i>Confirm & Approve
        </button>
    </div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
let approvalSignaturePad;

document.addEventListener("DOMContentLoaded", function() {
    const signModal = document.getElementById('signApprovalModal');
    if (signModal) {
        signModal.addEventListener('shown.bs.modal', function () {
            const canvas = document.getElementById('approvalSignatureCanvas');
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
            }
            resizeCanvas();
            
            if (!approvalSignaturePad) {
                approvalSignaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)'
                });
            } else {
                approvalSignaturePad.clear();
            }
        });
    }
});

function submitApprovalSignature() {
    if (approvalSignaturePad.isEmpty()) {
        alert("Please provide a signature first.");
        return;
    }
    
    // Grab signature and inject into the form and submit
    const dataURL = approvalSignaturePad.toDataURL("image/png");
    document.getElementById('signatureDataInput').value = dataURL;
    document.getElementById('approveForm').submit();
}
</script>

<?= $this->endSection() ?>
