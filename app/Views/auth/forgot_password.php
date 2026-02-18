<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<form action="<?= site_url('auth/password/forgot') ?>" method="post" class="general-form" autocomplete="on">
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

    <button class="w-100 btn btn-lg btn-primary" type="submit">
        <?= esc(t('send_reset_link', 'Send reset link')) ?>
    </button>
</form>

<div class="mt-2 d-flex justify-content-between flex-wrap gap-2">
    <a href="<?= site_url('auth/signin') ?>"><?= esc(t('back_to_signin', 'Back to sign in')) ?></a>
</div>

<?= $this->endSection() ?>
