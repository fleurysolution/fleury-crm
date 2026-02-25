<?php
// app/Views/projects/tabs/finance_inline.php
$certModel  = new \App\Models\PaymentCertificateModel();
$invModel   = new \App\Models\InvoiceModel();
$expModel   = new \App\Models\ProjectExpenseModel();
$boqModel   = new \App\Models\BOQItemModel();

$certs      = $certModel->forProject($project['id']);
$totalPaid  = $certModel->totalPaid($project['id']);

$invTotals  = $invModel->totalByDirection($project['id']);
$invoices   = $invModel->forProject($project['id']);

$expenses   = $expModel->forProject($project['id'], 'pending');
$expTotal   = $expModel->totalApproved($project['id']);
$expByCat   = $expModel->totalByCategory($project['id']);

$boqTotal   = $boqModel->totalBOQ($project['id']);

$statusBadge = [
    'draft'=>'secondary','submitted'=>'primary','approved'=>'info','paid'=>'success',
    'sent'=>'primary','partial'=>'warning','overdue'=>'danger','void'=>'dark','pending'=>'warning',
];
?>

<!-- KPI Cards row -->
<div class="row g-3 mb-4">
    <?php foreach ([
        ['Contract Value', number_format($boqTotal,2), 'primary',   'fa-file-contract'],
        ['Certs Paid',     number_format($totalPaid,2),'success',   'fa-check-circle'],
        ['Income Billed',  number_format($invTotals['income']['total'],2), 'info', 'fa-arrow-up'],
        ['Expenses',       number_format($expTotal,2), 'danger',    'fa-arrow-down'],
    ] as [$label,$val,$col,$icon]): ?>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3" style="border-radius:12px;">
            <i class="fa-solid <?= $icon ?> fa-lg text-<?= $col ?> mb-1"></i>
            <div class="fw-bold fs-5"><?= $val ?></div>
            <div class="text-muted small"><?= $label ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Tabs: Certificates / Invoices / Expenses -->
<ul class="nav nav-tabs border-0 mb-3" id="finTab">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#finCerts">Payment Certs</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#finInvoices">Invoices</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#finExpenses">Expenses <?= count($expenses) > 0 ? '<span class="badge bg-warning text-dark ms-1">'.count($expenses).'</span>' : '' ?></a></li>
</ul>

<div class="tab-content">

<!-- Certs tab -->
<div class="tab-pane fade show active" id="finCerts">
    <div class="d-flex justify-content-end mb-2">
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newCertModal">
            <i class="fa-solid fa-plus me-1"></i>New IPC
        </button>
    </div>
    <?php if (empty($certs)): ?>
    <div class="text-center py-4 text-muted">No payment certificates yet.</div>
    <?php else: ?>
    <table class="table table-hover small align-middle">
        <thead class="table-light"><tr><th>Number</th><th>Period</th><th>Gross</th><th>Retention</th><th>Net</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($certs as $c): ?>
        <tr>
            <td class="fw-semibold"><?= esc($c['cert_number']) ?></td>
            <td class="text-muted"><?= ($c['period_from']??'') ?> — <?= ($c['period_to']??'') ?></td>
            <td><?= number_format($c['gross_amount'],2) ?></td>
            <td class="text-danger">(<?= number_format($c['retention_amount'],2) ?>)</td>
            <td class="fw-semibold"><?= number_format($c['net_amount'],2) ?></td>
            <td><span class="badge bg-<?= $statusBadge[$c['status']]??'secondary' ?>-subtle text-<?= $statusBadge[$c['status']]??'secondary' ?>"><?= ucfirst($c['status']) ?></span></td>
            <td>
                <?php if ($c['status']==='submitted'): ?>
                <button class="btn btn-xs btn-success" onclick="certAction(<?= $c['id'] ?>,'approve')">Approve</button>
                <?php elseif ($c['status']==='approved'): ?>
                <button class="btn btn-xs btn-primary" onclick="certAction(<?= $c['id'] ?>,'mark-paid')">Mark Paid</button>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- Invoices tab -->
