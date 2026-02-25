<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?php $uri = uri_string(); $currentTab = $tab ?? 'general'; ?>

<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-gear me-2 text-primary"></i>System Settings</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active">Settings</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-3 align-items-start">

    <!-- ══ SIDEBAR ══════════════════════════════════════════════════════ -->
    <div class="col-lg-3 col-md-4">
        <div class="card border-0 shadow-sm" id="settingsSidebarCard" style="position:sticky;top:76px;border-radius:14px;overflow:hidden;">
            <!-- Header -->
            <div class="px-3 py-3 text-white"
                 style="background:linear-gradient(135deg,#4a90e2 0%,#6f42c1 100%);">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-sliders fa-lg"></i>
                    <span class="fw-semibold">Navigation</span>
                </div>
            </div>

            <!-- Scrollable nav list -->
            <div style="max-height:76vh;overflow-y:auto;overflow-x:hidden;" class="pb-2">

                <?php
                $navGroups = [
                    'Core' => [
                        ['tab'=>'general',           'icon'=>'fa-building',        'label'=>'General',             'url'=>'settings/general'],
                        ['tab'=>'email',             'icon'=>'fa-envelope',        'label'=>'Email / SMTP',        'url'=>'settings/email'],
                        ['tab'=>'localization',      'icon'=>'fa-globe',           'label'=>'Localization',        'url'=>'settings/localization'],
                        ['tab'=>'modules',           'icon'=>'fa-cubes',           'label'=>'Modules',             'url'=>'settings/modules'],
                        ['tab'=>'notifications',     'icon'=>'fa-bell',            'label'=>'Notifications',       'url'=>'settings/notifications'],
                    ],
                    'Security & Access' => [
                        ['tab'=>'ip_restriction',    'icon'=>'fa-ban',             'label'=>'IP Restriction',      'url'=>'settings/ip_restriction'],
                        ['tab'=>'client_permissions','icon'=>'fa-user-lock',       'label'=>'Client Permissions',  'url'=>'settings/client_permissions'],
                        ['tab'=>'gdpr',              'icon'=>'fa-shield-halved',   'label'=>'GDPR',                'url'=>'settings/gdpr'],
                        ['tab'=>'rbac',              'icon'=>'fa-user-shield',     'label'=>'Roles & Permissions', 'url'=>'settings/rbac'],
                        ['tab'=>'approval_workflows','icon'=>'fa-diagram-project', 'label'=>'Approval Workflows',  'url'=>'settings/approval_workflows'],
                    ],
                    'Finance' => [
                        ['tab'=>'invoices',          'icon'=>'fa-file-invoice',    'label'=>'Invoices',            'url'=>'settings/invoices'],
                        ['tab'=>'estimates',         'icon'=>'fa-file-lines',      'label'=>'Estimates',           'url'=>'settings/estimates'],
                        ['tab'=>'contracts',         'icon'=>'fa-file-contract',   'label'=>'Contracts',           'url'=>'settings/contracts'],
                        ['tab'=>'proposals',         'icon'=>'fa-handshake',       'label'=>'Proposals',           'url'=>'settings/proposals'],
                        ['tab'=>'orders',            'icon'=>'fa-cart-shopping',   'label'=>'Orders',              'url'=>'settings/orders'],
                        ['tab'=>'subscriptions',     'icon'=>'fa-rotate',          'label'=>'Subscriptions',       'url'=>'settings/subscriptions'],
                        ['tab'=>'store',             'icon'=>'fa-store',           'label'=>'Store',               'url'=>'settings/store'],
                    ],
                    'Projects & Tasks' => [
                        ['tab'=>'projects',          'icon'=>'fa-folder-open',     'label'=>'Projects',            'url'=>'settings/projects'],
                        ['tab'=>'tasks',             'icon'=>'fa-list-check',      'label'=>'Tasks',               'url'=>'settings/tasks'],
                        ['tab'=>'events',            'icon'=>'fa-calendar-days',   'label'=>'Events',              'url'=>'settings/events'],
                    ],
                    'Support & CRM' => [
                        ['tab'=>'tickets',           'icon'=>'fa-ticket',          'label'=>'Tickets',             'url'=>'settings/tickets'],
                        ['tab'=>'imap_settings',     'icon'=>'fa-inbox',           'label'=>'IMAP',                'url'=>'settings/imap_settings'],
                        ['tab'=>'leads',             'icon'=>'fa-user-plus',       'label'=>'Leads',               'url'=>'settings/leads'],
                    ],
                    'UI & Integrations' => [
                        ['tab'=>'footer',            'icon'=>'fa-align-center',    'label'=>'Footer',              'url'=>'settings/footer'],
                        ['tab'=>'top_menu',          'icon'=>'fa-bars',            'label'=>'Top Menu',            'url'=>'settings/top_menu'],
                        ['tab'=>'pwa',               'icon'=>'fa-mobile-screen',   'label'=>'PWA',                 'url'=>'settings/pwa'],
                        ['tab'=>'integration',       'icon'=>'fa-plug',            'label'=>'Integrations',        'url'=>'settings/integration'],
                        ['tab'=>'cron_job',          'icon'=>'fa-clock',           'label'=>'Cron Job',            'url'=>'settings/cron_job'],
                        ['tab'=>'db_backup',         'icon'=>'fa-database',        'label'=>'DB Backup',           'url'=>'settings/db_backup'],
                    ],
                    'Branch Structure' => [
                        ['tab'=>'regions',           'icon'=>'fa-map',             'label'=>'Regions',             'url'=>'settings/branches/regions'],
                        ['tab'=>'offices',           'icon'=>'fa-building-columns','label'=>'Offices',             'url'=>'settings/branches/offices'],
                        ['tab'=>'divisions',         'icon'=>'fa-layer-group',     'label'=>'Divisions',           'url'=>'settings/branches/divisions'],
                    ],
                ];
                foreach ($navGroups as $groupLabel => $items):
                ?>
                <div class="settings-nav-group-label"><?= esc($groupLabel) ?></div>
                <?php foreach ($items as $item):
                    $isActive = ($currentTab === $item['tab'])
                        || (in_array($item['tab'], ['regions','offices','divisions']) && str_contains($uri, $item['tab']));
                ?>
                <a href="<?= site_url($item['url']) ?>"
                   class="settings-nav-link <?= $isActive ? 'active' : '' ?>">
                    <i class="fa-solid <?= esc($item['icon']) ?> settings-nav-icon"></i>
                    <span><?= esc($item['label']) ?></span>
                </a>
                <?php endforeach; endforeach; ?>

            </div><!-- /scroll area -->
        </div>
    </div><!-- /sidebar -->

    <!-- ══ MAIN CONTENT ═════════════════════════════════════════════ -->
    <div class="col-lg-9 col-md-8">

        <!-- Flash alerts -->
        <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= esc(session()->getFlashdata('message')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="fa-solid fa-circle-xmark me-2"></i><?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Content card -->
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body p-4">
                <?= $this->renderSection('settings_content') ?>
            </div>
        </div>

    </div><!-- /main -->

</div><!-- /row -->

<!-- Toast container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;" id="settingsToastWrap"></div>

<!-- ══ STYLES ═══════════════════════════════════════════════════════ -->
<style>
/* ── Sidebar nav ── */
.settings-nav-group-label {
    font-size: .66rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #9ca3af;
    padding: .9rem 1rem .35rem;
    border-top: 1px solid #f3f4f6;
}
.settings-nav-group-label:first-child { border-top: none; }

.settings-nav-link {
    display: flex;
    align-items: center;
    gap: .55rem;
    padding: .45rem .9rem .45rem 1rem;
    font-size: .855rem;
    font-weight: 500;
    color: #374151;
    text-decoration: none;
    border-radius: 8px;
    margin: 1px .5rem;
    transition: background .13s, color .13s, transform .1s;
}
.settings-nav-link:hover {
    background: rgba(74,144,226,.1);
    color: #4a90e2;
    transform: translateX(2px);
}
.settings-nav-link.active {
    background: linear-gradient(135deg, #4a90e2, #6f42c1);
    color: #fff;
    box-shadow: 0 3px 10px rgba(74,144,226,.3);
}
.settings-nav-icon {
    width: 15px;
    text-align: center;
    flex-shrink: 0;
    font-size: .8rem;
}

/* ── Form elements ── */
.settings-section-hdr {
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #6b7280;
    padding-bottom: .5rem;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 1.2rem;
    margin-top: 1.8rem;
}
.settings-section-hdr:first-child { margin-top: 0; }

.form-label { font-weight: 600; font-size: .875rem; color: #374151; }
.form-control, .form-select {
    border-radius: 8px;
    border: 1.5px solid #e5e7eb;
    font-size: .875rem;
    transition: border-color .15s;
}
.form-control:focus, .form-select:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74,144,226,.15);
}

/* ── Toggle row ── */
.toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .7rem 0;
    border-bottom: 1px solid #f3f4f6;
}
.toggle-row:last-child { border-bottom: none; }
.toggle-row .toggle-label strong { font-size: .875rem; color: #374151; }
.toggle-row .toggle-label small  { font-size: .775rem; color: #6b7280; display: block; }
.form-switch .form-check-input   { width: 2.5em; height: 1.35em; cursor: pointer; }

/* ── Save button ── */
.btn-save {
    background: linear-gradient(135deg, #4a90e2, #6f42c1);
    border: none;
    color: #fff;
    padding: .55rem 1.8rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: .875rem;
    transition: opacity .18s, transform .1s;
}
.btn-save:hover  { opacity: .88; color: #fff; transform: translateY(-1px); }
.btn-save:active { transform: translateY(0); }
.btn-save:disabled { opacity: .55; }
</style>

<!-- ══ AJAX SAVE ════════════════════════════════════════════════════ -->
<script>
/* Toast helper */
function settingsToast(msg, type) {
    const id = 'st_' + Date.now();
    const icon = type === 'success' ? 'fa-circle-check' : 'fa-circle-xmark';
    const bg   = type === 'success' ? '#0f9d58'         : '#d32f2f';
    document.getElementById('settingsToastWrap').insertAdjacentHTML('beforeend', `
        <div id="${id}" class="toast align-items-center text-white border-0 shadow-sm mb-2"
             style="background:${bg};border-radius:10px;" role="alert">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="fa-solid ${icon}"></i> ${msg}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`);
    const el = document.getElementById(id);
    new bootstrap.Toast(el, { delay: 3200 }).show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
}

/* Bind all .settings-ajax-form forms */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.settings-ajax-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = form.querySelector('[type="submit"]');
            const orig = btn ? btn.innerHTML : '';
            if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…'; }

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(d => settingsToast(d.message || (d.success ? 'Saved!' : 'Error'), d.success ? 'success' : 'error'))
            .catch(() => settingsToast('Network error.', 'error'))
            .finally(() => { if (btn) { btn.disabled = false; btn.innerHTML = orig; } });
        });
    });
});
</script>

<?= $this->endSection() ?>
