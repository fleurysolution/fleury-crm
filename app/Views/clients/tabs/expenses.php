<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Expenses</h5>
    <button class="btn btn-sm btn-primary">New Expense</button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                 <?php if (empty($expenses)): ?>
                    <tr><td colspan="3" class="text-center py-3 text-muted">No expenses found.</td></tr>
                <?php else: ?>
                    <?php foreach ($expenses as $expense): ?>
                    <tr>
                        <td class="fw-bold"><?= esc($expense['title']) ?></td>
                        <td><?= esc($expense['expense_date']) ?></td>
                        <td><?= number_format($expense['amount'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
