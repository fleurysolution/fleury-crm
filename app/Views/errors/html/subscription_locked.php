<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/fontawesome/all.min.css') ?>">
    <style>
        body { background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: 'Inter', sans-serif; }
        .locked-card { max-width: 500px; padding: 40px; background: white; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); text-align: center; }
        .lock-icon { font-size: 60px; color: #f43f5e; margin-bottom: 20px; }
        .btn-pay { background: #4facfe; color: white; border: none; padding: 12px 30px; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>
    <div class="locked-card">
        <div class="lock-icon"><i class="fa-solid fa-lock"></i></div>
        <h2 class="fw-bold mb-3">Access Blocked</h2>
        <p class="text-muted mb-4">Your construction company subscription has expired or is inactive. Please renew your plan to continue using BPMS247 CRM.</p>
        <a href="<?= site_url('subscription/renew') ?>" class="btn-pay">Renew Subscription</a>
        <div class="mt-4">
            <a href="<?= site_url('auth/signout') ?>" class="text-muted small">Sign Out</a>
        </div>
    </div>
</body>
</html>
