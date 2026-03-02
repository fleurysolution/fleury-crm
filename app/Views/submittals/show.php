<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?php
$badge = ['draft'=>'secondary','submitted'=>'info','under_review'=>'warning','approved'=>'success','approved_as_noted'=>'primary','rejected'=>'danger','resubmit'=>'dark'][$submittal['status']] ?? 'secondary';
?>

<div class="d-flex align-items-start justify-content-between mb-0 pb-3 border-bottom">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1 small">
            <li class="breadcrumb-item"><a href="<?= site_url('projects') ?>" class="text-decoration-none">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url("projects/{$project['id']}?tab=submittals") ?>" class="text-decoration-none"><?= esc($project['title']) ?></a></li>
            <li class="breadcrumb-item active"><?= esc($submittal['submittal_number']) ?></li>
        </ol></nav>
        <h4 class="fw-bold mb-1">
            <i class="fa-solid fa-file-signature text-muted me-2"></i>
            <?= esc($submittal['submittal_number']) ?>: <?= esc($submittal['title']) ?>
        </h4>
        <div class="d-flex align-items-center gap-2 mt-2">
            <span class="badge bg-<?= $badge ?>-subtle text-<?= $badge ?> fs-6"><?= ucwords(str_replace('_',' ',$submittal['status'])) ?></span>
            <span class="badge bg-light text-dark border">Rev <?= $submittal['current_revision'] ?></span>
            <?php if ($submittal['type']): ?>
                <span class="badge bg-light text-dark border"><i class="fa-solid fa-tag me-1"></i> <?= ucwords(str_replace('_',' ',$submittal['type'])) ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-danger" onclick="deleteSubmittal()">
            <i class="fa-solid fa-trash me-1"></i>Delete
        </button>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-8">
        <!-- Submittal Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0">Submittal Details</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted small fw-semibold">Spec Section</div>
                        <div class="fw-medium"><?= esc($submittal['spec_section']) ?: '<span class="text-muted">—</span>' ?></div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted small fw-semibold">Due Date</div>
                        <div class="fw-medium">
                            <?= $submittal['due_date'] ? date('M j, Y', strtotime($submittal['due_date'])) : '<span class="text-muted">—</span>' ?>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted small fw-semibold">Created At</div>
                        <div class="fw-medium">
                            <?= $submittal['created_at'] ? date('M j, Y g:i A', strtotime($submittal['created_at'])) : '<span class="text-muted">—</span>' ?>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted small fw-semibold">Submitter</div>
                        <div class="fw-medium">
                            <?= esc($submittal['user_name'] ?? 'Unknown User') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review History -->
        <h6 class="fw-bold mb-3">Review History</h6>
        <div class="timeline ps-3 pe-2" style="border-left:2px solid #e9ecef;">
            <?php foreach($revisions as $rev): 
                $rBadge = ['submitted'=>'info','under_review'=>'warning','approved'=>'success','approved_as_noted'=>'primary','rejected'=>'danger','resubmit'=>'dark'][$rev['status']] ?? 'secondary';
                $icon   = ['submitted'=>'paper-plane','under_review'=>'eye','approved'=>'check','approved_as_noted'=>'check-double','rejected'=>'xmark','resubmit'=>'rotate-left'][$rev['status']] ?? 'circle';
            ?>
            <div class="position-relative mb-4 ps-4">
                <div class="position-absolute bg-<?= $rBadge ?> text-white d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width:32px;height:32px;left:-17px;top:0;">
                    <i class="fa-solid fa-<?= $icon ?> small"></i>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="fw-bold mb-0">Revision <?= $rev['revision_no'] ?> 
                                    <span class="badge bg-<?= $rBadge ?>-subtle text-<?= $rBadge ?> ms-2"><?= ucwords(str_replace('_',' ',$rev['status'])) ?></span>
                                </h6>
                            </div>
                            <div class="text-muted small"><?= $rev['reviewed_at'] ? date('M j, Y g:i A', strtotime($rev['reviewed_at'])) : date('M j, Y g:i A', strtotime($rev['created_at'])) ?></div>
                        </div>
                        <?php if ($rev['reviewer_name']): ?>
                            <div class="text-muted small mb-2"><i class="fa-solid fa-user-pen me-1"></i> Reviewed by <strong><?= esc($rev['reviewer_name']) ?></strong></div>
                        <?php endif; ?>
                        
                        <?php if ($rev['notes']): ?>
                            <div class="p-3 bg-light rounded small mt-2">
                                <?= nl2br(esc($rev['notes'])) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($rev['filepath']): ?>
                            <div class="mt-3">
                                <a href="<?= base_url('uploads/'.$rev['filepath']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-paperclip me-1"></i> Download Attachment
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($rev['signature_data'])): ?>
                            <div class="mt-3 border bg-light text-center p-2 rounded d-inline-block">
                                <img src="<?= esc($rev['signature_data']) ?>" alt="Signature" style="max-height:50px;">
                                <div class="small text-muted" style="font-size:0.7rem;">
                                    Signed: <?= date('M d, Y H:i', strtotime($rev['signed_at'])) ?><br>
                                    IP: <?= esc($rev['signature_ip']) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Review Action Panel -->
        <div class="card border-0 shadow-sm sticky-top" style="top:2rem;">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0">Leave a Review</h6>
            </div>
            <div class="card-body">
                <form id="reviewForm" onsubmit="submitReview(event)">
                    <input type="hidden" id="submittalSignatureData" value="">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Action</label>
                        <select id="reviewStatus" class="form-select" required>
                            <option value="">-- Select Decision --</option>
                            <option value="approved">Approve & Close</option>
                            <option value="approved_as_noted">Approve as Noted & Close</option>
                            <option value="rejected">Reject & Close</option>
                            <option value="resubmit">Revise and Resubmit</option>
                            <option value="under_review">Mark Under Review</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-primary"><i class="fa-solid fa-share me-1"></i> Forward For Additional Review (Optional)</label>
                        <select id="forwardTo" class="form-select">
                            <option value="">-- No, close review process --</option>
                            <?php foreach($members??[] as $m): if ($m['user_id'] == $submittal['reviewer_id']) continue; ?>
                                <option value="<?= $m['user_id'] ?>"><?= esc($m['name']) ?> (<?= esc($m['role']??'') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text" style="font-size:0.7rem;">If selected, the status will remain "Under Review" and be assigned to this person, regardless of the Action chosen above.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Notes / Comments</label>
                        <textarea id="reviewNotes" class="form-control" rows="4" placeholder="Enter review comments or conditions..."></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-medium"><i class="fa-solid fa-check me-2"></i>Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Digital Signature Modal -->
<div class="modal fade" id="signSubmittalModal" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content border-0 shadow" style="border-radius:14px;">
    <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-pen-nib me-2"></i>Sign Submittal Review</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body pt-3">
        <div class="alert alert-info border-0 rounded-3 small">
            <i class="fa-solid fa-info-circle me-1"></i>
            Approving this submittal requires a formal digital signature to enforce accountability.
        </div>
        <div class="bg-light rounded-3 p-3 border text-center">
            <label class="form-label fw-semibold mb-2">Please draw your signature below:</label>
            <div class="border bg-white rounded-3 overflow-hidden shadow-sm mx-auto" style="width: 100%; max-width: 600px; height: 300px;">
                <canvas id="submittalSignatureCanvas" style="width: 100%; height: 100%; touch-action: none;"></canvas>
            </div>
            <div class="mt-3">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="submittalSignaturePad.clear()">
                    <i class="fa-solid fa-eraser me-1"></i>Clear Signature
                </button>
            </div>
        </div>
    </div>
    <div class="modal-footer border-0 bg-light rounded-bottom-4">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary fw-semibold shadow-sm" onclick="confirmSubmittalSignature()">
            <i class="fa-solid fa-check me-1"></i>Confirm & Submit
        </button>
    </div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
const CSRF_TOKEN = '<?= csrf_hash() ?>';
const CSRF_NAME  = '<?= csrf_token() ?>';
let submittalSignaturePad;

document.addEventListener("DOMContentLoaded", function() {
    const signModal = document.getElementById('signSubmittalModal');
    if (signModal) {
        signModal.addEventListener('shown.bs.modal', function () {
            const canvas = document.getElementById('submittalSignatureCanvas');
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
            }
            resizeCanvas();
            
            if (!submittalSignaturePad) {
                submittalSignaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)', penColor: 'rgb(0, 0, 0)' });
            } else {
                submittalSignaturePad.clear();
            }
        });
    }
});

