<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Custom Hub (Objects)</h4>
            <p class="text-muted small mb-0">Create new entities like "Leads", "Assets", or "Inspections".</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addObjectModal">
            <i class="fa-solid fa-cube me-2"></i>New Custom Object
        </button>
    </div>

    <div class="row g-4">
        <?php foreach ($objects as $obj): ?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 transition-hover">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="rounded-circle bg-primary-light p-3 text-primary">
                            <i class="fa-solid fa-database fa-lg"></i>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown border-0">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                <li><a class="dropdown-item" href="<?= site_url('settings/custom-fields?object=' . $obj['name']) ?>">Manage Fields</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item text-danger" onclick="deleteObject(<?= $obj['id'] ?>)">Delete</button></li>
                            </ul>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1"><?= esc($obj['label_plural']) ?></h5>
                    <p class="text-muted small mb-3"><?= esc($obj['description']) ?: 'No description provided.' ?></p>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <span class="badge bg-success-light text-success fw-bold text-uppercase small" style="font-size: 10px;">Active</span>
                        <a href="<?= site_url('hub/' . $obj['name']) ?>" class="btn btn-sm btn-light border">View Data</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; if (empty($objects)): ?>
        <div class="col-12 text-center py-5">
            <div class="text-muted mb-3"><i class="fa-solid fa-ghost fa-3x opacity-25"></i></div>
            <h5 class="text-muted">No custom objects yet.</h5>
            <p class="text-muted small">Start by creating your first object to store custom data.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Object Modal -->
<div class="modal fade" id="addObjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">New Custom Object</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <?= form_open('settings/custom-hub/store') ?>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Label (Singular)</label>
                    <input type="text" name="label_singular" class="form-control" placeholder="e.g. Lead" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Label (Plural)</label>
                    <input type="text" name="label_plural" class="form-control" placeholder="e.g. Leads" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Describe the purpose of this object..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Object</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
    async function deleteObject(id) {
        if (confirm('Are you sure you want to delete this custom object? All related data and fields will be lost.')) {
            const r = await fetch('<?= site_url('settings/custom-hub/delete/') ?>' + id, { method: 'POST' });
            const d = await r.json();
            if (d.success) location.reload();
            else alert(d.message);
        }
    }
</script>

<style>
    .bg-primary-light { background-color: rgba(13, 110, 253, 0.1); }
    .bg-success-light { background-color: rgba(25, 135, 84, 0.1); }
    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.1)!important;
        transition: all 0.3s ease;
    }
</style>
<?= $this->endSection() ?>
