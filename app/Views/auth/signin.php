<?php
/**
 * Modern Split-Screen Login View
 * Styling via public/assets/css/auth.css
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <!-- Include Custom Auth CSS -->
    <link href="<?= base_url('assets/css/auth.css') ?>" rel="stylesheet">
    <!-- Include Inter Font for crisp typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

    <div class="auth-split">
        <!-- Visual Left Side -->
        <div class="auth-visual">
            <div class="glass-shapes">
                <div class="glass-shape shape-1"></div>
                <div class="glass-shape shape-2"></div>
                <div class="glass-shape shape-3"></div>
            </div>
            <div class="auth-visual-content">
                <h1>Next Generation<br>Business Management</h1>
                <p>Welcome to BPMS247. Elevate your operational efficiency, manage your workflow effortlessly, and unlock new possibilities with our premium enterprise solutions.</p>
            </div>
        </div>

        <!-- Form Right Side -->
        <div class="auth-form-wrapper">
            <div class="brand-header">
                <h2>BPMS<span>247</span></h2>
                <p class="auth-subtitle">Welcome back! Please enter your details.</p>
            </div>

            <div class="auth-card">
                <?php if (session()->getFlashdata('message')) : ?>
                    <div class="custom-alert alert-success">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        <?= session()->getFlashdata('message') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="custom-alert alert-error">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($errors) && $errors) : ?>
                    <div class="custom-alert alert-error">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="align-self: flex-start; margin-top: 0.125rem;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <div>
                            <ul style="margin: 0; padding-left: 1rem;">
                            <?php foreach ($errors as $error) : ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <?= form_open('auth/signin') ?>
                    <div class="form-floating">
                        <!-- Note: The blank placeholder is a CSS trick required for :placeholder-shown to work effectively across browsers -->
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" placeholder=" " tabindex="1" required autofocus>
                        <label for="email">Email Address</label>
                    </div>

                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" placeholder=" " tabindex="2" required>
                        <label for="password">Password</label>
                    </div>

                    <div class="forgot-password">
                        <a href="<?= site_url('auth/password/forgot') ?>" tabindex="4">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-auth" tabindex="3">Sign In</button>
                <?= form_close() ?>
            </div>
            
            <p class="auth-footer">
                Don't have an account? <a href="#">Contact Sales</a>
            </p>
        </div>
    </div>

</body>
</html>
