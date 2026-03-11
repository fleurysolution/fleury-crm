<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Upgrade Your Plan</h2>
        <p class="text-muted">Scale your business with more features and capacity.</p>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
        <?php foreach ($packages as $pkg): ?>
        <div class="col" style="max-width: 350px;">
            <div class="card h-100 border-0 shadow-sm transition-hover">
                <div class="card-body p-4 text-center">
                    <h5 class="fw-bold text-uppercase text-primary mb-3"><?= esc($pkg['name']) ?></h5>
                    <div class="mb-4">
                        <span class="display-5 fw-bold"><?= esc($pkg['currency']) ?> <?= number_format($pkg['price'], 2) ?></span>
                        <span class="text-muted">/ <?= esc($pkg['billing_interval']) ?></span>
                    </div>
                    
                    <ul class="list-unstyled text-start mb-4">
                        <?php 
                        $features = explode("\n", $pkg['features']);
                        foreach ($features as $feature): 
                            if (trim($feature)):
                        ?>
                        <li class="mb-2">
                            <i class="fa-solid fa-check text-success me-2"></i><?= esc(trim($feature)) ?>
                        </li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>

                    <button class="btn btn-primary w-100 btn-lg rounded-pill" onclick="selectPlan(<?= $pkg['id'] ?>)">
                        Select Plan
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    async function selectPlan(pkgId) {
        if (confirm('Are you sure you want to change to this plan?')) {
            window.location.href = '<?= site_url('subscriptions/checkout') ?>/' + pkgId;
        }
    }
</script>

<style>
    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transition: all 0.3s ease;
    }
</style>
<?= $this->endSection() ?>
