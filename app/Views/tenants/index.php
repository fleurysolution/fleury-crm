<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold">Organization Management</h2>
            <p class="text-muted small mb-0">Manage construction companies and their subscription status.</p>
        </div>
        <div class="d-flex gap-2">
            <div class="btn-group btn-group-sm">
                <a href="?status=active" class="btn btn-outline-success">Active</a>
                <a href="?status=trialing" class="btn btn-outline-info">Trialing</a>
                <a href="?status=past_due" class="btn btn-outline-warning">Past Due</a>
                <a href="?status=expired" class="btn btn-outline-danger">Expired</a>
            </div>
            <a href="<?= site_url('signup') ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-2"></i> Onboard New Company</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Company Name</th>
                            <th>Industry</th>
                            <th>Employees</th>
                            <th>Region/Currency</th>
                            <th>Subscription</th>
                            <th>Expires</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tenants as $tenant): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark"><?= esc($tenant['name']) ?></div>
                                <small class="text-muted">ID: #<?= $tenant['id'] ?></small>
                            </td>
                            <td><?= esc($tenant['industry'] ?: 'Construction') ?></td>
                            <td><?= esc($tenant['employee_count']) ?></td>
                            <td>
                                <div><?= esc($tenant['country']) ?></div>
                                <small class="text-muted"><?= esc($tenant['currency']) ?></small>
                            </td>
                            <td>
                                <?php 
                                    $badgeClass = 'bg-secondary';
                                    if ($tenant['subscription_status'] === 'active') $badgeClass = 'bg-success';
                                    if ($tenant['subscription_status'] === 'trialing') $badgeClass = 'bg-info';
                                    if ($tenant['subscription_status'] === 'past_due') $badgeClass = 'bg-warning';
                                    if ($tenant['subscription_status'] === 'expired') $badgeClass = 'bg-danger';
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= strtoupper($tenant['subscription_status']) ?></span>
                            </td>
                            <td><?= $tenant['subscription_end'] ?></td>
                            <td class="text-end pe-4">
                                <button class="btn btn-light btn-sm"><i class="fa-solid fa-eye"></i></button>
                                <button class="btn btn-light btn-sm text-primary"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-light btn-sm text-danger"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
