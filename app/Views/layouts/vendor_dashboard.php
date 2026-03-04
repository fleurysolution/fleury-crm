<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Vendor Dashboard') ?> · BPMS247</title>

    <!-- Bootstrap 5 CSS – local -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Font Awesome 6 – local -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/fontawesome/all.min.css') ?>">
    <!-- Admin stylesheet -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

    <style>
        body { font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; }
    </style>
</head>
<body class="admin-body">

<!-- Mobile overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="dashboard-layout">

    <!-- ═══ SIDEBAR ═══ -->
    <aside class="sidebar" id="sidebar">

        <div class="sidebar-header">
            <a href="<?= site_url('vendor-portal/dashboard') ?>" class="sidebar-brand">
                BPMS<span>247 Vendor</span>
            </a>
        </div>

        <nav class="sidebar-nav">
            <?php 
                $uri = uri_string();
            ?>

            <div class="sidebar-category">Vendor Portal</div>
            <a href="<?= site_url('vendor-portal/dashboard') ?>"
               class="sidebar-link <?= $uri === 'vendor-portal/dashboard' ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="<?= site_url('vendor-portal/pos') ?>"
               class="sidebar-link <?= str_starts_with($uri, 'vendor-portal/pos') ? 'active' : '' ?>">
                <i class="fa-solid fa-file-invoice-dollar"></i> Purchase Orders
            </a>
            <a href="<?= site_url('vendor-portal/bids') ?>"
               class="sidebar-link <?= str_starts_with($uri, 'vendor-portal/bids') ? 'active' : '' ?>">
                <i class="fa-solid fa-file-signature"></i> Bids & Quotes
            </a>
            <a href="<?= site_url('vendor-portal/tasks') ?>"
               class="sidebar-link <?= str_starts_with($uri, 'vendor-portal/tasks') ? 'active' : '' ?>">
                <i class="fa-solid fa-list-check"></i> Assigned Tasks
            </a>
            
        </nav>

        <div class="sidebar-footer" style="padding: 1rem;">
            <form action="<?= site_url('auth/signout') ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-danger w-100">
                    <i class="fa-solid fa-right-from-bracket"></i> Sign Out
                </button>
            </form>
        </div>

    </aside>
    <!-- /SIDEBAR -->

    <!-- ═══ MAIN CONTENT ═══ -->
    <div class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="d-lg-none btn btn-sm btn-light border-0 p-2"
                        onclick="openSidebar()" aria-label="Open menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2 class="topbar-title mb-0"><?= esc($title ?? 'Vendor Dashboard') ?></h2>
            </div>

            <div class="user-menu d-flex align-items-center gap-1">
                <?php if (session()->get('is_logged_in')): ?>
                <span class="d-none d-md-inline text-muted" style="font-size:.875rem;">
                    <?= esc(session()->get('user_name') ?: session()->get('user_email')) ?>
                </span>
                <div class="user-avatar" title="<?= esc(session()->get('user_name') ?? '') ?>">
                    <?= strtoupper(substr((string)(session()->get('user_name') ?: 'V'), 0, 1)) ?>
                </div>
                <?php endif; ?>
            </div>
        </header>
        <!-- /TOPBAR -->

        <!-- PAGE CONTENT -->
        <div class="content-wrapper">

            <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                <?= esc(session()->getFlashdata('message')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>

        </div>
        <!-- /PAGE CONTENT -->

    </div>
    <!-- /MAIN CONTENT -->

</div>
<!-- /dashboard-layout -->

<!-- Bootstrap Bundle JS – local -->
<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- jQuery – local -->
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>

<script>
function openSidebar() {
    document.getElementById('sidebar').classList.add('show');
    document.getElementById('sidebarOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('show');
    document.getElementById('sidebarOverlay').classList.remove('show');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
</script>

</body>
</html>
