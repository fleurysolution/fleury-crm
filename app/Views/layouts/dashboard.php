<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard') ?> - BPMS247</title>
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Styles are loaded from public/assets/css/style.css -->
</head>
<body>

<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">BPMS<span>247</span></div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-category">Main</div>
            <a href="<?= site_url('dashboard') ?>" class="nav-item <?= uri_string() == 'dashboard' ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="<?= site_url('leads') ?>" class="nav-item <?= strpos(uri_string(), 'leads') === 0 ? 'active' : '' ?>">
                <i class="fa-solid fa-filter"></i> Leads
            </a>
            <a href="<?= site_url('clients') ?>" class="nav-item <?= strpos(uri_string(), 'clients') === 0 ? 'active' : '' ?>">
                <i class="fa-solid fa-building"></i> Clients
            </a>
            <a href="<?= site_url('estimates') ?>" class="nav-item <?= strpos(uri_string(), 'estimates') === 0 ? 'active' : '' ?>">
                <i class="fa-solid fa-file-lines"></i> Estimates
            </a>
            <a href="<?= site_url('invoices') ?>" class="nav-item <?= strpos(uri_string(), 'invoices') === 0 ? 'active' : '' ?>">
                <i class="fa-solid fa-file-invoice-dollar"></i> Invoices
            </a>
            
            <div class="nav-category">Management</div>
            <a href="<?= site_url('team') ?>" class="nav-item <?= strpos(uri_string(), 'team') === 0 ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> Team
            </a>
            <?php if (in_array('admin', session()->get('user_roles') ?? [])): ?>
            <a href="<?= site_url('roles') ?>" class="nav-item <?= strpos(uri_string(), 'roles') === 0 ? 'active' : '' ?>">
                <i class="fa-solid fa-shield-halved"></i> Roles & Permissions
            </a>
            <?php endif; ?>
            <a href="<?= site_url('jobs') ?>" class="nav-item">
                <i class="fa-solid fa-briefcase"></i> Jobs
            </a>

            <div class="nav-category">Settings</div>
            <?php if (in_array('admin', session()->get('user_roles') ?? [])): ?>
            <a href="<?= site_url('settings') ?>" class="nav-item <?= strpos(uri_string(), 'settings') === 0 ? 'active' : '' ?>">
                <i class="fa-solid fa-cog"></i> Settings
            </a>
            <?php endif; ?>
        </nav>
        <div style="padding: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
             <form action="<?= site_url('auth/signout') ?>" method="post">
                <button type="submit" class="nav-item" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer;">
                    <i class="fa-solid fa-sign-out-alt"></i> Sign Out
                </button>
             </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="topbar">
            <h2 style="font-size: 1.25rem; font-weight: 600; margin: 0;"><?= esc($title ?? 'Page') ?></h2>
            <div class="user-menu">
                <span><?= session()->get('user_name') ?></span>
                <div class="user-avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
        </header>
        
        <div class="content-wrapper">
             <?php if (session()->getFlashdata('message')) : ?>
                <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </main>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
