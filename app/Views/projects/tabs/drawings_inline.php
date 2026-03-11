<!-- Drawings Tab (Master Drawing List) -->
<?php
$dModel = new \App\Models\DrawingModel();
$drawings = $dModel->forProject($project['id']);

$disciplines = [
    'Architectural',
    'Structural',
    'Mechanical',
    'Electrical',
    'Plumbing',
    'Civil',
    'Other'
];
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Master Drawing List</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newDrawingModal">
        <i class="fa-solid fa-plus me-1"></i> Add Drawing
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">Drawing No.</th>
                    <th>Title</th>
                    <th>Discipline</th>
                    <th>Revision</th>
                    <th>Status</th>
                    <th>Added By</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($drawings)): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="fa-regular fa-folder-open fs-3 mb-2 d-block"></i>
                        No drawings registered yet.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($drawings as $drw): 
                        $badgeColor = 'success';
                        if ($drw['status'] === 'Superseded') $badgeColor = 'secondary';
                        if ($drw['status'] === 'Pending') $badgeColor = 'warning';
                    ?>
                    <tr>
                        <td class="ps-3 fw-bold">
                            <i class="fa-regular fa-file-pdf text-danger me-2"></i><?= esc($drw['drawing_number']) ?>
                        </td>
                        <td><?= esc($drw['title']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= esc($drw['discipline']) ?></span></td>
                        <td><span class="badge bg-info-subtle text-info">Rev <?= esc($drw['revision']) ?></span></td>
                        <td><span class="badge bg-<?= $badgeColor ?>-subtle text-<?= $badgeColor ?>"><?= esc($drw['status']) ?></span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar" style="width:24px;height:24px;border-radius:50%;background:#e9ecef;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:bold;">
                                    <?= strtoupper(substr($drw['creator_name'] ?? 'U N', 0, 2)) ?>
                                </div>
                                <span class="small"><?= esc($drw['creator_name'] ?? 'Unknown User') ?></span>
                            </div>
                        </td>
                        <td class="text-end pe-3">
                            <a href="<?= site_url("drawings/{$drw['id']}") ?>" class="btn btn-sm btn-outline-primary">
                                View & History
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Drawing Modal -->
<div class="modal fade" id="newDrawingModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url("projects/{$project['id']}/drawings") ?>" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Add Master Drawing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Drawing Number</label>
                    <input type="text" name="drawing_no" class="form-control" placeholder="e.g. A-101" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Title / Description</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Ground Floor Plan" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Discipline</label>
                    <select name="discipline" class="form-select" required>
                        <option value="">Select discipline...</option>
                        <?php foreach($disciplines as $disc): ?>
                            <option value="<?= esc($disc) ?>"><?= esc($disc) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Initial Revision</label>
                        <input type="text" name="initial_revision" class="form-control" value="00" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Revision Date</label>
                        <input type="date" name="revision_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">File (PDF/DWG)</label>
                    <input type="file" name="drawing_file" class="form-control" accept=".pdf,.dwg,.jpg,.png" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Revision Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes for this revision"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Upload Drawing
                </button>
            </div>
        </form>
    </div>
</div>
