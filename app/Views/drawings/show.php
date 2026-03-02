<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<div class="mb-3">
    <a href="<?= site_url("projects/{$project['id']}?tab=drawings") ?>" class="text-decoration-none text-muted">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Project Drawings
    </a>
</div>

<div class="row g-4">
    <!-- Drawing Master Detail -->
    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:20px;">
                            <i class="fa-regular fa-file-pdf"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1"><?= esc($drawing['drawing_no']) ?></h4>
                            <span class="badge bg-light text-dark border"><?= esc($drawing['discipline']) ?></span>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3"><?= esc($drawing['title']) ?></h5>

                <div class="mb-4">
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Current Revision</span>
                        <span class="fw-bold text-dark">Rev <?= esc($drawing['current_revision']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Status</span>
                        <span class="badge bg-<?= $drawing['status'] === 'Current' ? 'success' : 'secondary' ?>-subtle text-<?= $drawing['status'] === 'Current' ? 'success' : 'secondary' ?>">
                            <?= esc($drawing['status']) ?>
                        </span>
                    </div>
                </div>

                <hr>

                <!-- Soft Delete Action -->
                <form action="<?= site_url("drawings/{$drawing['id']}/delete") ?>" method="POST" onsubmit="return confirm('Delete this drawing entirely? This cannot be undone.');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                        <i class="fa-solid fa-trash me-1"></i> Delete Master Drawing
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Revision History List -->
    <div class="col-md-7 col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Revision History</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadRevisionModal">
                <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload New Revision
            </button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="list-group list-group-flush">
                <?php if (empty($revisions)): ?>
                    <div class="list-group-item text-center py-4 text-muted">
                        No revisions found.
                    </div>
                <?php else: ?>
                    <?php foreach($revisions as $index => $rev): ?>
                    <div class="list-group-item p-3 <?= $index === 0 ? 'bg-light' : '' ?>">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <h6 class="mb-0 fw-bold <?=$index===0 ? 'text-primary' : ''?>">Rev <?= esc($rev['revision_no']) ?>
                                    <?php if($index === 0): ?> <span class="badge bg-success ms-1" style="font-size:0.6rem;">LATEST</span> <?php endif; ?>
                                </h6>
                                <div class="small text-muted mb-0"><i class="fa-regular fa-calendar me-1"></i><?= date('M d, Y', strtotime($rev['revision_date'])) ?></div>
                            </div>
                            <div class="col-md-5">
                                <p class="mb-0 small text-wrap"><?= esc($rev['notes']) ?: '<i class="text-muted">No notes provided</i>' ?></p>
                                <div class="small text-muted mt-1"><i class="fa-regular fa-user me-1"></i><?= esc($rev['uploader_name'] ?? 'Unknown') ?></div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <?php if($rev['filepath']): ?>
                                    <div class="d-flex gap-2 justify-content-md-end">
                                        <a href="<?= base_url('uploads/' . esc($rev['filepath'])) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-regular fa-eye me-1"></i> View
                                        </a>
                                        <a href="<?= base_url('uploads/' . esc($rev['filepath'])) ?>" download class="btn btn-sm btn-outline-secondary">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">No file</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Upload Revision Modal -->
<div class="modal fade" id="uploadRevisionModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url("drawings/{$drawing['id']}/revisions") ?>" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Upload New Revision</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small">
                    <i class="fa-solid fa-info-circle me-1"></i> Uploading a new revision will mark all previous revisions as superseded.
                </div>
                
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Revision No. / Code</label>
                        <input type="text" name="revision_no" class="form-control" placeholder="e.g. 01, B" required>
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
                    <label class="form-label small fw-bold">Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Describe what changed in this revision..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Upload
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
