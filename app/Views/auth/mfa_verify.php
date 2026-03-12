<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> | BPMS247</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --accent-color: #38bdf8;
        }
        body {
            background: var(--primary-gradient);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            color: #f8fafc;
        }
        .mfa-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-container img {
            height: 50px;
            filter: drop-shadow(0 0 10px rgba(56, 189, 248, 0.3));
        }
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 1.1rem;
            text-align: center;
            letter-spacing: 0.5rem;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2);
            color: #fff;
        }
        .btn-mfa {
            background: var(--accent-color);
            border: none;
            color: #0f172a;
            font-weight: 700;
            padding: 14px;
            border-radius: 12px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }
        .btn-mfa:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(56, 189, 248, 0.4);
            background: #7dd3fc;
        }
    </style>
</head>
<body>
    <div class="mfa-card">
        <div class="logo-container">
            <img src="<?= base_url('assets/img/logo-white.png') ?>" alt="BPMS247">
        </div>
        <h4 class="text-center fw-bold mb-2">Two-Step Verification</h4>
        <p class="text-center text-secondary small mb-4">Enter the 6-digit code from your authenticator app.</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger small">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <?= implode('<br>', $errors) ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('auth/mfa-verify') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <input type="text" name="mfa_code" class="form-control" maxlength="6" placeholder="000000" required autofocus autocomplete="off">
            </div>
            <button type="submit" class="btn btn-mfa">Verify Code</button>
        </form>

        <div class="mt-4 text-center">
            <a href="<?= site_url('auth/signin') ?>" class="text-decoration-none text-secondary small">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Sign In
            </a>
        </div>
    </div>
</body>
</html>
