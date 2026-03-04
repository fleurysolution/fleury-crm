<?= $this->extend('layouts/vendor_dashboard'); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Purchase Orders</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>PO Number</th>
                                <th>Title</th>
                                <th>Total Amount</th>
                                <th>Delivery Date</th>
                                <th>Status</th>
                                <th>Date Issued</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pos)): ?>
                                <?php foreach ($pos as $po): ?>
                                    <tr>
                                        <td><strong><?= esc($po['po_number']) ?></strong></td>
                                        <td><?= esc($po['title'] ?: 'N/A') ?></td>
                                        <td>$<?= number_format((float) $po['total_amount'], 2) ?></td>
                                        <td><?= $po['delivery_date'] ? date('M j, Y', strtotime((string)$po['delivery_date'])) : '<span class="text-muted">N/A</span>' ?></td>
                                        <td><span class="badge bg-success"><?= esc($po['status']) ?></span></td>
                                        <td><?= date('M j, Y', strtotime((string)$po['created_at'])) ?></td>
                                        <td>
                                            <a href="<?= site_url('procurement/pos/' . $po['id'] . '/pdf') ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="fa-solid fa-download"></i> Download PDF
                                            </a>
                                            <!-- Vendor Invoicing Feature could go here in a future step -->
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="alert('Invoicing feature coming soon')">
                                                <i class="fa-solid fa-file-invoice"></i> Submit Invoice
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No Purchase Orders found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
