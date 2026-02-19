<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
    <div>
        <a href="<?= site_url('estimates/' . $estimate['id'] . '/convert') ?>" class="btn btn-success btn-sm">
            <i class="fas fa-check"></i> Convert to Invoice
        </a>
        <a href="<?= site_url('estimates/edit/' . $estimate['id']) ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-edit"></i> Edit
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-sm-6">
                <h5 class="font-weight-bold">Estimate #<?= $estimate['id'] ?></h5>
                <p>
                    <strong>Date:</strong> <?= date('Y-m-d', strtotime($estimate['estimate_date'])) ?><br>
                    <strong>Valid Until:</strong> <?= date('Y-m-d', strtotime($estimate['valid_until'])) ?><br>
                    <strong>Status:</strong> <?= ucfirst($estimate['status']) ?>
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
                    <?php $itemTotal = $item['quantity'] * $item['rate']; $subtotal += $itemTotal; ?>
                    <tr>
                        <td><?= esc($item['title']) ?></td>
                        <td><?= esc($item['description']) ?></td>
                        <td class="text-right"><?= $item['quantity'] ?></td>
                        <td class="text-right"><?= number_format($item['rate'], 2) ?></td>
                        <td class="text-right"><?= number_format($itemTotal, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right font-weight-bold">Total</td>
                    <td class="text-right font-weight-bold"><?= number_format($subtotal, 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <?php if ($estimate['note']): ?>
            <div class="mt-4">
                <strong>Note:</strong><br>
                <?= nl2br(esc($estimate['note'])) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