function confirmSubmittalSignature() {
    if (submittalSignaturePad.isEmpty()) {
        alert("Please provide a signature first.");
        return;
    }
    const dataURL = submittalSignaturePad.toDataURL("image/png");
    document.getElementById('submittalSignatureData').value = dataURL;
    
    // Hide modal and trigger the actual submission
    const modal = bootstrap.Modal.getInstance(document.getElementById('signSubmittalModal'));
    modal.hide();
    
    submitReview(new Event('submit'));
}

function submitReview(e) {
    if (e) e.preventDefault();
    
    const status    = document.getElementById('reviewStatus').value;
    const forwardTo = document.getElementById('forwardTo').value;
    const notes     = document.getElementById('reviewNotes').value;
    const sigData   = document.getElementById('submittalSignatureData').value;

    if (!status) { alert('Please select a review decision.'); return; }

    // If approving, require a signature
    if ((status === 'approved' || status === 'approved_as_noted') && !sigData) {
        new bootstrap.Modal(document.getElementById('signSubmittalModal')).show();
        return; // Intercepted, wait for signature
    }

    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('status', status);
    fd.append('forward_to', forwardTo);
    fd.append('notes', notes);
    if (sigData) fd.append('signature_data', sigData);

    fetch(`<?= site_url("submittals/{$submittal['id']}/review") ?>`, {
        method: 'POST', body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) location.reload();
        else alert('Failed to submit review.');
    });
}

function deleteSubmittal() {
    if(!confirm('Are you sure you want to delete this submittal? This cannot be undone.')) return;
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fetch(`<?= site_url("submittals/{$submittal['id']}/delete") ?>`, {method:'POST', body: fd})
        .then(() => {
            window.location.href = `<?= site_url("projects/{$project['id']}?tab=submittals") ?>`;
        });
}
</script>

<?= $this->endSection() ?>
