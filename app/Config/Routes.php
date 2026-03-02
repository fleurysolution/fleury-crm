<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

//$routes->get('/', 'Dashboard::index');
$routes->get('/', 'Landing::index');
// Auth
$routes->get('auth/signin', 'Auth::signin');
$routes->post('auth/signin', 'Auth::attemptSignin');
$routes->post('auth/signout', 'Auth::signout');

$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

$routes->get('auth/password/forgot', 'Auth::forgotPassword');
$routes->post('auth/password/forgot', 'Auth::sendResetLink');
$routes->get('auth/password/reset/(:segment)', 'Auth::resetPassword/$1');
$routes->post('auth/password/reset', 'Auth::doResetPassword');

$routes->get('approval/requests', 'Approval::index');
$routes->get('approval/requests/(:num)', 'Approval::view/$1');
$routes->post('approval/requests/(:num)/approve', 'Approval::approve/$1');
$routes->post('approval/requests/(:num)/reject', 'Approval::reject/$1');
$routes->get('locale/(:segment)', 'Locale::set/$1');

// Team Management
$routes->group('team', ['filter' => 'permission:manage_team'], function ($routes) {
    $routes->get('/', 'Team::index');
    $routes->get('create', 'Team::create');
    $routes->post('store', 'Team::store');
});

// Role Management
$routes->group('roles', ['filter' => 'permission:manage_roles'], function ($routes) {
    $routes->get('/', 'Roles::index');
    $routes->get('create', 'Roles::create');
    $routes->post('store', 'Roles::store');
});

