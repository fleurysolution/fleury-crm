<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
    <div>
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#paymentModal">
            <i class="fas fa-money-bill-wave"></i> Add Payment
        </button>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-sm-6">
                <h5 class="font-weight-bold">Invoice #<?= $invoice['id'] ?></h5>
                <p>
                    <strong>Bill Date:</strong> <?= date('Y-m-d', strtotime($invoice['bill_date'])) ?><br>
                    <strong>Due Date:</strong> <?= date('Y-m-d', strtotime($invoice['due_date'])) ?><br>
                    <strong>Status:</strong> <span class="badge badge-<?= $invoice['status'] == 'fully_paid' ? 'success' : 'warning' ?>"><?= ucfirst(str_replace('_', ' ', $invoice['status'])) ?></span>
                </p>
            </div>
            <div class="col-sm-6 text-right">
                <h5 class="font-weight-bold">To:</h5>
                <p>
                    <?php if ($client): ?>
                        <?= esc($client['company_name']) ?><br>
                        <?= esc($client['address']) ?><br>
                        <?= esc($client['city']) ?>, <?= esc($client['state']) ?> <?= esc($client['zip']) ?><br>
                        <?= esc($client['country']) ?>
                    <?php else: ?>
                        Client not found.
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Rate</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $subtotal = 0; ?>
                <?php foreach ($items as $item): ?>
                    <?php $subtotal += $item['total']; ?>
                    <tr>
                        <td><?= esc($item['title']) ?></td>
                        <td><?= esc($item['description']) ?></td>
                        <td class="text-right"><?= $item['quantity'] ?></td>
                        <td class="text-right"><?= number_format($item['rate'], 2) ?></td>
                        <td class="text-right"><?= number_format($item['total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right font-weight-bold">Total</td>
                    <td class="text-right font-weight-bold"><?= number_format($subtotal, 2) ?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right font-weight-bold">Paid</td>
                    <td class="text-right font-weight-bold"><?= number_format($invoice['payment_received'], 2) ?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right font-weight-bold">Balance Due</td>
                    <td class="text-right font-weight-bold text-danger"><?= number_format($subtotal - $invoice['payment_received'], 2) ?></td>
                </tr>
            </tfoot>
        </table>

         <?php if (!empty($payments)): ?>
            <hr>
            <h5>Payments History</h5>
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?= $payment['payment_date'] ?></td>
                            <td><?= number_format($payment['amount'], 2) ?></td>
                            <td><?= $payment['payment_method_id'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Add Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= site_url('invoices/' . $invoice['id'] . '/payment') ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" name="amount" class="form-control" step="0.01" max="<?= $subtotal - $invoice['payment_received'] ?>" required>
                    </div>
                     <div class="form-group">
                        <label for="payment_date">Date</label>
                        <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                     <div class="form-group">
                        <label for="payment_method">Method</label>
                         <select name="payment_method_id" class="form-control">
                             <option value="1">Cash</option>
                             <option value="2">Bank Transfer</option>
                             <option value="3">Credit Card</option>
                         </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
