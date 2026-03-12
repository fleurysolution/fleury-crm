<?php
// app/Views/projects/tabs/boq_inline.php
$boqModel    = new \App\Models\BOQItemModel();
$driverModel  = new \App\Models\ProjectDriverModel();
$drivers      = $driverModel->forProject($project['id']);
$tree         = $boqModel->buildTree($project['id']);
$totalBOQ     = $boqModel->totalBOQ($project['id']);
$totalAct     = $boqModel->totalActual($project['id']);
$variance     = $totalAct - $totalBOQ;
$pct          = $totalBOQ > 0 ? round($totalAct / $totalBOQ * 100, 1) : 0;

function renderBOQRow(array $item, int $depth = 0, array $drivers = []): void { ?>
<?php if ($item['is_section']): ?>
<tr class="table-light fw-semibold" data-id="<?= $item['id'] ?>">
    <td colspan="7" style="padding-left:<?= ($depth * 16 + 8) ?>px;">
        <i class="fa-solid fa-folder me-2 text-warning"></i><?= esc($item['description']) ?>
    </td>
</tr>
<?php else: ?>
<tr data-id="<?= $item['id'] ?>">
    <td style="padding-left:<?= ($depth * 16 + 16) ?>px;" class="text-muted small"><?= esc($item['item_code'] ?? '') ?></td>
    <td>
        <?= esc($item['description']) ?>
        <?php if ($item['driver_id']): 
            $dName = '';
            foreach ($drivers as $drv) if ($drv['id'] == $item['driver_id']) $dName = $drv['name'];
        ?>
        <div class="small text-primary opacity-75" style="font-size:0.75rem;">
            <i class="fa-solid fa-link me-1"></i>Linked: <?= esc($dName) ?> (x<?= $item['driver_multiplier'] ?>)
        </div>
        <?php endif; ?>
    </td>
    <td class="text-center"><?= esc($item['unit'] ?? '') ?></td>
    <td class="text-end"><?= number_format($item['quantity'], 2) ?></td>
    <td class="text-end"><?= number_format($item['unit_rate'], 2) ?></td>
    <td class="text-end fw-semibold"><?= number_format($item['total_amount'], 2) ?></td>
    <td class="text-end <?= $item['actual_amount'] > $item['total_amount'] ? 'text-danger' : 'text-success' ?>">
        <?= number_format($item['actual_amount'], 2) ?>
    </td>
</tr>
<?php endif;
    foreach ($item['children'] ?? [] as $child) renderBOQRow($child, $depth + 1, $drivers);
}
?>

<!-- Summary bar -->
<div class="row g-3 mb-3">
    <?php 
    $costPerSqFt = ($project['gross_sqft'] > 0) ? ($totalBOQ / $project['gross_sqft']) : 0;
    foreach ([
        ['BOQ Total', number_format($totalBOQ,2), 'primary'],
        ['Actual', number_format($totalAct,2), 'info'],
        ['Cost / SQFT', ($costPerSqFt > 0 ? '$'.number_format($costPerSqFt,2) : '—'), 'secondary'],
        ['Variance', ($variance >= 0 ? '+' : '').number_format($variance,2), $variance >= 0 ? 'success' : 'danger'],
    ] as [$label, $value, $color]): ?>
    <div class="col-md-3">
        <div class="card border-0 bg-<?= $color ?>-subtle text-<?= $color ?> text-center py-3" style="border-radius:10px;">
            <div style="font-size:1.3rem;" class="fw-bold"><?= $value ?></div>
            <div class="small mt-1 opacity-75"><?= $label ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Toolbar -->
<div class="d-flex justify-content-end gap-2 mb-2">
    <button class="btn btn-sm btn-outline-success" onclick="importBOQ()">
        <i class="fa-solid fa-file-excel me-1"></i>Import from Master Excel
    </button>
    <a href="<?= site_url("projects/{$project['id']}/boq/export") ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-download me-1"></i>Export CSV
    </a>
    <button class="btn btn-sm btn-outline-primary" onclick="addBOQSection()">
        <i class="fa-solid fa-folder-plus me-1"></i>Add Section
    </button>
    <button class="btn btn-sm btn-primary" onclick="addBOQItem()">
        <i class="fa-solid fa-plus me-1"></i>Add Item
    </button>
</div>

<div class="card border-0 shadow-sm" style="border-radius:10px;overflow:hidden;">
<table class="table table-sm table-hover align-middle small mb-0">
    <thead class="table-light">
        <tr>
            <th style="width:80px;">Code</th>
            <th>Description</th>
            <th class="text-center" style="width:60px;">Unit</th>
            <th class="text-end" style="width:90px;">Qty</th>
            <th class="text-end" style="width:100px;">Unit Rate</th>
            <th class="text-end" style="width:120px;">BOQ Total</th>
            <th class="text-end" style="width:120px;">Actual</th>
        </tr>
    </thead>
    <tbody id="boqBody">
    <?php foreach ($tree as $item) renderBOQRow($item, 0, $drivers); ?>
    <?php if (empty($tree)): ?>
    <tr><td colspan="7" class="text-center text-muted py-4">No BOQ items yet. Click <strong>Add Item</strong> to begin.</td></tr>
    <?php endif; ?>
    </tbody>
    <tfoot class="table-light fw-bold">
        <tr>
            <td colspan="5" class="text-end">Total</td>
            <td class="text-end"><?= number_format($totalBOQ, 2) ?></td>
            <td class="text-end"><?= number_format($totalAct, 2) ?></td>
        </tr>
    </tfoot>
