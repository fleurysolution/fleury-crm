<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?php
/**
 * $project
 * $po
 * $items
 * $subcontractors
 */
$isEditable = in_array($po['status'], ['Draft', 'Sent']);
?>

<div class="mb-3">
    <a href="<?= site_url("projects/{$project['id']}?tab=procurement") ?>" class="text-decoration-none text-muted">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Procurement
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Purchase Order: <?= esc($po['po_number']) ?></h3>
        <div class="d-flex gap-3 text-muted small align-items-center">
            <span>Project: <strong class="text-dark"><?= esc($project['title']) ?></strong></span>
            <span>Date: <strong class="text-dark"><?= date('F d, Y', strtotime($po['created_at'])) ?></strong></span>
            <?php 
                $badge = 'bg-secondary';
                if ($po['status'] === 'Sent') $badge = 'bg-primary';
                if ($po['status'] === 'Executed') $badge = 'bg-success';
                if ($po['status'] === 'Void') $badge = 'bg-danger text-white';
            ?>
            <span class="badge <?= $badge ?>"><?= esc($po['status']) ?></span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url("procurement/pos/{$po['id']}/pdf") ?>" class="btn btn-outline-dark" target="_blank">
            <i class="fa-solid fa-file-pdf me-2"></i>Export PDF
        </a>
    </div>
</div>