// Settings
$routes->group('settings', ['filter' => 'permission:manage_settings'], function ($routes) {
    $routes->get('/', 'Settings::index');

    // ── Core ──────────────────────────────────────────────────────────
    $routes->get('general',                          'Settings::general');
    $routes->post('save_general_settings',           'Settings::save_general_settings');

    $routes->get('email',                            'Settings::email');
    $routes->post('save_email_settings',             'Settings::save_email_settings');

    $routes->get('localization',                     'Settings::localization');
    $routes->post('save_localization_settings',      'Settings::save_localization_settings');

    $routes->get('modules',                          'Settings::modules');
    $routes->post('save_module_settings',            'Settings::save_module_settings');

    $routes->get('notifications',                    'Settings::notifications');
    $routes->post('notification_settings_list_data', 'Settings::notification_settings_list_data');
    $routes->post('notification_modal_form',         'Settings::notification_modal_form');
    $routes->post('save_notification_settings',      'Settings::save_notification_settings');

    // ── Security & Access ─────────────────────────────────────────────
    $routes->get('ip_restriction',                   'Settings::ip_restriction');
    $routes->post('save_ip_settings',                'Settings::save_ip_settings');

    $routes->get('client_permissions',               'Settings::client_permissions');
    $routes->post('save_client_settings',            'Settings::save_client_settings');

    $routes->get('gdpr',                             'Settings::gdpr');
    $routes->post('save_gdpr_settings',              'Settings::save_gdpr_settings');

    $routes->get('rbac',                             'Settings::rbac');
    $routes->post('save_role',                       'Settings::save_role');
    $routes->post('delete_role/(:num)',              'Settings::delete_role/$1');

    $routes->get('approval_workflows',               'Settings::approval_workflows');
    $routes->post('save_workflow',                   'Settings::save_workflow');
    $routes->post('delete_workflow/(:num)',           'Settings::delete_workflow/$1');

    // ── Business ──────────────────────────────────────────────────────
    $routes->get('invoices',                         'Settings::invoices');
    $routes->post('save_invoice_settings',           'Settings::save_invoice_settings');

    $routes->get('estimates',                        'Settings::estimates');
    $routes->post('save_estimate_settings',          'Settings::save_estimate_settings');

    $routes->get('contracts',                        'Settings::contracts');
    $routes->post('save_contract_settings',          'Settings::save_contract_settings');

    $routes->get('proposals',                        'Settings::proposals');
    $routes->post('save_proposal_settings',          'Settings::save_proposal_settings');

    $routes->get('orders',                           'Settings::orders');
    $routes->post('save_order_settings',             'Settings::save_order_settings');

    $routes->get('subscriptions',                    'Settings::subscriptions');
    $routes->post('save_subscription_settings',      'Settings::save_subscription_settings');

    $routes->get('store',                            'Settings::store');
    $routes->post('save_store_settings',             'Settings::save_store_settings');

    // ── Projects & Tasks ─────────────────────────────────────────────
    $routes->get('projects',                         'Settings::projects');
    $routes->post('save_projects_settings',          'Settings::save_projects_settings');

    $routes->get('tasks',                            'Settings::tasks');
    $routes->post('save_task_settings',              'Settings::save_task_settings');

    $routes->get('events',                           'Settings::events');
    $routes->post('save_event_settings',             'Settings::save_event_settings');

    // ── Support ───────────────────────────────────────────────────────
    $routes->get('tickets',                          'Settings::tickets');
    $routes->post('save_ticket_settings',            'Settings::save_ticket_settings');
    $routes->get('imap_settings',                   'Settings::imap_settings');
    $routes->post('save_imap_settings',              'Settings::save_imap_settings');

    $routes->get('leads',                            'Settings::leads');
    $routes->post('save_lead_settings',              'Settings::save_lead_settings');

    // ── UI & Integrations ─────────────────────────────────────────────
    $routes->get('footer',                           'Settings::footer');
    $routes->post('save_footer_settings',            'Settings::save_footer_settings');

    $routes->get('top_menu',                         'Settings::top_menu');
    $routes->post('save_top_menu_settings',          'Settings::save_top_menu_settings');

    $routes->get('pwa',                              'Settings::pwa');
    $routes->post('save_pwa_settings',               'Settings::save_pwa_settings');

    $routes->get('integration',                      'Settings::integration');
    $routes->get('re_captcha',                       'Settings::re_captcha');
    $routes->post('save_re_captcha_settings',        'Settings::save_re_captcha_settings');
    $routes->get('push_notification',                'Settings::push_notification');
    $routes->post('save_push_notification_settings', 'Settings::save_push_notification_settings');
    $routes->get('slack',                            'Settings::slack');
    $routes->post('save_slack_settings',             'Settings::save_slack_settings');
    $routes->get('google_drive',                     'Settings::google_drive');
    $routes->post('save_google_drive_settings',      'Settings::save_google_drive_settings');
    $routes->get('github',                           'Settings::github');
    $routes->post('save_github_settings',            'Settings::save_github_settings');
    $routes->get('bitbucket',                        'Settings::bitbucket');
    $routes->post('save_bitbucket_settings',         'Settings::save_bitbucket_settings');
    $routes->get('tinymce',                          'Settings::tinymce');
    $routes->post('save_tinymce_settings',           'Settings::save_tinymce_settings');

    $routes->get('cron_job',                         'Settings::cron_job');
    $routes->get('db_backup',                        'Settings::db_backup');

    // Legacy generic save
    $routes->post('save',                            'Settings::save');

    // ── Branch Structure ──────────────────────────────────────────────
    $routes->get('branches/regions',                        'BranchSettings::regions');
    $routes->get('branches/offices',                        'BranchSettings::offices');
    $routes->get('branches/divisions',                      'BranchSettings::divisions');
    $routes->post('branches/regions/save',                  'BranchSettings::saveRegion');
    $routes->post('branches/regions/delete/(:num)',         'BranchSettings::deleteRegion/$1');
    $routes->get('branches/regions/get/(:num)',             'BranchSettings::getRegion/$1');
    $routes->post('branches/offices/save',                  'BranchSettings::saveOffice');
    $routes->post('branches/offices/delete/(:num)',         'BranchSettings::deleteOffice/$1');
    $routes->get('branches/offices/get/(:num)',             'BranchSettings::getOffice/$1');
    $routes->get('branches/offices/by-region/(:num)',       'BranchSettings::officesByRegion/$1');
    $routes->post('branches/divisions/save',                'BranchSettings::saveDivision');
    $routes->post('branches/divisions/delete/(:num)',       'BranchSettings::deleteDivision/$1');
    $routes->get('branches/divisions/get/(:num)',           'BranchSettings::getDivision/$1');
    $routes->get('branches/divisions/by-office/(:num)',     'BranchSettings::divisionsByOffice/$1');
});

