<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Invoices</h5>
    <button class="btn btn-sm btn-primary">New Invoice</button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>ID</th>
                    <th>Bill Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                 <?php if (empty($invoices)): ?>
                    <tr><td colspan="5" class="text-center py-3 text-muted">No invoices found.</td></tr>
                <?php else: ?>
                    <?php foreach ($invoices as $invoice): ?>
                    <tr>
                        <td class="fw-bold text-primary">#<?= esc($invoice['id']) ?></td>
                        <td><?= esc($invoice['invoice_date'] ?? '') ?></td>
                        <td><?= esc($invoice['due_date'] ?? '') ?></td>
                        <td><span class="badge bg-secondary"><?= esc($invoice['status'] ?? '') ?></span></td>
                        <td><?= number_format($invoice['total_amount'] ?? 0, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
