<?php
/**
 * EXPECTS:
 * $project (array)
 * $submittals (array of submittals, populated from Submittals controller)
 * $counts (array, populated from Submittals controller)
 */
// If not pre-loaded by main controller, load it now
$subModel = new \App\Models\SubmittalModel();
$submittals = $subModel->forProject($project['id']);
$counts = $subModel->statusCounts($project['id']);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="fa-solid fa-file-signature me-2 text-primary"></i> Submittals</h5>
    <button class="btn btn-sm btn-primary" onclick="openNewSubmittalModal()">
        <i class="fa-solid fa-plus me-1"></i> New Submittal
    </button>
</div>

<!-- Status Cards -->
<div class="row g-2 mb-4">
    <?php
    $sumCards = [
        ['label'=>'Total',     'val'=>array_sum($counts), 'color'=>'secondary'],
        ['label'=>'Draft',     'val'=>$counts['draft']??0, 'color'=>'secondary'],
        ['label'=>'Under Rev', 'val'=>$counts['under_review']??0, 'color'=>'warning'],
        ['label'=>'Apprv.',    'val'=>$counts['approved']??0, 'color'=>'success'],
        ['label'=>'Apprv. As Noted', 'val'=>$counts['approved_as_noted']??0, 'color'=>'info'],
        ['label'=>'Rejected',  'val'=>$counts['rejected']??0, 'color'=>'danger'],
        ['label'=>'Resubmit',  'val'=>$counts['resubmit']??0, 'color'=>'dark'],
    ];
    foreach ($sumCards as $c): ?>
    <div class="col-6 col-md-2">
        <div class="card border-0 bg-<?= $c['color'] ?>-subtle text-center py-2 px-1">
            <div class="fw-bold fs-5 text-<?= $c['color'] ?>"><?= $c['val'] ?></div>
            <div class="text-muted" style="font-size:.72rem;"><?= $c['label'] ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Submittals Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    <th>Type</th>
                    <th>Spec Section</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Submitter</th>
                    <th>Reviewer</th>
                    <th>Due</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($submittals)): ?>
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">No submittals found.</td>
                </tr>
                <?php else: foreach($submittals as $sub): 
                    $badge = ['draft'=>'secondary','submitted'=>'info','under_review'=>'warning','approved'=>'success','approved_as_noted'=>'primary','rejected'=>'danger','resubmit'=>'dark'][$sub['status']] ?? 'secondary';
                ?>
                <tr>
                    <td class="fw-semibold">
                        <a href="<?= site_url("submittals/{$sub['id']}") ?>" class="text-decoration-none"><?= esc($sub['submittal_number']) ?></a>
                        <?php if($sub['revision'] > 0): ?>
                            <span class="badge bg-light text-dark border ms-1">Rev <?= $sub['revision'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge bg-light text-dark border"><?= ucwords(str_replace('_',' ',$sub['type'])) ?></span></td>
                    <td class="text-muted small"><?= esc($sub['spec_section']) ?></td>
                    <td class="fw-semibold"><?= esc($sub['title']) ?></td>
                    <td><span class="badge bg-<?= $badge ?>-subtle text-<?= $badge ?>"><?= ucwords(str_replace('_',' ',$sub['status'])) ?></span></td>
                    <td class="small"><?= esc($sub['submitter_name']) ?: '<span class="text-muted">—</span>' ?></td>
                    <td class="small"><?= esc($sub['reviewer_name']) ?: '<span class="text-muted">—</span>' ?></td>
                    <td class="small">
                        <?php if ($sub['due_date']): ?>
                            <?= date('M j, Y', strtotime($sub['due_date'])) ?>
                            <?php if ($sub['status'] !== 'approved' && $sub['status'] !== 'approved_as_noted' && $sub['status'] !== 'rejected' && strtotime($sub['due_date']) < time()): ?>
                                <i class="fa-solid fa-triangle-exclamation text-danger ms-1" title="Overdue"></i>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <a href="<?= site_url("submittals/{$sub['id']}") ?>" class="btn btn-sm btn-light border"><i class="fa-solid fa-arrow-right"></i></a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- New Submittal Modal -->
<div class="modal fade" id="newSubmittalModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-semibold"><i class="fa-solid fa-file-signature me-2 text-primary"></i> Create Submittal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="card border-0 shadow-sm p-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Number <span class="text-danger">*</span></label>
                            <input type="text" id="subNumber" class="form-control" placeholder="e.g. 01-001" required>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label small fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" id="subTitle" class="form-control" placeholder="e.g. Paint Color Samples">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Type</label>
                            <select id="subType" class="form-select">
                                <option value="shop_drawing">Shop Drawing</option>
                                <option value="product_data">Product Data</option>
                                <option value="sample">Sample</option>
                                <option value="o_and_m">O&M Manual</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Spec Section</label>
                            <input type="text" id="subSpec" class="form-control" placeholder="e.g. 09 90 00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Due Date</label>
                            <input type="date" id="subDue" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Reviewer</label>
                            <select id="subReviewer" class="form-select">
                                <option value="">-- Unassigned --</option>
                                <?php foreach($members??[] as $m): ?>
                                    <option value="<?= $m['user_id'] ?>"><?= esc($m['name']) ?> (<?= esc($m['role']??'') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Description</label>
                            <textarea id="subDescription" class="form-control" rows="3" placeholder="Provide detailed information about this submittal..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Attachments (Files / Photos)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-paperclip text-muted"></i></span>
                                <input type="file" id="subFiles" class="form-control" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip" capture="environment">
                            </div>
                            <div class="smallest text-muted mt-1">Select multiple files, or use your camera to capture photos.</div>
                            <div id="subFilePreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="btnSubmitSubmittal" class="btn btn-primary" onclick="submitNewSubmittal()">
                    <i class="fa-solid fa-paper-plane me-1"></i> Submit
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('subFiles')?.addEventListener('change', function(e) {
    const preview = document.getElementById('subFilePreview');
    preview.innerHTML = '';
    [...e.target.files].forEach(file => {
        const div = document.createElement('div');
        div.className = 'badge bg-light text-dark border p-2 d-flex align-items-center smallest';
        div.innerHTML = `<i class="fa-solid fa-file me-2 text-muted"></i> ${file.name} (${(file.size/1024).toFixed(1)} KB)`;
        preview.appendChild(div);
    });
});

function openNewSubmittalModal() {
    new bootstrap.Modal(document.getElementById('newSubmittalModal')).show();
}

function submitNewSubmittal() {
    const title = document.getElementById('subTitle').value.trim();
    const number = document.getElementById('subNumber').value.trim();
    const btn = document.getElementById('btnSubmitSubmittal');

    if (!number) { alert('Submittal Number is required.'); return; }
    if (!title) { alert('Title is required.'); return; }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-1"></i> Submitting...';

    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('submittal_number', number);
    fd.append('title',            title);
    fd.append('type',             document.getElementById('subType').value);
    fd.append('spec_section',     document.getElementById('subSpec').value);
    fd.append('due_date',         document.getElementById('subDue').value);
    fd.append('assigned_to',      document.getElementById('subReviewer').value);
    fd.append('description',      document.getElementById('subDescription').value);

    const files = document.getElementById('subFiles').files;
    for (let i = 0; i < files.length; i++) {
        fd.append('attachments[]', files[i]);
    }

    fetch(`/staging/public/projects/<?= $project['id'] ?>/submittals`, {
        method: 'POST', 
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            window.location.href = `/staging/public/index.php/submittals/${d.id}`;
        } else {
            alert(d.error || 'Could not create Submittal.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> Submit';
        }
    })
    .catch(err => {
        console.error(err);
        alert('An error occurred while submitting.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> Submit';
    });
}
</script>
