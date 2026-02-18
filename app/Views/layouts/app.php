<!doctype html>
<html lang="<?= esc(service('request')->getLocale()) ?>">
<head>
    <?= view('partials/meta') ?>
    <?= view('partials/styles') ?>
</head>
<body class="bg-light">
    <?= view('partials/header') ?>

    <div class="d-flex app-shell">
        <?= view('partials/sidebar') ?>

        <main class="app-content flex-grow-1 d-flex flex-column min-vh-100">
            <div class="container-fluid py-4">
                <?= $this->renderSection('content') ?>
            </div>
            <?= view('partials/footer') ?>
        </main>
    </div>

    <?= view('partials/scripts', ['pageScript' => $pageScript ?? null]) ?>
</body>
</html>
