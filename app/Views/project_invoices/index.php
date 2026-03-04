<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <div>
            <h3 class="m-0">Invoices</h3>
            <p class="text-muted m-0" style="font-size: 0.875rem;">Manage invoices and payments</p>
        </div>
        <!-- <a href="<?= site_url('invoices/new') ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> New Invoice</a> -->
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Bill Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Paid</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($invoices)): ?>
                    <tr>
                         <td colspan="8" class="text-center p-4">
                            <div class="text-muted">No invoices found.</div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td class="font-mono">#<?= $invoice['id'] ?></td>
                            <td>
                                <a href="<?= site_url('clients/' . $invoice['client_id']) ?>" class="font-bold">
                                    <?= esc($invoice['company_name']) ?>
                                </a>
                            </td>
                            <td><?= date('M d, Y', strtotime($invoice['bill_date'])) ?></td>
                            <td class="text-muted"><?= date('M d, Y', strtotime($invoice['due_date'])) ?></td>
                            <td>
                                <?php 
                                    $statusClass = 'badge-secondary';
                                    if ($invoice['status'] == 'fully_paid') $statusClass = 'badge-success';
                                    if ($invoice['status'] == 'partially_paid') $statusClass = 'badge-info';
                                    if ($invoice['status'] == 'not_paid') $statusClass = 'badge-warning';
                                    if ($invoice['status'] == 'overdue') $statusClass = 'badge-danger';
                                    if ($invoice['status'] == 'cancelled') $statusClass = 'badge-secondary';
                                ?>
                                <span class="badge <?= $statusClass ?>">
                                    <?= ucfirst(str_replace('_', ' ', $invoice['status'])) ?>
                                </span>
                            </td>
                            <td class="font-mono"><?= number_format($invoice['invoice_total'], 2) ?></td>
                            <td class="font-mono"><?= number_format($invoice['payment_received'], 2) ?></td>
                            <td class="text-right">
                                <a href="<?= site_url('invoices/' . $invoice['id']) ?>" class="btn btn-sm btn-secondary">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
