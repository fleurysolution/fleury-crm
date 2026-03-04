<?= $this->extend('layouts/dashboard'); ?>

<?= $this->section('content'); ?>
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4 class="page-title">Vendor Applications</h4>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Tabs for Status -->
                <ul class="nav nav-pills mb-3">
                    <li class="nav-item">
                        <a href="<?= site_url('vendor-applications?status=pending') ?>" class="nav-link <?= $currentStatus === 'pending' ? 'active' : '' ?>">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url('vendor-applications?status=approved') ?>" class="nav-link <?= $currentStatus === 'approved' ? 'active' : '' ?>">Approved</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url('vendor-applications?status=rejected') ?>" class="nav-link <?= $currentStatus === 'rejected' ? 'active' : '' ?>">Rejected</a>
                    </li>
                </ul>

                <div class="table-responsive">
                    <table class="table table-centered table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Company</th>
                                <th>Contact</th>
                                <th>Trade</th>
                                <th>Status</th>
                                <th>Date Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($applications)): ?>
                                <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td><?= esc($app['company_name']) ?></td>
                                        <td>
                                            <?= esc($app['contact_name']) ?><br>
                                            <small class="text-muted"><?= esc($app['email']) ?></small>
                                        </td>
                                        <td><?= esc($app['trade_type'] ?: 'N/A') ?></td>
                                        <td>
                                            <?php if ($app['status'] === 'pending'): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php elseif ($app['status'] === 'approved'): ?>
                                                <span class="badge bg-success">Approved</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('M j, Y', strtotime((string) $app['created_at'])) ?></td>
                                        <td>
                                            <a href="<?= site_url('vendor-applications/' . $app['id']) ?>" class="btn btn-sm btn-info">Review</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No <?= esc($currentStatus) ?> applications found.</td>
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