// ── Projects, Tasks, Areas ─────────────────────────────────────────────────
$routes->group('projects', ['filter' => 'auth'], function ($routes) {
    $routes->get('/',                            'Projects::index');
    $routes->get('create',                       'Projects::create');
    $routes->post('store',                       'Projects::store');
    $routes->get('(:num)',                       'Projects::show/$1');
    $routes->get('(:num)/edit',                  'Projects::edit/$1');
    $routes->post('(:num)/update',               'Projects::update/$1');
    $routes->post('(:num)/archive',              'Projects::archive/$1');

    // Per-project sub-resources
    $routes->post('(:num)/tasks',                'Tasks::store/$1');
    $routes->get('(:num)/tasks',                 'Tasks::index/$1');
    $routes->get('(:num)/kanban',                'Tasks::kanban/$1');
    $routes->get('(:num)/members',               'Projects::members/$1');
    $routes->post('(:num)/members',              'Projects::addMember/$1');
    $routes->get('(:num)/areas',                 'Areas::index/$1');
    $routes->post('(:num)/areas',                'Areas::store/$1');
    $routes->post('(:num)/phases',               'Projects::storePhase/$1');
    $routes->post('(:num)/milestones',           'Projects::storeMilestone/$1');
});

// Tasks — global AJAX endpoints
$routes->get('tasks/(:num)',                     'Tasks::show/$1',    ['filter' => 'auth']);
$routes->post('tasks/(:num)/update',             'Tasks::update/$1',  ['filter' => 'auth']);
$routes->post('tasks/(:num)/move',               'Tasks::move/$1',    ['filter' => 'auth']);
$routes->post('tasks/(:num)/comment',            'Tasks::comment/$1', ['filter' => 'auth']);
$routes->post('tasks/(:num)/upload',             'Tasks::upload/$1',  ['filter' => 'auth']);
$routes->post('tasks/(:num)/checklist',          'Tasks::checklist/$1', ['filter' => 'auth']);
$routes->post('tasks/(:num)/delete',             'Tasks::delete/$1',  ['filter' => 'auth']);

// Areas — global AJAX endpoints
$routes->post('areas/(:num)/update',             'Areas::update/$1',  ['filter' => 'auth']);
$routes->post('areas/(:num)/delete',             'Areas::delete/$1',  ['filter' => 'auth']);

// Milestones — global AJAX endpoints
$routes->post('milestones/(:num)/update',        'Projects::updateMilestone/$1', ['filter' => 'auth']);

// ── Gantt ───────────────────────────────────────────────────────────────────
$routes->get('projects/(:num)/gantt/data',       'Gantt::data/$1',        ['filter' => 'auth']);
$routes->post('tasks/(:num)/gantt-update',        'Gantt::updateDates/$1', ['filter' => 'auth']);

// ── Timesheets ──────────────────────────────────────────────────────────────
$routes->group('timesheets', ['filter' => 'auth'], function ($routes) {
    $routes->get('/',                   'Timesheets::index');
    $routes->get('all',                 'Timesheets::all');
    $routes->get('create',              'Timesheets::create');
    $routes->post('store',              'Timesheets::store');
    $routes->get('(:num)',              'Timesheets::show/$1');
    $routes->post('(:num)/save',        'Timesheets::save/$1');
    $routes->post('(:num)/submit',      'Timesheets::submit/$1');
    $routes->post('(:num)/approve',     'Timesheets::approve/$1');
    $routes->post('(:num)/reject',      'Timesheets::reject/$1');
    $routes->get('(:num)/export',       'Timesheets::export/$1');
});

