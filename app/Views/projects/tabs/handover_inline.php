<?php
$assetModel = new \App\Models\AssetRegistryModel();
$assets = $assetModel->where('project_id', $project['id'])->findAll();
?>

<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card border-0 shadow-sm bg-dark text-white p-4" style="background: linear-gradient(135deg, #1a1a1a 0%, #3d3d3d 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Digital Handover Vault</h4>
                    <p class="text-white-50 mb-0">Project close-out assets, warranty information, and O&M manuals.</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" onclick="openAssetModal()">
                        <i class="fa-solid fa-plus me-1"></i> Register Asset
                    </button>
                    <a href="<?= site_url("projects/{$project['id']}/handover/print-qr") ?>" class="btn btn-outline-light">
                        <i class="fa-solid fa-qrcode me-1"></i> Print QR Labels
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0">Asset Registry & Warranty Vault</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 small fw-bold text-uppercase">Asset ID / Name</th>
                            <th class="small fw-bold text-uppercase">Category</th>
                            <th class="small fw-bold text-uppercase">Manufacturer / Model</th>
                            <th class="small fw-bold text-uppercase">Warranty Expiry</th>
                            <th class="small fw-bold text-uppercase">Status</th>
                            <th class="pe-3 text-end small fw-bold text-uppercase">Docs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($assets)): ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">No assets registered in the vault.</td></tr>
                        <?php else: ?>
                            <?php foreach ($assets as $asset): ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-light rounded p-2 text-center" style="width: 48px; height: 48px;">
                                                <i class="fa-solid fa-box-open text-primary fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-primary"><?= esc($asset['asset_name']) ?></div>
                                                <div class="text-muted small"><?= esc($asset['asset_tag']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= esc($asset['category']) ?></span></td>
                                    <td class="small">
                                        <div class="fw-bold"><?= esc($asset['manufacturer']) ?></div>
                                        <div><?= esc($asset['model_number']) ?></div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold <?= (strtotime($asset['warranty_expiry']) < time()) ? 'text-danger' : 'text-success' ?>">
                                            <?= date('M d, Y', strtotime($asset['warranty_expiry'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <div class="btn-group">
                                            <a href="<?= $asset['manual_url'] ?>" class="btn btn-sm btn-outline-secondary" target="_blank" title="View Manual">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-secondary" title="View Warranty Cert">
                                                <i class="fa-solid fa-certificate"></i>
                                            </button>
                                        </div>
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

<!-- Asset Modal -->
<div class="modal fade" id="assetModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="assetForm" class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold">Register New Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Asset Name</label>
                    <input type="text" name="asset_name" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-bold">Category</label>
                        <select name="category" class="form-select">
                            <option>Mechanical</option>
                            <option>Electrical</option>
                            <option>Plumbing</option>
                            <option>Fire Protection</option>
                            <option>Architecture</option>
                        </select>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-bold">Asset Tag / ID</label>
                        <input type="text" name="asset_tag" class="form-control" placeholder="e.g. AHU-01">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-bold">Manufacturer</label>
                        <input type="text" name="manufacturer" class="form-control">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-bold">Model Number</label>
                        <input type="text" name="model_number" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Warranty Expiry Date</label>
                    <input type="date" name="warranty_expiry" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">O&M Manual (Link/Upload)</label>
                    <input type="text" name="manual_url" class="form-control" placeholder="https://...">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="fa-solid fa-save"></i> Save to Vault
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const assetModal = new bootstrap.Modal(document.getElementById('assetModal'));
    function openAssetModal() { assetModal.show(); }

    document.getElementById('assetForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append(CSRF_NAME, CSRF_TOKEN);
        fetch(`<?= site_url("projects/{$project['id']}/handover/save") ?>`, {
            method: 'POST',
            body: fd
        }).then(r => r.json()).then(res => {
            if(res.success) { location.reload(); }
        });
    });
</script>
