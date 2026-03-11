<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/fontawesome/all.min.css') ?>">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --card-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        body {
            background-color: #f8fafc;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #334155;
        }
        .signup-header {
            background: var(--primary-gradient);
            padding: 80px 0 120px;
            color: white;
            text-align: center;
        }
        .pricing-container {
            margin-top: -60px;
        }
        .pricing-card {
            background: white;
            border: none;
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .pricing-card:hover {
            transform: translateY(-10px);
        }
        .pricing-card.featured {
            border: 2px solid #4facfe;
            position: relative;
        }
        .featured-badge {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: #4facfe;
            color: white;
            padding: 5px 20px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .plan-name { font-weight: 700; font-size: 1.5rem; margin-bottom: 5px; }
        .plan-price { font-size: 3rem; font-weight: 800; color: #1e293b; margin: 20px 0; }
        .plan-price span { font-size: 1rem; color: #64748b; font-weight: 400; }
        .feature-list { list-style: none; padding: 0; margin: 30px 0; flex-grow: 1; }
        .feature-list li { margin-bottom: 12px; display: flex; align-items: center; gap: 10px; }
        .feature-list li i { color: #10b981; }
        .btn-select {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-weight: 600;
            transition: opacity 0.3s;
        }
        .btn-select:hover { color: white; opacity: 0.9; }
    </style>
</head>
<body>

<header class="signup-header">
    <div class="container">
        <h1 class="display-4 fw-bold">Select Your Power Plan</h1>
        <p class="lead opacity-75">Join thousands of construction companies optimizing their operations with BPMS247.</p>
    </div>
</header>

<main class="container pricing-container pb-5">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger mb-4"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="row g-4 justify-content-center">
        <?php foreach ($packages as $pkg): ?>
        <div class="col-lg-4 col-md-6">
            <div class="pricing-card <?= $pkg['name'] === 'Pro Plan (Per User)' ? 'featured' : '' ?>">
                <?php if ($pkg['name'] === 'Pro Plan (Per User)'): ?>
                    <div class="featured-badge">MOST POPULAR</div>
                <?php endif; ?>
                
                <div class="plan-name"><?= esc($pkg['name']) ?></div>
                <div class="text-muted small"><?= esc($pkg['description']) ?></div>
                
                <div class="plan-price">
                    $<?= number_format($pkg['price'], 0) ?>
                    <span>/ <?= $pkg['is_per_user'] ? 'user / ' : '' ?><?= $pkg['billing_interval'] === 'monthly' ? 'mo' : 'yr' ?></span>
                </div>

                <ul class="feature-list">
                    <?php $features = json_decode($pkg['features'], true) ?: []; ?>
                    <?php foreach ($features as $f): ?>
                        <li><i class="fa-solid fa-circle-check"></i> <?= esc($f) ?></li>
                    <?php endforeach; ?>
                </ul>

                <a href="<?= site_url('signup/account/' . $pkg['id']) ?>" class="btn btn-select text-center text-decoration-none">
                    Select Plan <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<footer class="text-center py-5">
    <p class="text-muted small">&copy; <?= date('Y') ?> BPMS247 Construction ERP. All rights reserved.</p>
</footer>

</body>
</html>
