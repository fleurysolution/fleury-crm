<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
        <h5 class="card-title mb-0 fw-semibold"><i class="fa-solid fa-images text-primary me-2"></i>Site Progress Photos</h5>
        <div class="d-flex align-items-center gap-2">
            <!-- Date Filters -->
            <div class="d-flex align-items-center gap-1 me-3">
                <input type="date" id="photoDateFrom" class="form-control form-control-sm" style="width: auto;" title="From Date">
                <span class="text-muted small">to</span>
                <input type="date" id="photoDateTo" class="form-control form-control-sm" style="width: auto;" title="To Date">
                <button class="btn btn-sm btn-light border" onclick="filterPhotos(<?= $project['id'] ?>)">
                    <i class="fa-solid fa-filter me-1"></i>Filter
                </button>
            </div>

            <!-- File Upload Input (hidden) -->
            <input type="file" id="sitePhotoUpload" class="d-none" multiple accept="image/*" onchange="showUploadModal(<?= $project['id'] ?>)">
            
            <div class="btn-group">
                <button class="btn btn-sm btn-primary" onclick="document.getElementById('sitePhotoUpload').click()">
                    <i class="fa-solid fa-plus me-1"></i>Add Photos
                </button>
                <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                    <li><a class="dropdown-item py-2" href="#" onclick="generateFilteredPDF(<?= $project['id'] ?>)"><i class="fa-solid fa-file-pdf me-2 text-danger"></i>Generate PDF Report</a></li>
                    <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#distributeReportModal"><i class="fa-solid fa-paper-plane me-2 text-primary"></i>Distribute Report</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body bg-light">
        <div class="row g-3" id="photoGallery">
            <?php if(empty($photos)): ?>
                <div class="col-12 text-center text-muted py-5" id="noPhotosMsg">
                    <i class="fa-regular fa-image fa-3x mb-3 opacity-25"></i>
                    <p>No site progress photos uploaded yet.</p>
                </div>
            <?php else: ?>
                <?php foreach($photos as $photo): ?>
                    <div class="col-6 col-md-4 col-lg-3 photo-card" id="photo-<?= $photo['id'] ?>">
                        <div class="card h-100 border-0 shadow-sm position-relative">
                            <a href="<?= base_url($photo['photo_path']) ?>" target="_blank">
                                <img src="<?= base_url($photo['photo_path']) ?>" class="card-img-top" style="height:200px; object-fit:cover;" alt="Progress Photo">
                            </a>
                            <div class="card-body p-2 d-flex flex-column justify-content-between">
                                <div class="small fw-semibold text-truncate mb-1" title="<?= esc($photo['title'] ?: $photo['caption']) ?>">
                                    <?= esc($photo['title'] ?: $photo['caption']) ?>
                                </div>
                                <?php if($photo['description']): ?>
                                    <div class="text-muted small text-truncate mb-2" style="font-size: 0.7rem;"><?= esc($photo['description']) ?></div>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted" style="font-size:0.7rem;"><?= date('M j, Y', strtotime($photo['created_at'])) ?></span>
                                    <button class="btn btn-sm btn-link text-danger p-0" onclick="deleteSitePhoto(<?= $project['id'] ?>, <?= $photo['id'] ?>)" title="Delete Photo">
                                        <i class="fa-solid fa-trash fa-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Photo Upload Modal -->
<div class="modal fade" id="uploadPhotosModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="modal-title fw-semibold">Photo Upload & Context</h6>
                <button type="button" id="startWebcamBtn" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-camera me-1"></i>Take Photo</button>
            </div>
            <div class="modal-body bg-light">
                <!-- Webcam Container -->
                <div id="webcamSection" class="d-none mb-4">
                    <div class="card border-0 shadow-sm overflow-hidden bg-black position-relative" style="aspect-ratio: 16/9;">
                        <video id="siteWebcamVideo" class="w-100 h-100" autoplay playsinline style="object-fit: cover;"></video>
                        <canvas id="siteWebcamCanvas" class="d-none"></canvas>
                        <div class="position-absolute bottom-0 start-0 w-100 p-3 d-flex justify-content-center gap-2 bg-dark bg-opacity-50">
                            <button type="button" id="siteSnapBtn" class="btn btn-primary px-4"><i class="fa-solid fa-camera me-2"></i>Snap Photo</button>
                            <button type="button" id="stopWebcamBtn" class="btn btn-light">Close Camera</button>
                        </div>
                    </div>
                </div>

                <div id="uploadPreviewContainer" class="row g-3">
                    <!-- Previews go here -->
                </div>
            </div>
            <div class="modal-footer border-top py-3">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmUploadBtn" class="btn btn-sm btn-primary px-4" onclick="executeUpload(<?= $project['id'] ?>)">Upload All</button>
            </div>
        </div>
    </div>