<div class="tab-pane fade" id="finInvoices">
    <div class="d-flex justify-content-end mb-2">
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newInvModal">
            <i class="fa-solid fa-plus me-1"></i>New Invoice
        </button>
    </div>
    <?php if (empty($invoices)): ?>
    <div class="text-center py-4 text-muted">No invoices yet.</div>
    <?php else: ?>
    <table class="table table-hover small align-middle">
        <thead class="table-light"><tr><th>Number</th><th>Direction</th><th>Party</th><th>Date</th><th>Total</th><th>Paid</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($invoices as $inv): ?>
        <tr>
            <td class="fw-semibold"><?= esc($inv['invoice_number']) ?></td>
            <td><span class="badge bg-<?= $inv['direction']==='income'?'success':'danger' ?>-subtle text-<?= $inv['direction']==='income'?'success':'danger' ?>"><?= ucfirst($inv['direction']) ?></span></td>
            <td><?= esc($inv['party_name']??'—') ?></td>
            <td><?= $inv['invoice_date'] ? date('d M y', strtotime($inv['invoice_date'])) : '—' ?></td>
            <td><?= number_format($inv['total_amount'],2) ?></td>
            <td><?= number_format($inv['paid_amount'],2) ?></td>
            <td><span class="badge bg-<?= $statusBadge[$inv['status']]??'secondary' ?>-subtle text-<?= $statusBadge[$inv['status']]??'secondary' ?>"><?= ucfirst($inv['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- Expenses tab -->
<div class="tab-pane fade" id="finExpenses">
    <div class="d-flex justify-content-end mb-2">
        <a href="<?= site_url("projects/{$project['id']}/finance/export") ?>" class="btn btn-sm btn-outline-secondary me-1">
            <i class="fa-solid fa-download me-1"></i>CSV Report
        </a>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newExpModal">
            <i class="fa-solid fa-plus me-1"></i>Add Expense
        </button>
    </div>
    <?php if (empty($expenses)): ?>
    <div class="text-center py-4 text-muted">No pending expenses.</div>
    <?php else: ?>
    <table class="table table-hover small align-middle">
        <thead class="table-light"><tr><th>Date</th><th>Category</th><th>Description</th><th>Vendor</th><th>Amount</th><th>By</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($expenses as $ex): ?>
        <tr>
            <td><?= $ex['expense_date'] ? date('d M y', strtotime($ex['expense_date'])) : '—' ?></td>
            <td><?= esc($ex['category']??'—') ?></td>
            <td><?= esc($ex['description']) ?></td>
            <td><?= esc($ex['vendor']??'—') ?></td>
            <td class="fw-semibold"><?= number_format($ex['amount'],2) ?></td>
            <td class="text-muted"><?= esc($ex['submitter_name']??'') ?></td>
            <td>
                <button class="btn btn-xs btn-success" onclick="approveExpense(<?= $ex['id'] ?>)">Approve</button>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

</div><!-- .tab-content -->

<!-- Modals -->
<div class="modal fade" id="newCertModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content border-0 shadow">
    <div class="modal-header border-0"><h6 class="modal-title fw-semibold">New Payment Certificate (IPC)</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="row g-2">
            <div class="col-6"><label class="form-label small">Period From</label><input type="date" id="certFrom" class="form-control form-control-sm"></div>
            <div class="col-6"><label class="form-label small">Period To</label><input type="date" id="certTo" class="form-control form-control-sm"></div>
            <div class="col-6"><label class="form-label small">Gross Amount</label><input type="number" id="certGross" class="form-control form-control-sm" step="0.01"></div>
            <div class="col-6"><label class="form-label small">Retention %</label><input type="number" id="certRet" class="form-control form-control-sm" value="10" step="0.01"></div>
            <div class="col-12"><label class="form-label small">Notes</label><textarea id="certNotes" class="form-control form-control-sm" rows="2"></textarea></div>
        </div>
    </div>
    <div class="modal-footer border-0">
        <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-sm btn-primary" onclick="submitCert()">Submit IPC</button>
    </div>
</div></div></div>

<div class="modal fade" id="newInvModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content border-0 shadow">
    <div class="modal-header border-0"><h6 class="modal-title fw-semibold">New Invoice</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="row g-2">
            <div class="col-6"><label class="form-label small">Direction</label>
                <select id="invDir" class="form-select form-select-sm"><option value="income">Income</option><option value="expense">Expense</option></select>
            </div>
            <div class="col-6"><label class="form-label small">Party</label><input type="text" id="invParty" class="form-control form-control-sm"></div>
            <div class="col-6"><label class="form-label small">Invoice Date</label><input type="date" id="invDate" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>"></div>
            <div class="col-6"><label class="form-label small">Due Date</label><input type="date" id="invDue" class="form-control form-control-sm"></div>
            <div class="col-6"><label class="form-label small">Subtotal</label><input type="number" id="invSub" class="form-control form-control-sm" step="0.01"></div>
            <div class="col-6"><label class="form-label small">Tax</label><input type="number" id="invTax" class="form-control form-control-sm" step="0.01" value="0"></div>
        </div>
    </div>
    <div class="modal-footer border-0">
        <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-sm btn-primary" onclick="submitInvoice()">Create</button>
    </div>
</div></div></div>

<div class="modal fade" id="newExpModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content border-0 shadow">
    <div class="modal-header border-0"><h6 class="modal-title fw-semibold">Add Expense</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="row g-2">
            <div class="col-6"><label class="form-label small">Category</label><input type="text" id="expCat" class="form-control form-control-sm" placeholder="Labour, Material…"></div>
            <div class="col-6"><label class="form-label small">Date</label><input type="date" id="expDate" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>"></div>
            <div class="col-12"><label class="form-label small">Description <span class="text-danger">*</span></label><input type="text" id="expDesc" class="form-control form-control-sm"></div>
            <div class="col-6"><label class="form-label small">Vendor</label><input type="text" id="expVendor" class="form-control form-control-sm"></div>
            <div class="col-6"><label class="form-label small">Amount</label><input type="number" id="expAmt" class="form-control form-control-sm" step="0.01"></div>
        </div>
    </div>
    <div class="modal-footer border-0">
        <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-sm btn-primary" onclick="submitExpense()">Submit</button>
    </div>
</div></div></div>

<script>
const projectId = <?= $project['id'] ?>;
function post(url, data={}) {
    const fd = new FormData();
    fd.append(CSRF_NAME, CSRF_TOKEN);
    Object.entries(data).forEach(([k,v]) => fd.append(k,v));
    return fetch(url, {method:'POST', body: fd}).then(r=>r.json());
}
function certAction(id, action) {
    post(`/staging/public/finance/certs/${id}/${action}`).then(d => { if(d.success) location.reload(); });
}
function approveExpense(id) {
    post(`/staging/public/finance/expenses/${id}/approve`).then(d => { if(d.success) location.reload(); });
}
function submitCert() {
    post(`/staging/public/projects/${projectId}/finance/certs`, {
        period_from: document.getElementById('certFrom').value,
        period_to:   document.getElementById('certTo').value,
        gross_amount:document.getElementById('certGross').value,
        retention_pct: document.getElementById('certRet').value,
        notes:       document.getElementById('certNotes').value,
    }).then(d => { if(d.success) location.reload(); });
}
function submitInvoice() {
    post(`/staging/public/projects/${projectId}/finance/invoices`, {
        direction:    document.getElementById('invDir').value,
        party_name:   document.getElementById('invParty').value,
        invoice_date: document.getElementById('invDate').value,
        due_date:     document.getElementById('invDue').value,
        subtotal:     document.getElementById('invSub').value,
        tax_amount:   document.getElementById('invTax').value,
    }).then(d => { if(d.success) location.reload(); });
}
function submitExpense() {
    const desc = document.getElementById('expDesc').value.trim();
    if (!desc) { alert('Enter a description.'); return; }
    post(`/staging/public/projects/${projectId}/finance/expenses`, {
        category:     document.getElementById('expCat').value,
        description:  desc,
        expense_date: document.getElementById('expDate').value,
        vendor:       document.getElementById('expVendor').value,
        amount:       document.getElementById('expAmt').value,
    }).then(d => { if(d.success) location.reload(); });
}
</script>
