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
                <p class="auth-subtitle">Reset your password</p>
            </div>

            <div class="card">
                <?php if (isset($status) && $status) : ?>
                    <div class="alert alert-success">
                        <?= esc($status) ?>
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

                <p style="margin-bottom: 1.5rem; color: var(--text-muted); font-size: 0.9rem;">
                    Enter your email address and we'll send you a link to reset your password.
                </p>

                <?= form_open('auth/password/forgot') ?>
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required autofocus placeholder="name@company.com">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
                <?= form_close() ?>

                <div class="text-center mt-4">
                    <a href="<?= site_url('auth/signin') ?>" class="btn btn-outline btn-block">Back to Sign In</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