// ── RFIs ─────────────────────────────────────────────────────────────────────
$routes->post('projects/(:num)/rfis',          'RFIs::store/$1',         ['filter' => 'auth']);
$routes->get('rfis/(:num)',                     'RFIs::show/$1',          ['filter' => 'auth']);
$routes->post('rfis/(:num)/respond',            'RFIs::respond/$1',       ['filter' => 'auth']);
$routes->post('rfis/(:num)/status',             'RFIs::updateStatus/$1',  ['filter' => 'auth']);
$routes->post('rfis/(:num)/delete',             'RFIs::delete/$1',        ['filter' => 'auth']);
$routes->get('rfis/(:num)/export',              'RFIs::export/$1',        ['filter' => 'auth']);
$routes->get('projects/(:num)/rfis',            'RFIs::index/$1',         ['filter' => 'auth']);

// ── Submittals ────────────────────────────────────────────────────────────────
$routes->post('projects/(:num)/submittals',     'Submittals::store/$1',   ['filter' => 'auth']);
$routes->get('submittals/(:num)',               'Submittals::show/$1',    ['filter' => 'auth']);
$routes->post('submittals/(:num)/review',       'Submittals::review/$1',  ['filter' => 'auth']);
$routes->post('submittals/(:num)/delete',       'Submittals::delete/$1',  ['filter' => 'auth']);
$routes->get('projects/(:num)/submittals',      'Submittals::index/$1',   ['filter' => 'auth']);

// ── Drawings ──────────────────────────────────────────────────────────────────
$routes->post('projects/(:num)/drawings',       'Drawings::store/$1',         ['filter' => 'auth']);
$routes->get('drawings/(:num)',                 'Drawings::show/$1',          ['filter' => 'auth']);
$routes->post('drawings/(:num)/revisions',      'Drawings::uploadRevision/$1',['filter' => 'auth']);
$routes->post('drawings/(:num)/delete',         'Drawings::delete/$1',        ['filter' => 'auth']);

// ── Estimates & Bids ──────────────────────────────────────────────────────────
$routes->post('projects/(:num)/estimates',      'Estimates::store/$1',        ['filter' => 'auth']);
$routes->get('estimates/(:num)',                'Estimates::show/$1',         ['filter' => 'auth']);
$routes->post('estimates/(:num)/items',         'Estimates::addItem/$1',      ['filter' => 'auth']);
$routes->post('estimates/(:num)/items/(:num)/delete', 'Estimates::deleteItem/$1/$2', ['filter' => 'auth']);
$routes->post('estimates/(:num)/delete',        'Estimates::delete/$1',       ['filter' => 'auth']);

$routes->post('projects/(:num)/bids',           'Bids::store/$1',             ['filter' => 'auth']);
$routes->post('bids/(:num)/status',             'Bids::updateStatus/$1',      ['filter' => 'auth']);
$routes->post('bids/(:num)/delete',             'Bids::delete/$1',            ['filter' => 'auth']);

// ── Punch List ────────────────────────────────────────────────────────────────
$routes->post('projects/(:num)/punch-list',               'PunchList::store/$1',        ['filter' => 'auth']);
$routes->get('projects/(:num)/punch-list',                'PunchList::index/$1',        ['filter' => 'auth']);
$routes->get('projects/(:num)/punch-list/export',         'PunchList::exportCsv/$1',    ['filter' => 'auth']);
$routes->post('punch-list/(:num)/status',                 'PunchList::updateStatus/$1', ['filter' => 'auth']);
$routes->post('punch-list/(:num)/resolve',                'PunchList::resolve/$1',      ['filter' => 'auth']);
$routes->post('punch-list/(:num)/close',                  'PunchList::close/$1',        ['filter' => 'auth']);
$routes->post('punch-list/(:num)/delete',                 'PunchList::delete/$1',       ['filter' => 'auth']);

