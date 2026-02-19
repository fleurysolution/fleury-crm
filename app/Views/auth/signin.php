<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="auth-layout">
        <div class="auth-container">
            <div class="auth-header">
                <h2 style="font-size: 2rem; font-weight: 800; color: var(--primary-color); letter-spacing: -1px;">BPMS<span style="color: var(--accent-color);">247</span></h2>
                <p class="auth-subtitle">Sign in to your account</p>
            </div>

            <div class="card">
                <?php if (session()->getFlashdata('message')) : ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('message') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($errors) && $errors) : ?>
                    <div class="alert alert-danger">
                        <ul style="margin-left: 1.5rem;">
                        <?php foreach ($errors as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?= form_open('auth/signin') ?>
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required autofocus placeholder="name@company.com">
                    </div>

                    <div class="form-group">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <label for="password" class="form-label" style="margin-bottom: 0;">Password</label>
                            <a href="<?= site_url('auth/password/forgot') ?>" style="font-size: 0.875rem;">Forgot password?</a>
                        </div>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                <?= form_close() ?>
            </div>
            
            <p class="text-center" style="margin-top: 2rem; font-size: 0.875rem; color: var(--text-muted);">
                Don't have an account? <a href="#">Contact Sales</a>
            </p>
        </div>
    </div>

</body>
</html>
