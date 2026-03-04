<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Success') ?> · BPMS247</title>

    <!-- Bootstrap 5 CSS – local -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/fontawesome/all.min.css') ?>">
    <style>
        body {
            background-color: #f8f9fc;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .auth-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 3rem;
            max-width: 500px;
            width: 100%;
            margin: auto;
            text-align: center;
            border: 1px solid #e9ecef;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="auth-card mx-auto">
        <div class="mb-4 text-success">
            <i class="fa-solid fa-circle-check" style="font-size: 4rem;"></i>
        </div>
        <h3 class="mb-3"><?= esc($title) ?></h3>
        <p class="text-muted mb-4 fs-5"><?= esc($message) ?></p>

        <a href="<?= site_url('auth/signin') ?>" class="btn btn-primary">Return to Sign In</a>
    </div>
</div>

</body>
</html>