// ── Site Diary ────────────────────────────────────────────────────────────────
$routes->get('projects/(:num)/site-diary',                'SiteDiary::index/$1',        ['filter' => 'auth']);
$routes->get('projects/(:num)/site-diary/create',         'SiteDiary::create/$1',       ['filter' => 'auth']);
$routes->post('projects/(:num)/site-diary',               'SiteDiary::store/$1',        ['filter' => 'auth']);
$routes->get('projects/(:num)/site-diary/(:num)',          'SiteDiary::show/$1/$2',      ['filter' => 'auth']);
    $routes->post('projects/(:num)/site-diary/(:num)/update',  'SiteDiary::update/$1/$2',    ['filter' => 'auth']);
    $routes->post('projects/(:num)/site-diary/(:num)/submit',  'SiteDiary::submit/$1/$2',    ['filter' => 'auth']);
    $routes->post('projects/(:num)/site-diary/(:num)/approve', 'SiteDiary::approve/$1/$2',   ['filter' => 'auth']);

// ── Contracts ─────────────────────────────────────────────────────────────────
$routes->post('projects/(:num)/contracts',      'Contracts::store/$1',        ['filter' => 'auth']);
$routes->get('projects/(:num)/contracts',       'Contracts::index/$1',        ['filter' => 'auth']);
$routes->post('contracts/(:num)/status',        'Contracts::updateStatus/$1', ['filter' => 'auth']);
$routes->post('contracts/(:num)/amend',         'Contracts::amend/$1',        ['filter' => 'auth']);
$routes->post('contracts/amendments/(:num)/approve', 'Contracts::approveAmendment/$1', ['filter' => 'auth']);
$routes->post('contracts/(:num)/delete',        'Contracts::delete/$1',       ['filter' => 'auth']);
$routes->post('contracts/(:num)/sign',          'Contracts::signContract/$1', ['filter' => 'auth']);
$routes->get('contracts/(:num)/pdf',            'Contracts::downloadPdf/$1',  ['filter' => 'auth']);
$routes->get('contracts/(:num)',                'Contracts::show/$1',         ['filter' => 'auth']);

// ── BOQ ──────────────────────────────────────────────────────────────────────
$routes->get('projects/(:num)/boq',                    'BOQ::index/$1',      ['filter' => 'auth']);
$routes->post('projects/(:num)/boq',                   'BOQ::save/$1',       ['filter' => 'auth']);
$routes->get('projects/(:num)/boq/export',             'BOQ::exportCsv/$1',  ['filter' => 'auth']);
$routes->post('boq/(:num)/delete',                     'BOQ::delete/$1',     ['filter' => 'auth']);

// ── Finance (SOV & Invoicing) ────────────────────────────────────────────────
$routes->get('projects/(:num)/finance',                'Finance::index/$1',           ['filter' => 'auth']);
$routes->post('projects/(:num)/sov',                   'Finance::storeSovItem/$1',    ['filter' => 'auth']);
$routes->post('projects/(:num)/sov/(:num)/delete',     'Finance::deleteSovItem/$1/$2',['filter' => 'auth']);
$routes->post('projects/(:num)/pay-apps',              'Finance::createPayApp/$1',    ['filter' => 'auth']);
$routes->get('finance/pay-apps/(:num)',                'Finance::showPayApp/$1',      ['filter' => 'auth']);
$routes->get('finance/pay-apps/(:num)/pdf',            'Finance::exportPayAppPdf/$1', ['filter' => 'auth']);
$routes->post('finance/pay-apps/(:num)/items',         'Finance::savePayAppItems/$1', ['filter' => 'auth']);
$routes->get('projects/(:num)/finance/export',         'Finance::exportCsv/$1',       ['filter' => 'auth']);
$routes->post('projects/(:num)/finance/certs',         'Finance::storeCert/$1',       ['filter' => 'auth']);
$routes->post('finance/certs/(:num)/approve',          'Finance::approveCert/$1',     ['filter' => 'auth']);
$routes->post('finance/certs/(:num)/mark-paid',        'Finance::markCertPaid/$1',    ['filter' => 'auth']);
$routes->post('projects/(:num)/finance/invoices',      'Finance::storeInvoice/$1',    ['filter' => 'auth']);
$routes->post('finance/invoices/(:num)/mark-paid',     'Finance::markInvoicePaid/$1', ['filter' => 'auth']);
$routes->post('projects/(:num)/finance/expenses',      'Finance::storeExpense/$1',    ['filter' => 'auth']);
$routes->post('finance/expenses/(:num)/approve',       'Finance::approveExpense/$1',  ['filter' => 'auth']);

