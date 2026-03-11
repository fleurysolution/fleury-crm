<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold">Subscription Packages</h2>
            <p class="text-muted small mb-0">Manage SaaS plans and pricing for construction companies.</p>
        </div>
        <a href="<?= site_url('subscriptions/create') ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-2"></i> Create New Package</a>
    </div>

    <div class="row">
        <?php foreach ($packages as $pkg): ?>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0"><?= esc($pkg['name']) ?></h5>
                        <span class="badge bg-success">ACTIVE</span>
                    </div>
                    <div class="display-6 fw-bold mb-2">$<?= number_format($pkg['price'], 2) ?></div>
                    <p class="text-muted small"><?= $pkg['billing_interval'] == 'yearly' ? 'Per Year' : 'Per Month' ?> <?= $pkg['is_per_user'] ? '/ User' : '' ?></p>
                    
                    <p class="small text-dark mb-4"><?= esc($pkg['description']) ?></p>
                    
                    <div class="features-list mb-4">
                        <div class="small fw-bold text-muted mb-2">Features:</div>
                        <p class="small text-muted"><?= nl2br(esc($pkg['features'])) ?></p>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-4">
                    <button class="btn btn-light w-100 btn-sm">Edit Package</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>
