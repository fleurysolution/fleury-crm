<?= $this->extend('layouts/vendor_dashboard'); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Bids & Quotes</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                
                <!-- Bidding from Vendor side usually requires allowing them to see open packages or submit one.
                     For now we just list their history. -->
                
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Trade Package</th>
                                <th>Bid Amount</th>
                                <th>Status</th>
                                <th>Date Submitted</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($bids)): ?>
                                <?php foreach ($bids as $bid): ?>
                                    <tr>
                                        <td><strong><?= esc($bid['trade_package']) ?></strong></td>
                                        <td>$<?= number_format((float) $bid['bid_amount'], 2) ?></td>
                                        <td>
                                            <?php if ($bid['status'] === 'Awarded'): ?>
                                                <span class="badge bg-success">Awarded</span>
                                            <?php elseif ($bid['status'] === 'Rejected'): ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('M j, Y', strtotime((string)$bid['created_at'])) ?></td>
                                        <td><?= esc($bid['remarks'] ?: '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No Bids or Quotes found.</td>
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
