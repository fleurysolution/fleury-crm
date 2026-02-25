<?php
// app/Views/projects/tabs/contracts_inline.php
$cModel    = new \App\Models\ContractModel();
$aModel    = new \App\Models\ContractAmendmentModel();
$contracts = $cModel->forProject($project['id']);
$totalVal  = $cModel->totalValue($project['id']);

$statusCol = ['draft'=>'secondary','active'=>'success','on_hold'=>'warning','completed'=>'info','terminated'=>'danger'];
$typeLabel = ['main'=>'Main Contract','subcontract'=>'Subcontract','supply'=>'Supply','consultant'=>'Consultant','other'=>'Other'];
?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="text-muted small">
        Total contract value: <strong class="text-dark"><?= number_format($totalVal, 2) ?></strong>
    </div>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newContractModal">
        <i class="fa-solid fa-plus me-1"></i>New Contract
    </button>
</div>

<?php if (empty($contracts)): ?>
<div class="text-center py-5 text-muted">
    <i class="fa-solid fa-file-contract fa-2x mb-2 opacity-25 d-block"></i>
    No contracts yet.
</div>
<?php else: ?>
<div class="card border-0 shadow-sm" style="border-radius:10px;overflow:hidden;">
<table class="table table-hover align-middle small mb-0">
    <thead class="table-light">
        <tr>
            <th>Number</th><th>Title</th><th>Type</th><th>Contractor</th>
            <th>Value</th><th>Status</th><th>Period</th><th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($contracts as $c):
        $amendments  = $aModel->forContract($c['id']);
        $totalChg    = $aModel->totalApprovedChange($c['id']);
        $currentVal  = $c['value'] + $totalChg;
    ?>
    <tr>
        <td><a href="<?= site_url("contracts/{$c['id']}") ?>" class="fw-semibold text-decoration-none"><?= esc($c['contract_number']) ?></a></td>
        <td><a href="<?= site_url("contracts/{$c['id']}") ?>" class="text-dark text-decoration-none"><?= esc($c['title']) ?></a></td>
        <td class="text-muted"><?= $typeLabel[$c['type']] ?? $c['type'] ?></td>
        <td class="text-muted"><?= esc($c['contractor_name'] ?? '—') ?></td>
        <td>
            <strong><?= number_format($currentVal, 2) ?></strong>
            <?php if ($totalChg != 0): ?>
            <span class="text-<?= $totalChg>0?'success':'danger' ?> small ms-1">(<?= $totalChg>0?'+':'' ?><?= number_format($totalChg,2) ?>)</span>
            <?php endif; ?>
        </td>
        <td>
            <select class="form-select form-select-sm" style="width:120px;"
                    onchange="updateContractStatus(<?= $c['id'] ?>, this.value)">
                <?php foreach ($statusCol as $st => $_): ?>
                <option value="<?= $st ?>" <?= $c['status']===$st?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$st)) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="text-muted small">
            <?= $c['start_date'] ? date('d M y', strtotime($c['start_date'])) : '—' ?>
            <?= $c['end_date']   ? ' → '.date('d M y', strtotime($c['end_date'])) : '' ?>
        </td>
        <td><a href="<?= site_url("contracts/{$c['id']}") ?>" class="btn btn-sm btn-outline-primary">View</a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

<!-- New Contract Modal -->
<div class="modal fade" id="newContractModal" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content border-0 shadow">
    <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold"><i class="fa-solid fa-file-contract me-2 text-primary"></i>New Contract</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="row g-3">
            <div class="col-12"><label class="form-label small fw-semibold">Title <span class="text-danger">*</span></label>
                <input type="text" id="ctTitle" class="form-control" placeholder="Contract scope title">
            </div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Type</label>
                <select id="ctType" class="form-select">
                    <option value="main">Main Contract</option>
                    <option value="subcontract">Subcontract</option>
                    <option value="supply">Supply</option>
                    <option value="consultant">Consultant</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Contractor / Party</label>
                <input type="text" id="ctContractor" class="form-control">
            </div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Currency</label>
                <input type="text" id="ctCurrency" class="form-control" value="USD">
            </div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Contract Value</label>
                <input type="number" id="ctValue" class="form-control" step="0.01" value="0">
            </div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Retention %</label>
                <input type="number" id="ctRetention" class="form-control" step="0.01" value="10">
            </div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Start Date</label>
                <input type="date" id="ctStart" class="form-control">
            </div>
            <div class="col-md-4"><label class="form-label small fw-semibold">End Date</label>
                <input type="date" id="ctEnd" class="form-control">
            </div>
            <div class="col-12"><label class="form-label small fw-semibold">Scope Summary</label>
                <textarea id="ctScope" class="form-control" rows="3"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="submitContract()">Create Contract</button>
    </div>
</div></div></div>

<script>
function updateContractStatus(id, status) {
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('status', status);
    fetch(`/staging/public/contracts/${id}/status`, {method:'POST', body: fd});
}

function submitContract() {
    const title = document.getElementById('ctTitle').value.trim();
    if (!title) { alert('Please enter a title.'); return; }
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    fd.append('title',           title);
    fd.append('type',            document.getElementById('ctType').value);
    fd.append('contractor_name', document.getElementById('ctContractor').value);
    fd.append('currency',        document.getElementById('ctCurrency').value);
    fd.append('value',           document.getElementById('ctValue').value);
    fd.append('retention_pct',   document.getElementById('ctRetention').value);
    fd.append('start_date',      document.getElementById('ctStart').value);
    fd.append('end_date',        document.getElementById('ctEnd').value);
    fd.append('scope',           document.getElementById('ctScope').value);
    fetch(`/staging/public/projects/<?= $project['id'] ?>/contracts`, {method:'POST', body: fd})
        .then(r=>r.json()).then(d=>{
            if (d.success) location.reload();
            else alert('Error creating contract.');
        });
}
</script>