<form action="<?= site_url("procurement/pos/{$po['id']}/items") ?>" method="POST" id="poForm">
    <?= csrf_field() ?>
    <input type="hidden" name="status_action" id="statusAction" value="save">

    <div class="row g-4">
        <!-- PO Metadata -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-md-5">
                            <h6 class="text-muted fw-bold mb-3 border-bottom pb-2">To Vendor:</h6>
                            <select name="vendor_id" class="form-select form-select-sm mb-3" <?= !$isEditable ? 'disabled' : '' ?>>
                                <option value="">-- To Be Determined --</option>
                                <?php foreach ($subcontractors as $sub): ?>
                                    <option value="<?= $sub['id'] ?>" <?= $po['vendor_id'] == $sub['id'] ? 'selected' : '' ?>>
                                        <?= esc($sub['first_name'] . ' ' . $sub['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <label class="form-label small fw-bold mt-2">Required Delivery Date</label>
                            <input type="date" name="delivery_date" class="form-control form-control-sm w-50" value="<?= esc($po['delivery_date']) ?>" <?= !$isEditable ? 'disabled' : '' ?>>
                        </div>

                        <div class="col-md-5 text-end">
                            <h6 class="text-muted fw-bold mb-3 border-bottom pb-2 text-end">Ship To:</h6>
                            <div class="fw-bold fs-5"><?= esc($project['title']) ?></div>
                            <div class="text-muted small">
                                <?= esc($project['location'] ?? 'No site address provided.') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Line Items Table -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Line Items</h5>
                    <!-- Must be able to edit title -->
                     <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted fw-bold mb-0 me-2">PO Title:</label>
                        <input type="text" name="title" class="form-control form-control-sm w-auto" value="<?= esc($po['title']) ?>" <?= !$isEditable ? 'disabled' : '' ?>>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="poTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3" style="width: 40%;">Description</th>
                                <th class="text-center" style="width: 15%;">Quantity</th>
                                <th class="text-center" style="width: 10%;">Unit</th>
                                <th class="text-end" style="width: 15%;">Unit Price</th>
                                <th class="text-end pe-4" style="width: 15%;">Total</th>
                                <?php if ($isEditable): ?><th style="width: 5%;"></th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($items) && $isEditable): ?>
                                <!-- Empty default row -->
                                <tr>
                                    <td class="ps-3 p-1"><input type="text" name="descriptions[]" class="form-control form-control-sm" placeholder="Item description" required></td>
                                    <td class="p-1"><input type="number" step="0.01" name="quantities[]" class="form-control form-control-sm text-center row-qty" value="1" oninput="calculateTotals()"></td>
                                    <td class="p-1 text-center"><input type="text" name="units[]" class="form-control form-control-sm text-center" value="LS"></td>
                                    <td class="p-1">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text border-0 bg-light">$</span>
                                            <input type="number" step="0.01" name="unit_prices[]" class="form-control form-control-sm text-end row-price" value="0.00" oninput="calculateTotals()">
                                        </div>
                                    </td>
                                    <td class="p-2 text-end pe-4 fw-bold row-total text-muted">$0.00</td>
                                    <td class="p-1 text-center"><button type="button" class="btn btn-sm text-danger border-0" onclick="this.closest('tr').remove(); calculateTotals();"><i class="fa-solid fa-xmark"></i></button></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td class="ps-3 p-1">
                                            <?php if ($isEditable): ?>
                                                <input type="text" name="descriptions[]" class="form-control form-control-sm" value="<?= esc($item['description']) ?>" required>
                                            <?php else: ?>
                                                <div class="fw-medium text-dark px-2 py-1"><?= esc($item['description']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-1">
                                            <?php if ($isEditable): ?>
                                                <input type="number" step="0.01" name="quantities[]" class="form-control form-control-sm text-center row-qty" value="<?= esc($item['quantity']) ?>" oninput="calculateTotals()">
                                            <?php else: ?>
                                                <div class="text-center px-2 py-1"><?= number_format($item['quantity'], 2) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-1">
                                            <?php if ($isEditable): ?>
                                                <input type="text" name="units[]" class="form-control form-control-sm text-center" value="<?= esc($item['unit']) ?>">
                                            <?php else: ?>
                                                <div class="text-center px-2 py-1"><?= esc($item['unit']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-1">
                                            <?php if ($isEditable): ?>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text border-0 bg-light">$</span>
                                                    <input type="number" step="0.01" name="unit_prices[]" class="form-control form-control-sm text-end row-price" value="<?= esc($item['unit_price']) ?>" oninput="calculateTotals()">
                                                </div>
                                            <?php else: ?>
                                                <div class="text-end px-2 py-1">$<?= number_format($item['unit_price'], 2) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-2 text-end pe-4 fw-bold text-dark row-total">
                                            $<?= number_format($item['total'], 2) ?>
                                        </td>
                                        <?php if ($isEditable): ?>
                                            <td class="p-1 text-center"><button type="button" class="btn btn-sm text-danger border-0" onclick="this.closest('tr').remove(); calculateTotals();"><i class="fa-solid fa-xmark"></i></button></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="bg-light border-top-2 border-dark">
                            <?php if ($isEditable): ?>
                                <tr>
                                    <td colspan="6" class="p-2 ps-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPoRow()">
                                            <i class="fa-solid fa-plus me-1"></i> Add Line Item
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="4" class="text-end fw-bold py-3 text-uppercase fs-5">Purchase Order Total:</td>
                                <td class="text-end pe-4 py-3 fw-bold fs-4 text-primary" id="poGrandTotal">$<?= number_format($po['total_amount'], 2) ?></td>
                                <?php if ($isEditable): ?><td></td><?php endif; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <label class="form-label fw-bold text-muted mb-2"><i class="fa-solid fa-pencil me-2"></i>Notes & Terms:</label>
                    <textarea name="notes" class="form-control border-0 bg-light" rows="3" placeholder="Add specific terms, shipping instructions, or notes here..." <?= !$isEditable ? 'disabled' : '' ?>><?= esc($po['notes']) ?></textarea>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center rounded-3 bg-white shadow-sm p-4 border border-light">
                <?php if ($isEditable): ?>
                    <div>
                        <button type="button" class="btn btn-light border me-2" onclick="submitPo('save')">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Save Changes
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <?php if ($po['status'] === 'Draft'): ?>
                            <button type="button" class="btn btn-primary" onclick="submitPo('send')">
                                <i class="fa-solid fa-paper-plane me-2"></i> Mark as Sent
                            </button>
                        <?php elseif ($po['status'] === 'Sent'): ?>
                            <button type="button" class="btn btn-success" onclick="submitPo('execute')">
                                <i class="fa-solid fa-file-signature me-2"></i> Mark Executed
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="submitPo('void')">
                                <i class="fa-solid fa-ban me-2"></i> Void PO
                            </button>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-muted w-100 text-center">
                        <?php if ($po['status'] === 'Executed'): ?>
                            <i class="fa-solid fa-circle-check text-success fs-4 mb-2 d-block"></i>
                            <span class="fw-bold">This PO has been fully executed and is locked.</span>
                        <?php else: ?>
                            <i class="fa-solid fa-ban text-danger fs-4 mb-2 d-block"></i>
                            <span class="fw-bold">This PO has been voided.</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<?php if ($isEditable): ?>
<script>
    function addPoRow() {
        const tbody = document.querySelector('#poTable tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="ps-3 p-1"><input type="text" name="descriptions[]" class="form-control form-control-sm" placeholder="Item description" required></td>
            <td class="p-1"><input type="number" step="0.01" name="quantities[]" class="form-control form-control-sm text-center row-qty" value="1" oninput="calculateTotals()"></td>
            <td class="p-1 text-center"><input type="text" name="units[]" class="form-control form-control-sm text-center" value="LS"></td>
            <td class="p-1">
                <div class="input-group input-group-sm">
                    <span class="input-group-text border-0 bg-light">$</span>
                    <input type="number" step="0.01" name="unit_prices[]" class="form-control form-control-sm text-end row-price" value="0.00" oninput="calculateTotals()">
                </div>
            </td>
            <td class="p-2 text-end pe-4 fw-bold row-total text-muted">$0.00</td>
            <td class="p-1 text-center"><button type="button" class="btn btn-sm text-danger border-0" onclick="this.closest('tr').remove(); calculateTotals();"><i class="fa-solid fa-xmark"></i></button></td>
        `;
        tbody.appendChild(tr);
        calculateTotals();
    }

    function calculateTotals() {
        let grandTotal = 0;
        
        document.querySelectorAll('#poTable tbody tr').forEach(row => {
            const qtyInput = row.querySelector('.row-qty');
            const priceInput = row.querySelector('.row-price');
            const totalDisplay = row.querySelector('.row-total');
            
            if (qtyInput && priceInput && totalDisplay) {
                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const lineTotal = qty * price;
                
                totalDisplay.innerText = '$' + lineTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                
                if (lineTotal > 0) totalDisplay.classList.replace('text-muted', 'text-dark');
                else totalDisplay.classList.replace('text-dark', 'text-muted');

                grandTotal += lineTotal;
            }
        });

        document.getElementById('poGrandTotal').innerText = '$' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function submitPo(action) {
        if(action === 'execute') {
            if(!confirm('Are you sure you want to mark this Purchase Order as fully EXECUTED? This will lock the document from future edits.')) return;
        }
        if(action === 'void') {
            if(!confirm('Are you sure you want to VOID this Purchase Order?')) return;
        }
        document.getElementById('statusAction').value = action;
        document.getElementById('poForm').submit();
    }

    // Initialize math on load
    document.addEventListener('DOMContentLoaded', calculateTotals);
</script>
<?php endif; ?>

<?= $this->endSection() ?>