// ── Field Management (Punch & Diaries) ───────────────────────────────────────
$routes->post('projects/(:num)/punch',                 'FieldManagement::storePunchList/$1',   ['filter' => 'auth']);
$routes->post('punch/(:num)/status',                   'FieldManagement::updatePunchStatus/$1',['filter' => 'auth']);
$routes->post('punch/(:num)/delete',                   'FieldManagement::deletePunchList/$1',  ['filter' => 'auth']);

$routes->post('projects/(:num)/diaries',               'FieldManagement::createDiary/$1',      ['filter' => 'auth']);
$routes->get('field/diary/(:num)',                     'FieldManagement::showDiary/$1',        ['filter' => 'auth']);
$routes->post('field/diary/(:num)/save',               'FieldManagement::saveDiaryItems/$1',   ['filter' => 'auth']);

// ── Procurement & Purchase Orders ────────────────────────────────────────────
$routes->post('projects/(:num)/procurement/pos',       'Procurement::createPo/$1',     ['filter' => 'auth']);
$routes->get('procurement/pos/(:num)',                 'Procurement::showPo/$1',       ['filter' => 'auth']);
$routes->post('procurement/pos/(:num)/items',          'Procurement::savePoItems/$1',  ['filter' => 'auth']);
$routes->get('procurement/pos/(:num)/pdf',             'Procurement::exportPoPdf/$1',  ['filter' => 'auth']);

// ── Reports & Dashboard ──────────────────────────────────────────────────────
$routes->get('reports',                               'Reports::index',              ['filter' => 'auth']);
$routes->get('reports/json',                          'Reports::executiveJson',      ['filter' => 'auth']);
$routes->get('projects/(:num)/report',                'Reports::project/$1',         ['filter' => 'auth']);
$routes->get('projects/(:num)/report/json',           'Reports::projectJson/$1',     ['filter' => 'auth']);
$routes->get('projects/(:num)/report/print',          'Reports::exportPrint/$1',     ['filter' => 'auth']);

// ── Notifications ────────────────────────────────────────────────────────────
$routes->get('notifications',               'Notifications::index',    ['filter' => 'auth']);
$routes->get('notifications/dropdown',      'Notifications::dropdown', ['filter' => 'auth']);
$routes->get('notifications/count',         'Notifications::count',    ['filter' => 'auth']);
$routes->post('notifications/read-all',     'Notifications::readAll',  ['filter' => 'auth']);
$routes->post('notifications/(:num)/read',  'Notifications::markRead/$1', ['filter' => 'auth']);

// ── Activity Log ───────────────────────────────────────────────────────────
$routes->get('activity',                    'ActivityLog::index',      ['filter' => 'auth']);
$routes->get('projects/(:num)/activity',    'ActivityLog::forProject/$1', ['filter' => 'auth']);

// ── Users Management ───────────────────────────────────────────────────────
$routes->get('users',                         'Users::index',           ['filter' => 'auth']);
$routes->get('users/create',                  'Users::create',          ['filter' => 'auth']);
$routes->post('users/store',                  'Users::store',           ['filter' => 'auth']);
$routes->get('users/(:num)',                  'Users::show/$1',         ['filter' => 'auth']);
$routes->post('users/(:num)/update',          'Users::update/$1',       ['filter' => 'auth']);
$routes->post('users/(:num)/password',        'Users::changePassword/$1', ['filter' => 'auth']);
$routes->post('users/(:num)/toggle-status',   'Users::toggleStatus/$1', ['filter' => 'auth']);
$routes->post('users/(:num)/delete',          'Users::delete/$1',       ['filter' => 'auth']);

