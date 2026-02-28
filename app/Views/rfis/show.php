<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$statusColors = [
    'draft'        => 'secondary',
    'submitted'    => 'primary',
    'under_review' => 'warning',
    'answered'     => 'info',
    'closed'       => 'success',
];
$priorityColors = ['low'=>'info','medium'=>'secondary','high'=>'warning','urgent'=>'danger'];
$statusBadge = $statusColors[$rfi['status']] ?? 'secondary';
$priorityBadge = $priorityColors[$rfi['priority']] ?? 'secondary';
?>

<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= site_url("projects/{$project['id']}?tab=rfis") ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h1 class="h3 mb-0 fw-bold text-dark">RFI <?= esc($rfi['rfi_number']) ?>: <?= esc($rfi['title']) ?></h1>
        </div>
        <div class="d-flex gap-2">
            <!-- Update Status -->
            <select class="form-select form-select-sm" style="width:160px;" id="rfiStatusDropdown" onchange="updateRfiStatus(this.value)">
                <?php foreach ($statusColors as $st => $_): ?>
                <option value="<?= $st ?>" <?= $rfi['status']===$st?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$st)) ?></option>
                <?php endforeach; ?>
            </select>
            <a href="<?= site_url("rfis/{$rfi['id']}/export") ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-file-export me-1"></i> Export
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- RFI Details Side -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Details</h5>
                    
                    <div class="mb-3">
                        <label class="small text-muted d-block fw-bold">Status</label>
                        <span class="badge bg-<?= $statusBadge ?>-subtle text-<?= $statusBadge ?> px-2 py-1">
                            <?= strtoupper(str_replace('_', ' ', $rfi['status'])) ?>
                        </span>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted d-block fw-bold">Priority</label>
                        <span class="badge bg-<?= $priorityBadge ?>-subtle text-<?= $priorityBadge ?> px-2 py-1">
                            <?= strtoupper($rfi['priority']) ?>
                        </span>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted d-block fw-bold">Discipline</label>
                        <div class="fw-semibold"><?= esc($rfi['discipline'] ?: '—') ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted d-block fw-bold">Due Date</label>
                        <div class="fw-semibold">
                            <?= $rfi['due_date'] ? date('d M Y', strtotime($rfi['due_date'])) : '—' ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted d-block fw-bold">Assigned To</label>
                        <div class="fw-semibold">
                            <?= esc($rfi['assignee_name'] ?? 'Unassigned') ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted d-block fw-bold">Submitted By</label>
                        <div class="fw-semibold">
                            <?= esc($rfi['submitter_name'] ?? 'System') ?>
                        </div>
                    </div>

                    <div>
                        <label class="small text-muted d-block fw-bold">Created At</label>
                        <div class="fw-semibold small">
                            <?= date('d M Y, H:i', strtotime($rfi['created_at'])) ?>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Impact & Resolution</h5>
                    
                    <div class="mb-3">
                        <label class="small text-muted d-block fw-bold">Cost Impact</label>
                        <div class="fw-semibold"><?= esc($rfi['cost_impact'] ?: 'None specified') ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted d-block fw-bold">Schedule Impact</label>
                        <div class="fw-semibold"><?= esc($rfi['schedule_impact'] ?: 'None specified') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description & Activity Side -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Question / Description</h5>
                    <div class="bg-light p-3 rounded text-dark" style="white-space: pre-wrap; font-size: 0.95rem;">
                        <?= esc($rfi['description'] ?: 'No description provided.') ?>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-3 mt-4">Responses</h5>
            
            <div class="responses-list mb-4">
                <?php if (empty($responses)): ?>
                    <p class="text-muted fst-italic">No responses yet.</p>
                <?php else: ?>
                    <?php foreach ($responses as $resp): ?>
                        <div class="card border-0 shadow-sm mb-3 <?= $resp['is_official'] ? 'border-start border-4 border-success' : '' ?>" style="border-radius:10px;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="fw-bold text-dark">
                                        <i class="fa-solid fa-user-circle text-muted me-1"></i> 
                                        <?= esc($resp['author_name'] ?? 'System') ?>
                                        <?php if ($resp['is_official']): ?>
                                            <span class="badge bg-success-subtle text-success ms-2"><i class="fa-solid fa-check fs-ms me-1"></i>Official</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="small text-muted">
                                        <?= date('d M Y, H:i', strtotime($resp['created_at'])) ?>
                                    </div>
                                </div>
                                <div class="text-dark" style="white-space: pre-wrap; font-size: 0.9rem;">
                                    <?= esc($resp['body']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- New Response Form -->
            <div class="card border-0 shadow-sm" style="border-radius:12px; background-color: #f8f9fa;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-reply me-2 text-primary"></i>Post a Reply</h6>
                    <form id="replyForm" onsubmit="postReply(event)">
                        <textarea id="replyBody" class="form-control border-1 mb-3 bg-white" rows="3" placeholder="Type your response here..." required></textarea>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="replyOfficial" value="1">
                                <label class="form-check-label small fw-semibold text-success ms-1" for="replyOfficial">
                                    Mark as Official Answer
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Post Reply</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const CSRF_NAME = '<?= csrf_token() ?>';
const CSRF_TOKEN = '<?= csrf_hash() ?>';

function updateRfiStatus(status) {
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('status', status);
    fetch(`<?= site_url("rfis/{$rfi['id']}/status") ?>`, { 
        method: 'POST', 
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(r => r.json())
        .then(d => {
            if(d.success) location.reload();
            else alert('Error updating status.');
        });
}

function postReply(e) {
    e.preventDefault();
    const body = document.getElementById('replyBody').value.trim();
    if (!body) return;

    const isOfficial = document.getElementById('replyOfficial').checked ? '1' : '0';
    
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('body', body);
    fd.append('is_official', isOfficial);

    fetch(`<?= site_url("rfis/{$rfi['id']}/respond") ?>`, { 
        method: 'POST', 
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(r => r.json())
        .then(d => {
            if(d.success) location.reload();
            else alert('Error posting reply.');
        })
        .catch(err => alert('Network error.'));
}
</script>

<?= $this->endSection() ?>
