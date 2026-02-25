<?php
// app/Views/projects/tabs/files_inline.php
// Project Files — AJAX upload + file list inside the project workspace "Files" tab
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="input-group" style="max-width:240px;">
        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
        <input type="text" id="fileSearch" class="form-control border-start-0" placeholder="Search files…">
    </div>
    <button class="btn btn-sm btn-primary" id="btnUploadFile">
        <i class="fa-solid fa-upload me-1"></i>Upload File
    </button>
</div>

<!-- Upload area (hidden by default) -->
<div id="uploadArea" class="border rounded-3 p-3 mb-3 bg-light d-none">
    <form id="uploadForm" enctype="multipart/form-data">
        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-semibold">File <span class="text-danger">*</span></label>
                <input type="file" name="file" class="form-control form-control-sm" required>
                <div class="form-text">Max 20 MB</div>
            </div>
            <div class="col-md-5">
                <label class="form-label small fw-semibold">Description</label>
                <input type="text" name="description" class="form-control form-control-sm" placeholder="Optional description">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success btn-sm w-100">
                    <i class="fa-solid fa-cloud-arrow-up me-1"></i>Upload
                </button>
            </div>
        </div>
        <div id="uploadMsg" class="small mt-2"></div>
    </form>
</div>

<!-- File grid -->
<div id="fileGrid" class="row g-3">
    <div class="col-12 text-center text-muted py-4" id="fileLoading">
        <div class="spinner-border spinner-border-sm"></div> Loading files…
    </div>
</div>

<script>
(function() {
    const projectId  = <?= (int)($project['id'] ?? 0) ?>;
    const CSRF_NAME  = '<?= csrf_token() ?>';
    let   CSRF_HASH  = '<?= csrf_hash() ?>';
    const uploadsUrl = '<?= base_url('uploads/project_files/') ?>';

    const mimeIcons = {
        'image':   'fa-file-image text-info',
        'pdf':     'fa-file-pdf text-danger',
        'word':    'fa-file-word text-primary',
        'excel':   'fa-file-excel text-success',
        'zip':     'fa-file-zipper text-warning',
        'default': 'fa-file text-secondary',
    };

    function getIcon(mime) {
        if (!mime) return mimeIcons.default;
        if (mime.startsWith('image/'))         return mimeIcons.image;
        if (mime === 'application/pdf')         return mimeIcons.pdf;
        if (mime.includes('word'))              return mimeIcons.word;
        if (mime.includes('excel') || mime.includes('sheet')) return mimeIcons.excel;
        if (mime.includes('zip') || mime.includes('rar'))     return mimeIcons.zip;
        return mimeIcons.default;
    }

    function fmtSize(b) {
        if (b >= 1048576) return (b/1048576).toFixed(1)+' MB';
        if (b >= 1024)    return (b/1024).toFixed(0)+' KB';
        return b+' B';
    }

    function timeAgo(dt) {
        if (!dt) return '';
        const d = Math.floor((Date.now() - new Date(dt)) / 1000);
        if (d < 60) return 'just now';
        if (d < 3600) return Math.floor(d/60)+'m ago';
        if (d < 86400) return Math.floor(d/3600)+'h ago';
        return Math.floor(d/86400)+'d ago';
    }

    function renderFile(f) {
        const isImage   = (f.mime_type || '').startsWith('image/');
        const thumb     = isImage ? `<img src="<?= base_url('uploads/project_files/') ?>${f.stored_name}" class="card-img-top" style="height:100px;object-fit:cover;" alt="">` : '';
        const iconClass = getIcon(f.mime_type);

        return `<div class="col-sm-6 col-lg-4 col-xl-3 file-card" data-name="${(f.name||'').toLowerCase()}">
          <div class="card border h-100" style="border-radius:10px;">
            ${thumb || `<div class="p-3 text-center" style="font-size:32px;"><i class="fa-solid ${iconClass}"></i></div>`}
            <div class="card-body p-2">
              <div class="fw-semibold text-truncate small" title="${f.name}">${f.name}</div>
              <div class="text-muted" style="font-size:10px;">${fmtSize(f.size||0)} · ${timeAgo(f.created_at)} · ${f.uploader_name||'System'}</div>
              ${f.description ? `<div class="text-muted small mt-1 text-truncate">${f.description}</div>` : ''}
            </div>
            <div class="card-footer bg-transparent d-flex gap-1 py-1 px-2 border-top-0">
              <a href="<?= site_url('files/') ?>${f.id}/download" target="_blank"
                 class="btn btn-xs btn-outline-primary flex-grow-1" download="${f.name}">
                <i class="fa-solid fa-download"></i>
              </a>
              <button class="btn btn-xs btn-outline-danger btn-del-file" data-id="${f.id}">
                <i class="fa-solid fa-trash"></i>
              </button>
            </div>
          </div>
        </div>`;
    }

    function loadFiles() {
        fetch(`<?= site_url('projects/') ?>${projectId}/files`, {
            headers:{'X-Requested-With':'XMLHttpRequest'}
        })
        .then(r => r.json())
        .then(files => {
            document.getElementById('fileLoading').style.display='none';
            const grid = document.getElementById('fileGrid');
            grid.innerHTML = files.length ? files.map(renderFile).join('') :
              '<div class="col-12 text-center text-muted py-4"><i class="fa-solid fa-folder-open fa-2x opacity-40 mb-2"></i><p>No files yet.</p></div>';
            bindDeleteBtns();
        });
    }

    function bindDeleteBtns() {
        document.querySelectorAll('.btn-del-file').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!confirm('Delete this file?')) return;
                const id = this.dataset.id;
                fetch(`<?= site_url('files/') ?>${id}/delete`, {
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
                    body: JSON.stringify({[CSRF_NAME]:CSRF_HASH})
                }).then(r=>r.json()).then(d => {
                    if (d.success) this.closest('.file-card').remove();
                });
            });
        });
    }

    // Upload toggle
    document.getElementById('btnUploadFile').addEventListener('click', () => {
        document.getElementById('uploadArea').classList.toggle('d-none');
    });

    // Upload submit
    document.getElementById('uploadForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type=submit]');
        btn.disabled = true;
        btn.textContent = 'Uploading…';
        const fd = new FormData(this);
        const r  = await fetch(`<?= site_url('projects/') ?>${projectId}/files/upload`, {
            method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}
        });
        const d  = await r.json();
        btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up me-1"></i>Upload';
        const msg = document.getElementById('uploadMsg');
        if (d.success) {
            msg.className = 'small text-success'; msg.textContent = 'File uploaded.';
            this.reset();
            document.getElementById('fileLoading').style.display='';
            loadFiles();
            setTimeout(() => document.getElementById('uploadArea').classList.add('d-none'), 1500);
        } else {
            msg.className = 'small text-danger'; msg.textContent = d.message || 'Upload failed.';
        }
    });

    // Search
    document.getElementById('fileSearch').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.file-card').forEach(c => {
            c.style.display = (c.dataset.name||'').includes(q) ? '' : 'none';
        });
    });

    loadFiles();
})();
</script>
<style>.btn-xs{padding:2px 6px;font-size:11px;}</style>
