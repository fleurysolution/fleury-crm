<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<form action="<?= site_url('auth/password/reset') ?>" method="post" class="general-form">
    <?= csrf_field() ?>

    <input type="hidden" name="token" value="<?= esc($token ?? '') ?>">

    <div class="mb-3">
        <label class="form-label"><?= esc(t('new_password', 'New password')) ?></label>
        <input
            type="password"
            name="password"
            placeholder="<?= esc(t('new_password', 'New password')) ?>"
            minlength="8"
            required
        >
    </div>

    <button class="w-100 btn btn-lg btn-primary" type="submit">
        <?= esc(t('reset_password', 'Reset password')) ?>
    </button>
</form>

<div class="mt-2">
    <a href="<?= site_url('auth/signin') ?>"><?= esc(t('back_to_signin', 'Back to sign in')) ?></a>
</div>

<?= $this->endSection() ?>