// ── My Profile ──────────────────────────────────────────────────────────────
$routes->get('profile',                       'Users::profile',         ['filter' => 'auth']);
$routes->post('profile/update',               'Users::updateProfile',   ['filter' => 'auth']);
$routes->post('profile/password',             'Users::changeOwnPassword', ['filter' => 'auth']);

// ── Construction Settings ───────────────────────────────────────────────────
$routes->get('settings/construction',         'Settings::construction', ['filter' => 'auth']);
$routes->post('settings/construction/save',   'Settings::saveConstruction', ['filter' => 'auth']);

// ── Calendar & Scheduling ──────────────────────────────────────────────────
$routes->get('calendar',                            'Calendar::index',              ['filter' => 'auth']);
$routes->get('calendar/events',                     'Calendar::events',             ['filter' => 'auth']);
$routes->post('calendar/events',                    'Calendar::store',              ['filter' => 'auth']);
$routes->post('calendar/events/(:num)/update',      'Calendar::update/$1',          ['filter' => 'auth']);
$routes->post('calendar/events/(:num)/delete',      'Calendar::delete/$1',          ['filter' => 'auth']);
$routes->post('calendar/events/(:num)/drag',        'Calendar::drag/$1',            ['filter' => 'auth']);

// ── File Manager ───────────────────────────────────────────────────────────
$routes->get('files',                               'FileManager::index',           ['filter' => 'auth']);
$routes->get('files/(:num)/download',               'FileManager::download/$1',     ['filter' => 'auth']);
$routes->post('files/(:num)/delete',                'FileManager::delete/$1',       ['filter' => 'auth']);
$routes->post('files/(:num)/update',                'FileManager::update/$1',       ['filter' => 'auth']);
$routes->get('projects/(:num)/files',               'FileManager::forProject/$1',   ['filter' => 'auth']);
$routes->post('projects/(:num)/files/upload',       'FileManager::upload/$1',       ['filter' => 'auth']);

// ── Comments ────────────────────────────────────────────────────────────────
$routes->get('comments',                            'Comments::index',              ['filter' => 'auth']);
$routes->post('comments',                           'Comments::store',              ['filter' => 'auth']);
$routes->post('comments/(:num)/delete',             'Comments::delete/$1',          ['filter' => 'auth']);
$routes->post('comments/(:num)/update',             'Comments::update/$1',          ['filter' => 'auth']);

$routes->group('leads', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Leads::index');
    $routes->get('kanban', 'Leads::kanban');
    $routes->get('create', 'Leads::create');
    $routes->post('store', 'Leads::store');
    $routes->get('convert/(:num)', 'Leads::convert/$1');
    $routes->get('(:num)', 'Leads::show/$1');
    $routes->get('edit/(:num)', 'Leads::edit/$1'); // Stub for now
});

// Clients Management
$routes->group('clients', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Clients::index');
    $routes->get('(:num)', 'Clients::show/$1');
});

// Estimates Management
$routes->group('estimates', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Estimates::index');
    $routes->get('new', 'Estimates::create');
    $routes->post('/', 'Estimates::store');
    $routes->get('(:num)', 'Estimates::show/$1');
    $routes->get('edit/(:num)', 'Estimates::edit/$1');
    $routes->get('convert/(:num)', 'Estimates::convert_to_invoice/$1');
});

// Invoices Management
$routes->group('invoices', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Invoices::index');
    $routes->get('(:num)', 'Invoices::show/$1');
    $routes->post('(:num)/payment', 'Invoices::add_payment/$1');
});

$routes->get('about/(:segment)', 'About::index/$1');