</div>

<!-- Distribute Report Modal -->
<div class="modal fade" id="distributeReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-semibold">Distribute Progress Report</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="distributeReportForm" onsubmit="sendReport(event, <?= $project['id'] ?>)">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Select Stakeholders (Project Members)</label>
                        <div class="d-flex flex-column gap-1 border rounded p-2 bg-light" style="max-height: 150px; overflow-y: auto;">
                            <?php if(!empty($members)): ?>
                                <?php foreach($members as $m): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="recipients[]" value="<?= esc($m['email']) ?>" id="mbr-<?= $m['id'] ?>">
                                        <label class="form-check-label small" for="mbr-<?= $m['id'] ?>">
                                            <?= esc($m['name']) ?> (<?= esc($m['role']) ?>)
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted small">No members found.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Additional Emails (Comma separated)</label>
                        <input type="text" name="custom_emails" class="form-control form-control-sm" placeholder="e.g. client@example.com, manager@site.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Message (Optional)</label>
                        <textarea name="message" class="form-control form-control-sm" rows="3" placeholder="Add a short note..."></textarea>
                    </div>
                    <div class="alert alert-info py-2 small mb-0">
                        <i class="fa-solid fa-info-circle me-1"></i> The PDF report will be generated based on your current date filters and attached to the email.
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="sendReportBtn" class="btn btn-sm btn-primary px-4">Send Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let selectedFiles = [];
let capturedImages = []; // Array of { data: base64, title: '', description: '' }
let webcamStream = null;

function showUploadModal(projectId) {
    const input = document.getElementById('sitePhotoUpload');
    if (input.files.length === 0) return;
    
    selectedFiles = Array.from(input.files);
    renderPreviews();
    
    const modal = new bootstrap.Modal(document.getElementById('uploadPhotosModal'));
    modal.show();
}

function renderPreviews() {
    const container = document.getElementById('uploadPreviewContainer');
    container.innerHTML = '';
    
    // File Input Files
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            addPreviewCard(e.target.result, `file-${index}`, true);
        };
        reader.readAsDataURL(file);
    });

    // Captured Webcam Images
    capturedImages.forEach((img, index) => {
        addPreviewCard(img.data, `cap-${index}`, false, index);
    });
}

