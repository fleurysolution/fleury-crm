<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
        <h5 class="card-title mb-0 fw-semibold"><i class="fa-solid fa-images text-primary me-2"></i>Site Progress Photos</h5>
        <div>
            <!-- File Upload Input -->
            <input type="file" id="sitePhotoUpload" class="d-none" multiple accept="image/*" onchange="uploadSitePhotos(<?= $project['id'] ?>)">
            
            <?php if(!empty($photos)): ?>
                <a href="<?= site_url("projects/{$project['id']}/progress-report") ?>" class="btn btn-sm btn-outline-danger me-2" target="_blank">
                    <i class="fa-solid fa-file-pdf me-1"></i>Generate PDF
                </a>
            <?php endif; ?>

            <button class="btn btn-sm btn-primary" onclick="document.getElementById('sitePhotoUpload').click()">
                <i class="fa-solid fa-upload me-1"></i>Upload Photos
            </button>
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
                                <div class="small fw-semibold text-truncate mb-1" title="<?= esc($photo['caption']) ?>"><?= esc($photo['caption']) ?></div>
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

<script>
function uploadSitePhotos(projectId) {
    const input = document.getElementById('sitePhotoUpload');
    if (input.files.length === 0) return;

    // Show a loading state on the button
    const btn = input.nextElementSibling;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Uploading...';
    btn.disabled = true;

    const fd = new FormData();
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    for (let i = 0; i < input.files.length; i++) {
        fd.append('photos[]', input.files[i]);
    }

    fetch(`/staging/public/projects/${projectId}/upload-photos`, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            
            if (d.success) {
                // If the "No photos" message is present, remove it
                const noMsg = document.getElementById('noPhotosMsg');
                if (noMsg) noMsg.remove();

                // Append new photos to the grid
                const gallery = document.getElementById('photoGallery');
                d.photos.forEach(p => {
                    const dateFormatted = new Date(p.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    const html = `
                        <div class="col-6 col-md-4 col-lg-3 photo-card" id="photo-${p.id}">
                            <div class="card h-100 border-0 shadow-sm position-relative">
                                <a href="/staging/public/${p.photo_path}" target="_blank">
                                    <img src="/staging/public/${p.photo_path}" class="card-img-top" style="height:200px; object-fit:cover;" alt="Progress Photo">
                                </a>
                                <div class="card-body p-2 d-flex flex-column justify-content-between">
                                    <div class="small fw-semibold text-truncate mb-1" title="${p.caption}">${p.caption}</div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted" style="font-size:0.7rem;">${dateFormatted}</span>
                                        <button class="btn btn-sm btn-link text-danger p-0" onclick="deleteSitePhoto(${projectId}, ${p.id})" title="Delete Photo">
                                            <i class="fa-solid fa-trash fa-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    // Insert at beginning of gallery
                    gallery.insertAdjacentHTML('afterbegin', html);
                });
                
                // Add Generate PDF button dynamically if not exists
                if (!document.querySelector('a[href*="progress-report"]')) {
                    const btnHtml = `<a href="/staging/public/projects/${projectId}/progress-report" class="btn btn-sm btn-outline-danger me-2" target="_blank"><i class="fa-solid fa-file-pdf me-1"></i>Generate PDF</a>`;
                    document.getElementById('sitePhotoUpload').insertAdjacentHTML('afterend', btnHtml);
                }

                // Reset input
                input.value = '';
            } else {
                alert(d.message || 'Error uploading photos.');
            }
        })
        .catch(err => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            alert('Upload failed.');
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
