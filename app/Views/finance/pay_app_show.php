<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?php
/**
 * $project
 * $payApp
 * $sovLines (array of SovItemModel)
 * $mappedItems (array keyed by sov_item_id containing work_completed_this_period, etc.)
 * $previousProgress (array keyed by sov_item_id)
 */

$isEditable = ($payApp['status'] === 'Draft' || $payApp['status'] === 'Rejected');

$totalScheduled = 0;
$totalPrev      = 0;
$totalThisPeriod= 0;
$totalStored    = 0;
?>

<div class="mb-3">
    <a href="<?= site_url("projects/{$project['id']}?tab=finance") ?>" class="text-decoration-none text-muted">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Finance
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Application for Payment #<?= str_pad($payApp['application_no'], 3, '0', STR_PAD_LEFT) ?></h3>
        <div class="d-flex gap-3 text-muted small align-items-center">
            <span>Period To: <strong class="text-dark"><?= date('M d, Y', strtotime($payApp['period_to'])) ?></strong></span>
            <span>Retainage: <strong class="text-dark"><?= $payApp['retainage_percentage'] ?>%</strong></span>
            <span class="badge bg-<?= $payApp['status']==='Paid'?'success':($payApp['status']==='Approved'?'success-subtle text-success':($payApp['status']==='Submitted'?'primary-subtle text-primary':($payApp['status']==='Draft'?'secondary':'danger'))) ?>">
                <?= esc($payApp['status']) ?>
            </span>
        </div>
    </div>
    <div class="text-end">
        <div class="fw-bold text-muted small text-uppercase mb-1">Total Earned Less Retainage</div>
        <h3 class="fw-bold text-success mb-0" id="headerTotalDue">$0.00</h3>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <form action="<?= site_url("finance/pay-apps/{$payApp['id']}/items") ?>" method="POST" id="payAppForm">
        <?= csrf_field() ?>
        <input type="hidden" name="status_action" id="statusAction" value="save">
        
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Continuation Sheet (Worksheet)</h5>
            <div class="d-flex gap-2">
                <a href="<?= site_url("finance/pay-apps/{$payApp['id']}/pdf") ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-print me-1"></i> PDF Export
                </a>
                <?php if ($isEditable): ?>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="submitApp('save')">Save Draft</button>
                    <button type="button" class="btn btn-sm btn-success" onclick="submitApp('submit')">Submit for Certification</button>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="min-width: 1000px; font-size: 0.85rem;">
                <thead class="bg-light align-middle text-center">
                    <tr>
                        <th rowspan="2" class="text-start ps-3" style="width: 60px;">Item No.</th>
                        <th rowspan="2" class="text-start">Description of Work</th>
                        <th rowspan="2" style="width: 120px;">Scheduled Value</th>
                        <th rowspan="2" style="width: 120px;">Previous Applications</th>
                        <th colspan="2" class="bg-primary-subtle">Work This Period</th>
                        <th rowspan="2" style="width: 140px;">Total Completed & Stored To Date</th>
                        <th rowspan="2" style="width: 80px;">% (G/C)</th>
                        <th rowspan="2" style="width: 120px;" class="pe-3">Balance to Finish</th>
                    </tr>
                    <tr>
                        <th class="bg-light" style="width: 130px;">Work Completed</th>
                        <th class="bg-light" style="width: 130px;">Materials Stored</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sovLines)): ?>
                        <tr><td colspan="9" class="text-center py-4">No SOV items found. Return to the Finance tab to build the Schedule of Values.</td></tr>
                    <?php else: ?>
                        <?php foreach ($sovLines as $line): 
                            $id = $line['id'];
                            $scheduled = (float)$line['scheduled_value'];
                            $prev = isset($previousProgress[$id]) ? (float)$previousProgress[$id] : 0.00;
                            
                            $thisWork = isset($mappedItems[$id]) ? (float)$mappedItems[$id]['work_completed_this_period'] : 0.00;
                            $thisMat  = isset($mappedItems[$id]) ? (float)$mappedItems[$id]['materials_presently_stored'] : 0.00;
                            
                            $totalScheduled += $scheduled;
                            $totalPrev += $prev;
                            $totalThisPeriod += $thisWork;
                            $totalStored += $thisMat;

                            $maxAllowed = $scheduled - $prev;
                        ?>
                            <tr class="sov-row" data-id="<?= $id ?>" data-scheduled="<?= $scheduled ?>" data-prev="<?= $prev ?>">
                                <td class="ps-3 fw-bold text-muted"><?= esc($line['item_no']) ?></td>
                                <td class="fw-medium text-wrap" style="max-width: 200px;"><?= esc($line['description']) ?></td>
                                <td class="text-end">$<?= number_format($scheduled, 2) ?></td>
                                <td class="text-end text-muted">$<?= number_format($prev, 2) ?></td>
                                
                                <!-- Editable Inputs -->
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0" max="<?= $maxAllowed ?>" 
                                           name="work_completed[<?= $id ?>]" 
                                           class="form-control form-control-sm text-end work-input" 
                                           value="<?= $thisWork > 0 ? $thisWork : '' ?>"
                                           placeholder="0.00"
                                           <?= !$isEditable ? 'disabled' : '' ?>>
                                </td>
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0" 
                                           name="materials_stored[<?= $id ?>]" 
                                           class="form-control form-control-sm text-end mat-input" 
                                           value="<?= $thisMat > 0 ? $thisMat : '' ?>"
                                           placeholder="0.00"
                                           <?= !$isEditable ? 'disabled' : '' ?>>
                                </td>

                                <!-- Calculated Cells -->
                                <td class="text-end fw-bold text-primary total-to-date">$0.00</td>
                                <td class="text-center pct-complete">0.00%</td>
                                <td class="text-end pe-3 text-muted balance-to-finish">$<?= number_format($scheduled, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot class="bg-light fw-bold text-end">
                    <tr>
                        <td colspan="2" class="pe-3">Grand Totals:</td>
                        <td>$<?= number_format($totalScheduled, 2) ?></td>
                        <td>$<?= number_format($totalPrev, 2) ?></td>
                        <td id="sumWork">$0.00</td>
                        <td id="sumMat">$0.00</td>
                        <td id="sumTotal" class="text-primary fs-6">$0.00</td>
                        <td id="sumPct" class="text-center">0.00%</td>
                        <td id="sumBal" class="pe-3">$0.00</td>
                    </tr>
                    
                    <!-- Retainage Calculation Row -->
                    <tr>
                        <td colspan="6" class="pe-3 text-muted">Less <?= $payApp['retainage_percentage'] ?>% Retainage</td>
                        <td class="text-danger" id="sumRetainage">$0.00</td>
                        <td colspan="2"></td>
                    </tr>
                    <!-- Total Earned Less Retainage -->
                    <tr>
                        <td colspan="6" class="pe-3 text-dark">Total Earned Less Retainage To Date</td>
                        <td class="text-success fs-5" id="sumEarned">$0.00</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </form>
</div>

<!-- JavaScript to auto-calculate the grid -->
<script>
    const retainagePct = <?= $payApp['retainage_percentage'] ?>;
    
    function formatCurrency(num) {
        return '$' + parseFloat(num).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function calculateGrid() {
        let globalWork = 0;
        let globalMat = 0;
        let globalToDate = 0;
        let globalBalance = 0;
        let globalScheduled = <?= $totalScheduled ?>;
        
        document.querySelectorAll('.sov-row').forEach(row => {
            const scheduled = parseFloat(row.getAttribute('data-scheduled')) || 0;
            const prev = parseFloat(row.getAttribute('data-prev')) || 0;
            
            const workInput = row.querySelector('.work-input');
            const matInput = row.querySelector('.mat-input');
            
            let work = parseFloat(workInput.value) || 0;
            let mat = parseFloat(matInput.value) || 0;
            
            // To Date = Prev + Work + Mat
            let toDate = prev + work + mat;
            if (toDate > scheduled) {
                // Warning highlight if overbilled
                workInput.classList.add('border-danger', 'text-danger');
            } else {
                workInput.classList.remove('border-danger', 'text-danger');
            }

            let balance = scheduled - toDate;
            let pct = scheduled > 0 ? (toDate / scheduled) * 100 : 0;

            row.querySelector('.total-to-date').innerText = formatCurrency(toDate);
            row.querySelector('.balance-to-finish').innerText = formatCurrency(balance);
            row.querySelector('.pct-complete').innerText = pct.toFixed(2) + '%';
            
            globalWork += work;
            globalMat += mat;
            globalToDate += toDate;
            globalBalance += balance;
        });

        // Update Footers
        document.getElementById('sumWork').innerText = formatCurrency(globalWork);
        document.getElementById('sumMat').innerText = formatCurrency(globalMat);
        document.getElementById('sumTotal').innerText = formatCurrency(globalToDate);
        document.getElementById('sumBal').innerText = formatCurrency(globalBalance);
        
        let globalPct = globalScheduled > 0 ? (globalToDate / globalScheduled) * 100 : 0;
        document.getElementById('sumPct').innerText = globalPct.toFixed(2) + '%';

        // Retainage
        let retainageAmount = globalToDate * (retainagePct / 100);
        document.getElementById('sumRetainage').innerText = '-' + formatCurrency(retainageAmount);
        
        // Total Earned
        let earned = globalToDate - retainageAmount;
        document.getElementById('sumEarned').innerText = formatCurrency(earned);
        
        // Update Header
        document.getElementById('headerTotalDue').innerText = formatCurrency(earned);
    }

    // Attach listener to all inputs
    document.querySelectorAll('.work-input, .mat-input').forEach(input => {
        input.addEventListener('input', calculateGrid);
    });

    // Run on load
    calculateGrid();

    // Form submission wrapper
    function submitApp(action) {
        if(action === 'submit') {
            if(!confirm('Are you sure you want to finalize and submit this Payment Application? It cannot be edited after submission.')) return;
        }
        document.getElementById('statusAction').value = action;
        document.getElementById('payAppForm').submit();
    }
</script>

<?= $this->endSection() ?>