function addPreviewCard(src, id, isFile, capturedIndex = null) {
    const container = document.getElementById('uploadPreviewContainer');
    const html = `
        <div class="col-md-6" id="up-preview-${id}">
            <div class="card h-100 border shadow-sm">
                <div class="position-relative">
                    <img src="${src}" class="card-img-top" style="height: 140px; object-fit: cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 opacity-75" onclick="removePreview('${id}', ${isFile}, ${capturedIndex})">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
                <div class="card-body p-2">
                    <input type="text" class="form-control form-control-sm mb-1 fw-bold" placeholder="Title..." name="${isFile ? 'file_titles[]' : 'cap_titles[]'}">
                    <textarea class="form-control form-control-sm" rows="2" placeholder="Description..." name="${isFile ? 'file_descriptions[]' : 'cap_descriptions[]'}"></textarea>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function removePreview(id, isFile, index) {
    document.getElementById(`up-preview-${id}`).remove();
    if (isFile) {
        // We can't easily remove from FileList, but we can manage a local array
        const idx = parseInt(id.split('-')[1]);
        selectedFiles.splice(idx, 1);
    } else {
        capturedImages.splice(index, 1);
    }
    renderPreviews();
}

// Webcam Logic
document.getElementById('startWebcamBtn').onclick = async () => {
    try {
        webcamStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        document.getElementById('siteWebcamVideo').srcObject = webcamStream;
        document.getElementById('webcamSection').classList.remove('d-none');
        document.getElementById('startWebcamBtn').classList.add('d-none');
    } catch (err) {
        alert("Camera access denied: " + err.message);
    }
};

document.getElementById('stopWebcamBtn').onclick = () => {
    if (webcamStream) {
        webcamStream.getTracks().forEach(t => t.stop());
        webcamStream = null;
    }
    document.getElementById('webcamSection').classList.add('d-none');
    document.getElementById('startWebcamBtn').classList.remove('d-none');
};

document.getElementById('siteSnapBtn').onclick = () => {
    const video = document.getElementById('siteWebcamVideo');
    const canvas = document.getElementById('siteWebcamCanvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    
    const data = canvas.toDataURL('image/jpeg');
    capturedImages.push({ data: data });
    renderPreviews();
};

function executeUpload(projectId) {
    const btn = document.getElementById('confirmUploadBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Uploading...';
    btn.disabled = true;

    const fd = new FormData();
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    
    // Process File Uploads
    const fileTitles = Array.from(document.querySelectorAll('input[name="file_titles[]"]')).map(i => i.value);
    const fileDescs = Array.from(document.querySelectorAll('textarea[name="file_descriptions[]"]')).map(i => i.value);
    selectedFiles.forEach((file, i) => {
        fd.append('photos[]', file);
        fd.append('file_titles[]', fileTitles[i] || '');
        fd.append('file_descriptions[]', fileDescs[i] || '');
    });

    // Process Webcam Snapshots
    const capTitles = Array.from(document.querySelectorAll('input[name="cap_titles[]"]')).map(i => i.value);
    const capDescs = Array.from(document.querySelectorAll('textarea[name="cap_descriptions[]"]')).map(i => i.value);
    capturedImages.forEach((img, i) => {
        fd.append('webcam_photos[]', img.data);
        fd.append('cap_titles[]', capTitles[i] || '');
        fd.append('cap_descriptions[]', capDescs[i] || '');
    });

    fetch(`/staging/public/projects/${projectId}/upload-photos`, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            if (d.success) {
                location.reload();
            } else {
                alert(d.message || 'Error uploading photos.');
            }
        });
}

function filterPhotos(projectId) {
    const from = document.getElementById('photoDateFrom').value;
    const to = document.getElementById('photoDateTo').value;
    
    const cards = document.querySelectorAll('.photo-card');
    cards.forEach(card => {
        const dateStr = card.querySelector('.text-muted').innerText;
        const cardDate = new Date(dateStr);
        cardDate.setHours(0,0,0,0);
        
        let show = true;
        if (from && cardDate < new Date(from)) show = false;
        if (to && cardDate > new Date(to)) show = false;
        
        if (show) card.classList.remove('d-none');
        else card.classList.add('d-none');
    });
}

function generateFilteredPDF(projectId) {
    const from = document.getElementById('photoDateFrom').value;
    const to = document.getElementById('photoDateTo').value;
    let url = `/staging/public/projects/${projectId}/progress-report`;
    if (from || to) {
        url += `?start_date=${from}&end_date=${to}`;
    }
    window.open(url, '_blank');
}

function sendReport(e, projectId) {
    e.preventDefault();
    const btn = document.getElementById('sendReportBtn');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Sending...';
    btn.disabled = true;

    const from = document.getElementById('photoDateFrom').value;
    const to = document.getElementById('photoDateTo').value;
    const fd = new FormData(e.target);
    fd.append('start_date', from);
    fd.append('end_date', to);
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    fetch(`/staging/public/projects/${projectId}/distribute-report`, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
            btn.innerHTML = 'Send Report';
            btn.disabled = false;
            if (d.success) {
                alert('Report distributed successfully!');
                bootstrap.Modal.getInstance(document.getElementById('distributeReportModal')).hide();
            } else {
                alert(d.message || 'Error sending report.');
            }
        })
        .catch(err => {
            btn.innerHTML = 'Send Report';
            btn.disabled = false;
            alert('Sending report failed.');
        });
}

function deleteSitePhoto(projectId, photoId) {
    if (!confirm('Are you sure you want to delete this photo?')) return;
    
    fetch(`/staging/public/projects/${projectId}/delete-photo/${photoId}`, { 
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            document.getElementById(`photo-${photoId}`).remove();
        } else {
            alert(d.message || 'Error deleting photo.');
        }
    });
}
</script>
