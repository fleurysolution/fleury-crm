<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<form action="<?= site_url('auth/signin') ?>" method="post" class="general-form" autocomplete="on">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label class="form-label"><?= esc(t('email', 'Email')) ?></label>
        <input
            type="email"
            name="email"
            value="<?= esc(old('email')) ?>"
            placeholder="<?= esc(t('email', 'Email')) ?>"
            required
            autofocus
        >
    </div>

    <div class="mb-2">
        <label class="form-label"><?= esc(t('password', 'Password')) ?></label>
        <input
            type="password"
            name="password"
            placeholder="<?= esc(t('password', 'Password')) ?>"
            required
        >
    </div>

    <?php if (!empty($redirect)): ?>
        <input type="hidden" name="redirect" value="<?= esc($redirect) ?>">
    <?php endif; ?>

    <?php if (!empty($packageId)): ?>
        <input type="hidden" name="packageId" value="<?= esc($packageId) ?>">
    <?php endif; ?>

    <button class="w-100 btn btn-lg btn-primary" type="submit">
        <?= esc(t('sign_in', 'Sign in')) ?>
    </button>
</form>

<div class="mt-2">
    <a href="<?= site_url('auth/password/forgot') ?>">
        <?= esc(t('forgot_password', 'Forgot password?')) ?>
    </a>
</div>

<?= $this->endSection() ?>
