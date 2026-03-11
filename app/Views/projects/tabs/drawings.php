<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Project Drawings & Blueprint Revisions</h6>
                <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#uploadDrawingModal">
                    <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Drawing
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">No.</th>
                                <th>Discipline</th>
                                <th>Title</th>
                                <th>Revision</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($drawings_list)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">No drawings uploaded yet.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($drawings_list as $draw): ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?= esc($draw['drawing_number']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= esc($draw['discipline']) ?></span></td>
                                <td class="fw-semibold"><?= esc($draw['title']) ?></td>
                                <td>
                                    <span class="badge bg-info text-white">Rev 0</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= site_url("drawings/view/{$draw['id']}") ?>" class="btn btn-sm btn-outline-dark">
                                        <i class="fa-solid fa-eye me-1"></i> View & Pin
                                    </a>
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

<!-- Modal: Upload Drawing -->
<div class="modal fade" id="uploadDrawingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Upload Drawing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url("drawings/store/{$project['id']}") ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body py-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Drawing Number</label>
                            <input type="text" name="drawing_number" class="form-control" required placeholder="A-101">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Discipline</label>
                            <select name="discipline" class="form-select">
                                <option value="Architectural">Architectural</option>
                                <option value="Structural">Structural</option>
                                <option value="Mechanical">Mechanical</option>
                                <option value="Electrical">Electrical</option>
                                <option value="Plumbing">Plumbing</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Drawing Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="First Floor Floor Plan">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Drawing File (PDF/Image)</label>
                            <input type="file" name="drawing_file" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-dark px-4">Upload & Publish</button>
                </div>
            </form>
        </div>
    </div>
</div>
