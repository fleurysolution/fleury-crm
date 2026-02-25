<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard') ?> · BPMS247</title>

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
            <a href="<?= site_url('dashboard') ?>" class="sidebar-brand">
                BPMS<span>247</span>
            </a>
        </div>

        <nav class="sidebar-nav">
            <?php $uri = uri_string(); ?>

            <div class="sidebar-category">Main</div>
            <a href="<?= site_url('dashboard') ?>"
               class="sidebar-link <?= $uri === 'dashboard' ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="<?= site_url('leads') ?>"
               class="sidebar-link <?= str_starts_with($uri, 'leads') ? 'active' : '' ?>">
                <i class="fa-solid fa-filter"></i> Leads
            </a>
            <a href="<?= site_url('clients') ?>"
               class="sidebar-link <?= str_starts_with($uri, 'clients') ? 'active' : '' ?>">
                <i class="fa-solid fa-building"></i> Clients
            </a>

            <div class="sidebar-category">Finance</div>
            <a href="<?= site_url('estimates') ?>"
               class="sidebar-link <?= str_starts_with($uri, 'estimates') ? 'active' : '' ?>">
                <i class="fa-solid fa-file-lines"></i> Estimates
            </a>
            <a href="<?= site_url('invoices') ?>"
               class="sidebar-link <?= str_starts_with($uri, 'invoices') ? 'active' : '' ?>">
                <i class="fa-solid fa-file-invoice-dollar"></i> Invoices
            </a>

            <div class="sidebar-category">Management</div>
            <a href="<?= site_url('team') ?>"
               class="sidebar-link <?= str_starts_with($uri, 'team') ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> Team
            </a>
            <?php if (in_array('admin', session()->get('user_roles') ?? [])): ?>
            <a href="<?= site_url('roles') ?>"
               class="sidebar-link <?= str_starts_with($uri, 'roles') ? 'active' : '' ?>">
                <i class="fa-solid fa-shield-halved"></i> Roles &amp; Permissions
            </a>
            <?php endif; ?>
            <a href="<?= site_url('approval/requests') ?>"
               class="sidebar-link <?= str_starts_with($uri, 'approval') ? 'active' : '' ?>">
                <i class="fa-solid fa-diagram-project"></i> Approvals
            </a>

            <!-- Single Settings entry — sub-nav is rendered by settings/layout.php -->
            <div class="sidebar-category">Configure</div>
            <a href="<?= site_url('settings/general') ?>"
               class="sidebar-link <?= str_starts_with($uri, 'settings') ? 'active' : '' ?>">
                <i class="fa-solid fa-sliders"></i> Settings
            </a>

        </nav>

        <div class="sidebar-footer">
            <form action="<?= site_url('auth/signout') ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="sidebar-link w-100">
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
                <h2 class="topbar-title mb-0"><?= esc($title ?? 'Dashboard') ?></h2>
            </div>

            <div class="user-menu">
                <?php if (session()->get('is_logged_in')): ?>
                <span class="d-none d-md-inline text-muted" style="font-size:.875rem;">
                    <?= esc(session()->get('user_name') ?: session()->get('user_email')) ?>
                </span>
                <div class="user-avatar" title="<?= esc(session()->get('user_name') ?? '') ?>">
                    <?= strtoupper(substr((string)(session()->get('user_name') ?: 'U'), 0, 1)) ?>
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
