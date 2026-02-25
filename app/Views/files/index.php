<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<div class="content-wrapper" style="min-height:100vh;background:#f8f9fa;">

<div class="content-header px-4 pt-4 pb-0">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h1 class="h4 fw-bold mb-0"><i class="fa-solid fa-folder-open me-2 text-primary"></i>File Manager</h1>
            <p class="text-muted small mb-0 mt-1">All project files in one place</p>
        </div>
    </div>
</div>

<div class="content px-4 pt-3 pb-4">
    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
        <div class="card-body py-2 px-3 d-flex flex-wrap gap-2 align-items-center">
            <input type="text" id="globalSearch" class="form-control form-control-sm" style="max-width:220px;" placeholder="Search by file name…">
            <select id="filterProject" class="form-select form-select-sm" style="max-width:200px;">
                <option value="">All Projects</option>
                <?php foreach ($projects as $p): ?>
                <option value="<?= (int)$p['id'] ?>"><?= esc($p['title']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterType" class="form-select form-select-sm" style="max-width:160px;">
                <option value="">All Types</option>
                <option value="image">Images</option>
                <option value="pdf">PDFs</option>
                <option value="doc">Documents</option>
                <option value="other">Other</option>
            </select>
            <span class="ms-auto text-muted small" id="fileCount"></span>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="table-responsive">
        <table class="table table-hover align-middle small mb-0" id="filesTable">
            <thead class="table-light">
                <tr>
                    <th>File</th>
                    <th>Project</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Uploaded By</th>
                    <th>Date</th>
                    <th style="width:90px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($files as $f):
                $mimeGroup = '';
                if (str_starts_with($f['mime_type']??'', 'image/')) $mimeGroup = 'image';
                elseif ($f['mime_type'] === 'application/pdf')       $mimeGroup = 'pdf';
                elseif (str_contains($f['mime_type']??'', 'word') || str_contains($f['mime_type']??'', 'document')) $mimeGroup = 'doc';
                else $mimeGroup = 'other';

                $iconCls = match(true) {
                    str_starts_with($f['mime_type']??'', 'image/') => 'fa-file-image text-info',
                    $f['mime_type'] === 'application/pdf'          => 'fa-file-pdf text-danger',
                    str_contains($f['mime_type']??'','word')       => 'fa-file-word text-primary',
                    str_contains($f['mime_type']??'','excel') || str_contains($f['mime_type']??'','sheet') => 'fa-file-excel text-success',
                    str_contains($f['mime_type']??'','zip')        => 'fa-file-zipper text-warning',
                    default => 'fa-file text-secondary',
                };

                $sizeStr = $f['size'] >= 1048576
                    ? round($f['size']/1048576, 1).' MB'
                    : ($f['size'] >= 1024 ? round($f['size']/1024).' KB' : $f['size'].' B');
            ?>
            <tr class="file-row" data-project="<?= (int)$f['project_id'] ?>" data-type="<?= $mimeGroup ?>">
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid <?= $iconCls ?> fa-lg"></i>
                        <div>
                            <div class="fw-semibold text-truncate" style="max-width:200px;" title="<?= esc($f['name']) ?>"><?= esc($f['name']) ?></div>
                            <?php if ($f['description']??null): ?>
                            <div class="text-muted" style="font-size:10px;"><?= esc($f['description']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td><?= esc($f['project_name'] ?? '—') ?></td>
                <td class="text-muted"><?= esc($f['mime_type'] ?? '—') ?></td>
                <td><?= $sizeStr ?></td>
                <td><?= esc($f['uploader_name'] ?? '—') ?></td>
                <td class="text-muted"><?= $f['created_at'] ? date('d M Y', strtotime($f['created_at'])) : '—' ?></td>
                <td>
                    <a href="<?= site_url('files/'.(int)$f['id'].'/download') ?>" target="_blank"
                       class="btn btn-xs btn-outline-primary me-1" title="Download"><i class="fa-solid fa-download"></i></a>
                    <button class="btn btn-xs btn-outline-danger btn-del-file" data-id="<?= (int)$f['id'] ?>" title="Delete">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($files)): ?>
            <tr><td colspan="7" class="text-center text-muted py-5">
                <i class="fa-solid fa-folder-open fa-2x opacity-30 mb-2"></i><br>No files found.
            </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
</div>

<style>.btn-xs{padding:2px 7px;font-size:11px;}</style>

<script>
const CSRF_NAME = '<?= csrf_token() ?>';
const CSRF_HASH = '<?= csrf_hash() ?>';

function applyFilters() {
    const q    = document.getElementById('globalSearch').value.toLowerCase();
    const proj = document.getElementById('filterProject').value;
    const type = document.getElementById('filterType').value;
    let vis    = 0;
    document.querySelectorAll('.file-row').forEach(r => {
        const name    = r.querySelector('td:first-child').textContent.toLowerCase();
        const matchQ  = !q    || name.includes(q);
        const matchP  = !proj || r.dataset.project === proj;
        const matchT  = !type || r.dataset.type === type;
        const show    = matchQ && matchP && matchT;
        r.style.display = show ? '' : 'none';
        if (show) vis++;
    });
    document.getElementById('fileCount').textContent = vis === 1 ? '1 file' : vis + ' files';
}

['globalSearch','filterProject','filterType'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', applyFilters);
    document.getElementById(id)?.addEventListener('change', applyFilters);
});

// Initial count
applyFilters();

// Delete
document.querySelectorAll('.btn-del-file').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Delete this file?')) return;
        fetch(`<?= site_url('files/') ?>${this.dataset.id}/delete`, {
            method:'POST',
            headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
            body: JSON.stringify({[CSRF_NAME]:CSRF_HASH})
        }).then(r=>r.json()).then(d=>{
            if (d.success) { this.closest('tr').remove(); applyFilters(); }
        });
    });
});
</script>

<?= $this->endSection() ?>