</table>
</div>

<!-- Quick-add row template (inline modal) -->
<div class="modal fade" id="boqItemModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content border-0 shadow">
    <div class="modal-header border-0">
        <h6 class="modal-title fw-semibold" id="boqModalTitle">Add BOQ Item</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <input type="hidden" id="boqIsSection" value="0">
        <div class="row g-2">
            <div class="col-4"><label class="form-label small">Code</label>
                <input type="text" id="boqCode" class="form-control form-control-sm"></div>
            <div class="col-8"><label class="form-label small">Description <span class="text-danger">*</span></label>
                <input type="text" id="boqDesc" class="form-control form-control-sm"></div>
            
            <div id="boqQtyFields">
                <div class="col-12 mt-2 bg-light p-2 rounded border">
                    <label class="form-label small fw-bold text-primary mb-1"><i class="fa-solid fa-link me-1"></i>Driver-Link (Optional)</label>
                    <div class="row g-1">
                        <div class="col-8">
                            <select id="boqDriverId" class="form-select form-select-sm" onchange="toggleManualQty()">
                                <option value="">— Use Manual Quantity —</option>
                                <?php foreach ($drivers as $d): ?>
                                <option value="<?= $d['id'] ?>"><?= esc($d['name']) ?> (<?= $d['value'] ?> <?= esc($d['unit']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-4">
                            <input type="number" id="boqMultiplier" class="form-control form-control-sm" placeholder="Multiplier" step="0.0001" value="1.0000">
                        </div>
                    </div>
                    <small class="text-muted" style="font-size:0.7rem;">Select a driver to auto-calculate Qty based on project metrics.</small>
                </div>

                <div class="row g-2 mt-2">
                    <div class="col-4"><label class="form-label small">Unit</label>
                        <input type="text" id="boqUnit" class="form-control form-control-sm" placeholder="m², kg…"></div>
                    <div class="col-4"><label class="form-label small">Qty</label>
                        <input type="number" id="boqQty" class="form-control form-control-sm" value="0" step="0.01"></div>
                    <div class="col-4"><label class="form-label small">Unit Rate</label>
                        <input type="number" id="boqRate" class="form-control form-control-sm" value="0" step="0.01"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="saveBOQItem()">Save</button>
    </div>
</div></div></div>

<script>
function toggleManualQty() {
    const drvId = document.getElementById('boqDriverId').value;
    const qtyInput = document.getElementById('boqQty');
    if (drvId) {
        qtyInput.readOnly = true;
        qtyInput.classList.add('bg-light');
    } else {
        qtyInput.readOnly = false;
        qtyInput.classList.remove('bg-light');
    }
}
function addBOQSection() {
    document.getElementById('boqIsSection').value = '1';
    document.getElementById('boqModalTitle').textContent = 'Add Section Header';
    document.getElementById('boqQtyFields').style.display = 'none';
    new bootstrap.Modal(document.getElementById('boqItemModal')).show();
}
function addBOQItem() {
    document.getElementById('boqIsSection').value = '0';
    document.getElementById('boqModalTitle').textContent = 'Add BOQ Item';
    document.getElementById('boqQtyFields').style.display = '';
    document.getElementById('boqDriverId').value = '';
    document.getElementById('boqMultiplier').value = '1.0000';
    toggleManualQty();
    new bootstrap.Modal(document.getElementById('boqItemModal')).show();
}
function saveBOQItem() {
    const desc = document.getElementById('boqDesc').value.trim();
    if (!desc) { alert('Enter a description.'); return; }
    const isSection = document.getElementById('boqIsSection').value;
    const qty  = parseFloat(document.getElementById('boqQty').value) || 0;
    const rate = parseFloat(document.getElementById('boqRate').value) || 0;
    const payload = {
        rows: [{
            item_code:   document.getElementById('boqCode').value,
            description: desc,
            unit:        document.getElementById('boqUnit').value,
            quantity:    qty,
            unit_rate:   rate,
            is_section:  parseInt(isSection),
            sort_order:  document.querySelectorAll('#boqBody tr').length,
            driver_id:   document.getElementById('boqDriverId').value,
            driver_multiplier: document.getElementById('boqMultiplier').value,
        }]
    };
    fetch(`/staging/public/projects/<?= $project['id'] ?>/boq`, {
        method:'POST',
        headers:{'Content-Type':'application/json', 'X-Requested-With':'XMLHttpRequest', [CSRF_NAME]: CSRF_TOKEN},
        body: JSON.stringify(payload),
    }).then(r=>r.json()).then(d => { if (d.success) location.reload(); });
}
function importBOQ() {
    if (!confirm('This will replace all current BOQ items with data from the Master Excel. Proceed?')) return;
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Importing...';
    
    fetch(`/staging/public/projects/<?= $project['id'] ?>/boq/import`, {
        method:'POST',
        headers:{'X-Requested-With':'XMLHttpRequest', [CSRF_NAME]: CSRF_TOKEN},
    }).then(r=>r.json()).then(d => {
        if (d.success) {
            alert('Imported ' + d.count + ' items successfully.');
            location.reload();
        } else {
            alert('Error: ' + d.message);
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }).catch(err => {
        alert('Server error occurred.');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}
</script>
