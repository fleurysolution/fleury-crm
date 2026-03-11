<?php
$plModel = new \App\Models\PunchListModel();
$sdModel = new \App\Models\SiteDiaryModel();
$punchItems = $plModel->forProject($project['id']);
$diaries    = $sdModel->forProject($project['id']);

// Users list for assignment dropdown
$users = (new \App\Models\UserModel())->findAll();
?>

<div class="row g-4">
    <!-- Left Column: Punch List -->
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Punch List Items</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newPunchModal">
                <i class="fa-solid fa-plus me-1"></i> Add Punch Item
            </button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3" style="width: 100px;">Item No.</th>
                            <th>Description</th>
                            <th>Location</th>
                            <th>Assignee</th>
                            <th>Due Date</th>
                            <th class="pe-3">Status</th>
                            <th>Attachment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($punchItems)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-clipboard-check fs-3 d-block mb-3"></i>
                                    No punch list items logged yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($punchItems as $pi): ?>
                                <tr>
                                    <td class="ps-3 fw-bold text-muted"><?= esc($pi['item_no']) ?></td>
                                    <td class="fw-medium text-wrap" style="max-width: 250px;"><?= esc($pi['description']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= esc($pi['location']) ?: 'Site-wide' ?></span></td>
                                    <td>
                                        <?php if ($pi['assignee_name']): ?>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-circle bg-primary text-white" style="width: 24px; height: 24px; font-size: 0.7rem;"><?= substr($pi['assignee_name'], 0, 1) ?></div>
                                                <span class="small"><?= esc($pi['assignee_name']) ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted small">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small <?= (strtotime($pi['due_date']) < time() && $pi['status'] === 'Open') ? 'text-danger fw-bold' : 'text-muted' ?>">
                                        <?= $pi['due_date'] ? date('M d, Y', strtotime($pi['due_date'])) : '-' ?>
                                    </td>
                                    <td class="pe-3">
                                        <form action="<?= site_url("punch/{$pi['id']}/status") ?>" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <select name="status" class="form-select form-select-sm border-0 fw-bold shadow-none <?= $pi['status']==='Resolved'?'text-success':($pi['status']==='Closed'?'text-secondary':'text-primary') ?>" onchange="this.form.submit()">
                                                <option value="Open" <?= $pi['status'] === 'Open' ? 'selected' : '' ?>>Open</option>
                                                <option value="Resolved" <?= $pi['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                                <option value="Closed" <?= $pi['status'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <?php if ($pi['attachment_path']): ?>
                                            <a href="<?= base_url($pi['attachment_path']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary p-1">
                                                <i class="fa-solid fa-paperclip"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action="<?= site_url("punch/{$pi['id']}/delete") ?>" method="POST" onsubmit="return confirm('Delete this punch item?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm text-danger border-0"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Site Diaries -->
    <div class="col-lg-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Daily Site Logs</h5>
            <form action="<?= site_url("projects/{$project['id']}/diaries") ?>" method="POST">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-dark btn-sm">
                    <i class="fa-solid fa-book-open-reader me-1"></i> Draft New Log
                </button>
            </form>
        </div>

        <?php if (empty($diaries)): ?>
            <div class="card border-0 shadow-sm p-4 text-center text-muted">
                <i class="fa-solid fa-cloud-sun fs-3 d-block mb-3"></i>
                No daily logs have been submitted yet.
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush shadow-sm rounded-3">
                <?php foreach ($diaries as $diary): ?>
                    <a href="<?= site_url("field/diary/{$diary['id']}") ?>" class="list-group-item list-group-item-action p-3 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1">
                                    Report: <?= date('D, M d Y', strtotime($diary['report_date'])) ?>
                                </h6>
                                <div class="small text-muted">By: <?= esc($diary['creator_name']) ?></div>
                            </div>
                            <?php 
                                $badgeClass = 'bg-secondary';
                                if ($diary['status'] === 'Submitted') $badgeClass = 'bg-primary-subtle text-primary';
                                if ($diary['status'] === 'Approved') $badgeClass = 'bg-success';
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= esc($diary['status']) ?></span>
                        </div>
                        <div class="small text-muted text-truncate" style="max-width: 250px;">
                            <i class="fa-solid fa-cloud me-1"></i> <?= esc($diary['weather_conditions'] ?: 'Weather not logged') ?> - <?= esc($diary['temperature']) ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Punch Item Modal -->
<div class="modal fade" id="newPunchModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url("projects/{$project['id']}/punch") ?>" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold">Log Punch Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Item No.</label>
                        <input type="text" name="item_no" class="form-control" placeholder="PL-001" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Location</label>
                        <input type="text" name="location" class="form-control" placeholder="e.g. Master Bath">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold">Description of deficiency</label>
                    <textarea name="description" class="form-control" rows="3" required></textarea>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Assign To</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">Unassigned</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= esc($u['first_name'] . ' ' . $u['last_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Due Date</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label small fw-bold">Attachment</label>
                    <div class="d-flex flex-column gap-2">
                        <div id="webcamContainer" class="d-none border rounded bg-black position-relative" style="aspect-ratio: 4/3; overflow: hidden;">
                            <video id="webcamVideo" class="w-100 h-100" autoplay playsinline style="object-fit: cover;"></video>
                            <canvas id="webcamCanvas" class="d-none"></canvas>
                            <img id="webcamPreview" class="d-none w-100 h-100" style="object-fit: cover;">
                            <div class="position-absolute bottom-0 start-0 e-100 w-100 p-2 d-flex justify-content-center gap-2 bg-dark bg-opacity-50">
                                <button type="button" id="snapBtn" class="btn btn-sm btn-light border-0"><i class="fa-solid fa-camera me-1"></i>Snap</button>
                                <button type="button" id="retakeBtn" class="btn btn-sm btn-outline-light border-0 d-none">Retake</button>
                                <button type="button" id="closeWebcamBtn" class="btn btn-sm btn-outline-light border-0">Cancel</button>
                            </div>
                        </div>
                        <input type="file" name="attachment" id="punchAttachment" class="form-control form-control-sm" accept="image/*">
                        <button type="button" id="openWebcamBtn" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-video me-1"></i>Use Camera</button>
                        <input type="hidden" name="webcam_image" id="webcamImageInput">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="fa-solid fa-check"></i> Save Item
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('webcamVideo');
    const canvas = document.getElementById('webcamCanvas');
    const preview = document.getElementById('webcamPreview');
    const webcamContainer = document.getElementById('webcamContainer');
    const fileInput = document.getElementById('punchAttachment');
    const webcamImageInput = document.getElementById('webcamImageInput');
    const snapBtn = document.getElementById('snapBtn');
    const retakeBtn = document.getElementById('retakeBtn');
    let stream = null;

    document.getElementById('openWebcamBtn').onclick = async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            video.srcObject = stream;
            webcamContainer.classList.remove('d-none');
            fileInput.classList.add('d-none');
            document.getElementById('openWebcamBtn').classList.add('d-none');
            preview.classList.add('d-none');
            video.classList.remove('d-none');
            snapBtn.classList.remove('d-none');
            retakeBtn.classList.add('d-none');
        } catch (err) {
            alert("Could not access camera: " + err.message);
        }
    };

    snapBtn.onclick = () => {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = canvas.toDataURL('image/jpeg');
        webcamImageInput.value = imageData;
        
        preview.src = imageData;
        preview.classList.remove('d-none');
        video.classList.add('d-none');
        snapBtn.classList.add('d-none');
        retakeBtn.classList.remove('d-none');
    };

    retakeBtn.onclick = () => {
        webcamImageInput.value = '';
        preview.classList.add('d-none');
        video.classList.remove('d-none');
        snapBtn.classList.remove('d-none');
        retakeBtn.classList.add('d-none');
    };

    function stopWebcam() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        webcamContainer.classList.add('d-none');
        fileInput.classList.remove('d-none');
        document.getElementById('openWebcamBtn').classList.remove('d-none');
        webcamImageInput.value = '';
    }

    document.getElementById('closeWebcamBtn').onclick = stopWebcam;
    
    // Stop webcam if modal is closed
    document.getElementById('newPunchModal').addEventListener('hidden.bs.modal', stopWebcam);
});
</script>
