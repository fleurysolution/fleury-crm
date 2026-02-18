<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-semibold" href="<?= site_url('/') ?>"><?= esc(t('app_name')) ?></a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#appNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div id="appNavbar" class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>"><?= esc(t('dashboard')) ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('approval/requests') ?>"><?= esc(t('approvals')) ?></a></li>
            </ul>

            <?php $currentLocale = service('request')->getLocale(); ?>
                <div class="dropdown me-3">
                <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <?= strtoupper(esc($currentLocale)) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php foreach (config('App')->supportedLocales as $loc): ?>
                    <li>
                        <a class="dropdown-item <?= $currentLocale === $loc ? 'active' : '' ?>"
                        href="<?= site_url('locale/' . $loc) ?>">
                        <?= strtoupper(esc($loc)) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                </div>

            <ul class="navbar-nav ms-auto">
                <?php if (session('is_logged_in')): ?>
                    <li class="nav-item me-2 text-light small align-self-center">
                        <?= esc(session('user_name') ?: session('user_email')) ?>
                    </li>
                    <li class="nav-item">
                        <form method="post" action="<?= site_url('auth/signout') ?>">
                            <?= csrf_field() ?>
                            <button class="btn btn-outline-light btn-sm"><?= esc(t('sign_out')) ?></button>
                        </form>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm" href="<?= site_url('auth/signin') ?>"><?= esc(t('sign_in')) ?></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
