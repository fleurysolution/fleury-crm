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

// Public Signup
$routes->get('signup', 'Signup::index');
$routes->get('signup/account/(:num)', 'Signup::account/$1');
$routes->get('signup/company', 'Signup::company');
$routes->post('signup/company', 'Signup::company');
$routes->get('signup/success', 'Signup::success');
$routes->post('signup/submit', 'Signup::submit');

// Stripe Webhooks
$routes->post('webhooks/stripe', 'Webhooks\Stripe::index');
// Platform Management (Super Admin)
$routes->get('tenants', 'Tenants::index', ['filter' => 'auth']);
$routes->group('subscriptions', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Subscriptions::index');
    $routes->get('create', 'Subscriptions::create');
    $routes->post('store', 'Subscriptions::store');
    $routes->get('current', 'Subscriptions::current');
    $routes->post('cancel', 'Subscriptions::cancel');
    $routes->get('upgrade', 'Subscriptions::upgrade');
    $routes->get('renew', 'Subscriptions::renew');
    $routes->get('checkout/(:num)', 'Subscriptions::checkout/$1');
    $routes->get('success', 'Subscriptions::success');
});

// Subscription Access Control
$routes->get('subscription/locked', 'Subscription::locked');
$routes->get('subscription/renew', 'Subscriptions::renew', ['filter' => 'auth']);

// Vendor Portal Routes
$routes->group('vendor-portal', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'VendorPortal::dashboard');
    $routes->get('pos', 'VendorPortal::pos');
    $routes->get('bids', 'VendorPortal::bids');
    $routes->get('tasks', 'VendorPortal::tasks');
});

// Vendor Onboarding (Public)
$routes->get('vendor/apply', 'VendorApply::index');
$routes->post('vendor/apply', 'VendorApply::submit');
$routes->get('vendor/apply/success', 'VendorApply::success');

$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

$routes->group('auth', function($routes) {
    $routes->get('signin',            'Auth::signin');
    $routes->post('signin',           'Auth::attemptSignin');
    $routes->post('signout',          'Auth::signout'); // Changed from get to post based on original

    // MFA Routes
    $routes->get('mfa-verify',        'Auth::mfaVerify');
    $routes->post('mfa-verify',       'Auth::doVerifyMfa');

    $routes->get('password/forgot',   'Auth::forgotPassword');
    $routes->post('password/forgot',  'Auth::sendResetLink');
    $routes->get('password/reset/(:any)', 'Auth::resetPassword/$1');
    $routes->post('password/reset',   'Auth::doResetPassword');
});

// $routes->get('approval/requests', 'Approval::index');
// $routes->get('approval/requests/(:num)', 'Approval::view/$1');
// $routes->post('approval/requests/(:num)/approve', 'Approval::approve/$1');
// $routes->post('approval/requests/(:num)/reject', 'Approval::reject/$1');
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

    // ── Cybersecurity ─────────────────────────────────────────────────
    $routes->get('security',                         'Settings::security');
    $routes->get('security_log_data',                'Settings::security_log_data');
    $routes->post('setup_mfa',                       'Settings::setup_mfa');
    $routes->post('verify_mfa_setup',                'Settings::verify_mfa_setup');
    $routes->post('disable_mfa',                     'Settings::disable_mfa');

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

    $routes->get('stripe-platform',                  'Settings::stripe_platform');
    $routes->post('save_platform_stripe',            'Settings::save_platform_stripe');

    $routes->group('custom-fields', function ($routes) {
        $routes->get('/', 'CustomFields::index');
        $routes->post('store', 'CustomFields::store');
        $routes->post('delete/(:num)', 'CustomFields::delete/$1');
    });

    $routes->group('automations', function ($routes) {
        $routes->get('/', 'Automations::index');
        $routes->post('store', 'Automations::store');
        $routes->get('toggle/(:num)', 'Automations::toggle/$1');
    });

    $routes->group('custom-hub', function ($routes) {
        $routes->get('/', 'CustomHub::index');
        $routes->post('store', 'CustomHub::store');
        $routes->post('delete/(:num)', 'CustomHub::delete/$1');
    });

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

// ── Custom Data Hub ──────────────────────────────────────────────────────────
$routes->get('hub/(:any)', 'CustomHub::viewData/$1', ['filter' => 'auth']);
$routes->post('hub/(:any)/save', 'CustomHub::saveData/$1', ['filter' => 'auth']);

// ── Workflow Approvals ──────────────────────────────────────────────────────
$routes->group('approvals', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Approvals::index');
    $routes->post('(:num)/action', 'Approvals::processAction/$1');
});

// ── Change Management ────────────────────────────────────────────────────────
$routes->group('change-orders', ['filter' => 'permission:view_production_control'], function ($routes) {
    $routes->post('events/store/(:num)', 'ChangeOrders::storeEvent/$1');
    $routes->post('convert/(:num)',      'ChangeOrders::convertToCO/$1');
    $routes->post('approve/(:num)',      'ChangeOrders::approveCO/$1');
});

// ── Meeting Minutes ──────────────────────────────────────────────────────────
$routes->group('meetings', ['filter' => 'auth'], function ($routes) {
    $routes->post('store/(:num)', 'Meetings::store/$1');
    $routes->post('update-minutes/(:num)', 'Meetings::updateMinutes/$1');
    $routes->post('update-attendee/(:num)', 'Meetings::updateAttendee/$1');
});

// ── Bidding Portal ───────────────────────────────────────────────────────────
$routes->group('bidding', ['filter' => 'auth'], function ($routes) {
    $routes->post('storePackage/(:num)', 'Bidding::storePackage/$1');
    $routes->post('submitBid/(:num)',    'Bidding::submitBid/$1');
    $routes->get('award/(:num)',         'Bidding::award/$1');
});

// ── Budget Line-Items ────────────────────────────────────────────────────────
$routes->group('budget-items', ['filter' => 'auth'], function ($routes) {
    $routes->post('store/(:num)', 'BudgetItems::store/$1');
    $routes->post('delete/(:num)', 'BudgetItems::delete/$1');
});

// ── Drawing Management ───────────────────────────────────────────────────────
$routes->group('drawings', ['filter' => 'auth'], function ($routes) {
    $routes->post('store/(:num)', 'Drawings::store/$1');
    $routes->get('view/(:num)',  'Drawings::view/$1');
    $routes->post('addPin/(:num)', 'Drawings::addPin/$1');
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
    $routes->get('(:num)/drivers',               'ProjectDrivers::index/$1');
    $routes->post('(:num)/drivers',              'ProjectDrivers::store/$1');
    $routes->post('(:num)/contracts',            'Contracts::store/$1');
    $routes->post('(:num)/setup/staffing',       'ProjectResources::storeStaffing/$1');
    $routes->post('(:num)/setup/equipment',      'ProjectResources::storeEquipment/$1');
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
$routes->post('drivers/(:num)/update',           'ProjectDrivers::update/$1', ['filter' => 'auth']);
$routes->post('drivers/(:num)/delete',           'ProjectDrivers::delete/$1', ['filter' => 'auth']);
$routes->post('setup/staffing/(:num)/update',    'ProjectResources::updateStaffing/$1', ['filter' => 'auth']);
$routes->post('setup/staffing/(:num)/delete',    'ProjectResources::deleteStaffing/$1', ['filter' => 'auth']);
$routes->post('setup/equipment/(:num)/update',   'ProjectResources::updateEquipment/$1', ['filter' => 'auth']);
$routes->post('setup/equipment/(:num)/delete',   'ProjectResources::deleteEquipment/$1', ['filter' => 'auth']);

// Contracts — global AJAX & detail endpoints
$routes->get('contracts/(:num)',                 'Contracts::show/$1',    ['filter' => 'auth']);
$routes->post('contracts/(:num)/status',         'Contracts::updateStatus/$1', ['filter' => 'auth']);
$routes->post('contracts/(:num)/amend',          'Contracts::amend/$1',   ['filter' => 'auth']);
$routes->post('contracts/amendments/(:num)/approve', 'Contracts::approveAmendment/$1', ['filter' => 'auth']);
$routes->post('contracts/amendments/(:num)/reject',  'Contracts::rejectAmendment/$1',  ['filter' => 'auth']);
$routes->post('contracts/(:num)/sign',           'Contracts::signContract/$1', ['filter' => 'auth']);
$routes->get('contracts/(:num)/pdf',             'Contracts::downloadPdf/$1',  ['filter' => 'auth']);
$routes->post('contracts/(:num)/delete',         'Contracts::delete/$1',  ['filter' => 'auth']);

// Milestones — global AJAX endpoints
$routes->post('milestones/(:num)/update',        'Projects::updateMilestone/$1', ['filter' => 'auth']);

// ── Gantt ───────────────────────────────────────────────────────────────────
$routes->group('gantt', ['filter' => 'permission:manage_p6_scheduler'], function ($routes) {
    $routes->get('projects/(:num)/data',       'Gantt::data/$1');
    $routes->post('projects/(:num)/import',     'Gantt::importXer/$1');
    $routes->post('projects/(:num)/recalculate','Gantt::recalculate/$1');
    $routes->post('tasks/(:num)/update',       'Gantt::updateDates/$1');
});

// ── Procurement & Precon ────────────────────────────────────────────────────
$routes->group('procurement', ['filter' => 'permission:manage_preconstruction'], function ($routes) {
    $routes->get('projects/(:num)',       'Procurement::index/$1');
    $routes->post('projects/(:num)/save', 'Procurement::saveItem/$1');
    $routes->get('projects/(:num)/bid-leveling',      'Procurement::bidLeveling/$1');
});

// ── Handover & Quality ──────────────────────────────────────────────────────
$routes->group('handover', ['filter' => 'permission:manage_handover_qc'], function ($routes) {
    $routes->post('projects/(:num)/save',    'Handover::saveAsset/$1');
    $routes->get('projects/(:num)/print-qr', 'Handover::printQr/$1');
});
$routes->post('tasks/(:num)/qa-toggle',           'Tasks::qaToggle/$1',        ['filter' => 'auth']);

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
    $routes->post('validate-location',  'Timesheets::validateLocation');
});

// ── RFIs ─────────────────────────────────────────────────────────────────────
$routes->get('rfis',                            'RFIs::globalIndex',      ['filter' => 'auth']);
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
$routes->get('project-estimates/new',                   'ProjectEstimates::create',   ['filter' => 'auth']);
$routes->get('project-estimates',                       'ProjectEstimates::index',    ['filter' => 'auth']);
$routes->post('projects/(:num)/estimates',              'ProjectEstimates::store/$1', ['filter' => 'auth']);
$routes->get('project-estimates/(:num)',                'ProjectEstimates::show/$1',  ['filter' => 'auth']);
$routes->post('project-estimates/(:num)/items',         'ProjectEstimates::addItem/$1', ['filter' => 'auth']);
$routes->post('project-estimates/(:num)/items/(:num)/delete', 'ProjectEstimates::deleteItem/$1/$2', ['filter' => 'auth']);
$routes->post('project-estimates/(:num)/delete',        'ProjectEstimates::delete/$1', ['filter' => 'auth']);

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

// ── Daily Construction Logs ──────────────────────────────────────────────────
$routes->get('projects/(:num)/daily-logs',                'DailyLogs::index/$1',        ['filter' => 'auth']);
$routes->post('projects/(:num)/daily-logs',               'DailyLogs::store/$1',        ['filter' => 'auth']);
$routes->get('daily-logs/(:num)',                         'DailyLogs::show/$1',         ['filter' => 'auth']);
$routes->post('daily-logs/(:num)/manpower',               'DailyLogs::addManpower/$1',  ['filter' => 'auth']);
$routes->post('daily-logs/(:num)/equipment',              'DailyLogs::addEquipment/$1', ['filter' => 'auth']);

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
$routes->post('finance/expenses/(:num)/delete',     'Finance::deleteExpense/$1',   ['filter' => 'auth']);

// ── Field Management (Punch & Diaries) ───────────────────────────────────────
$routes->post('projects/(:num)/punch',                 'FieldManagement::storePunchList/$1',   ['filter' => 'auth']);
$routes->post('punch/(:num)/status',                   'FieldManagement::updatePunchStatus/$1',['filter' => 'auth']);
$routes->post('punch/(:num)/delete',                   'FieldManagement::deletePunchList/$1',  ['filter' => 'auth']);

$routes->post('projects/(:num)/diaries',               'FieldManagement::createDiary/$1',      ['filter' => 'auth']);
$routes->get('field/diary/(:num)',                     'FieldManagement::showDiary/$1',        ['filter' => 'auth']);
$routes->post('field/diary/(:num)/save',               'FieldManagement::saveDiaryItems/$1',   ['filter' => 'auth']);

// ── QHSE (Inspections & Safety) ──────────────────────────────────────────────
$routes->get('projects/(:num)/inspections',           'Inspections::index/$1',       ['filter' => 'auth']);
$routes->get('projects/(:num)/inspections/(:num)',    'Inspections::show/$1/$2',     ['filter' => 'auth']);
$routes->post('projects/(:num)/inspections',          'Inspections::store/$1',       ['filter' => 'auth']);
$routes->post('projects/(:num)/inspections/(:num)/delete', 'Inspections::delete/$1/$2', ['filter' => 'auth']);

$routes->get('projects/(:num)/safety',                 'SafetyIncidents::index/$1',   ['filter' => 'auth']);
$routes->post('projects/(:num)/safety',                'SafetyIncidents::store/$1',   ['filter' => 'auth']);
$routes->post('projects/(:num)/safety/(:num)/status',  'SafetyIncidents::updateStatus/$1/$2', ['filter' => 'auth']);
$routes->post('projects/(:num)/safety/(:num)/delete',  'SafetyIncidents::delete/$1/$2', ['filter' => 'auth']);

// ── Procurement & Purchase Orders ────────────────────────────────────────────
$routes->post('projects/(:num)/procurement/pos',       'Procurement::createPo/$1',     ['filter' => 'auth']);
$routes->get('procurement/pos/(:num)',                 'Procurement::showPo/$1',       ['filter' => 'auth']);
$routes->post('procurement/pos/(:num)/items',          'Procurement::savePoItems/$1',  ['filter' => 'auth']);
$routes->get('procurement/pos/(:num)/pdf',             'Procurement::exportPoPdf/$1',  ['filter' => 'auth']);

// ── Inventory & Asset Management ─────────────────────────────────────────────
$routes->get('assets',                         'Assets::index',               ['filter' => 'auth']);
$routes->post('assets',                        'Assets::store',               ['filter' => 'auth']);
$routes->post('assets/(:num)/assign',          'Assets::assign/$1',           ['filter' => 'auth']);
$routes->post('assets/(:num)/maintenance',     'Assets::maintenance/$1',      ['filter' => 'auth']);

$routes->get('inventory',                      'Inventory::index',            ['filter' => 'auth']);
$routes->post('inventory/items',               'Inventory::storeItem',        ['filter' => 'auth']);
$routes->post('inventory/locations',           'Inventory::storeLocation',    ['filter' => 'auth']);
$routes->post('inventory/transactions',        'Inventory::processTransaction',['filter' => 'auth']);

// ── Workforce & Payroll ──────────────────────────────────────────────────────
$routes->get('payroll',                        'Payroll::index',              ['filter' => 'auth']);
$routes->post('payroll/profiles',              'Payroll::storeProfile',       ['filter' => 'auth']);
$routes->post('payroll/tax-profiles',          'Payroll::storeTaxProfile',    ['filter' => 'auth']);
$routes->post('payroll/runs/generate',         'Payroll::generateRun',        ['filter' => 'auth']);
$routes->post('payroll/runs/(:num)/approve',   'Payroll::approveRun/$1',      ['filter' => 'auth']);

// ── Reports & Dashboard ──────────────────────────────────────────────────────
$routes->get('reports',                               'Reports::index',              ['filter' => 'auth']);
$routes->get('reports/financial/pnl',                 'FinancialReports::pnl',       ['filter' => 'auth']);
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

// ── Branch & Department Management ─────────────────────────────────────────
$routes->group('branches', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Branches::index');
    $routes->post('create', 'Branches::create');
    $routes->get('delete/(:num)', 'Branches::delete/$1');
});

$routes->group('departments', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Departments::index');
    $routes->post('create', 'Departments::create');
    $routes->get('delete/(:num)', 'Departments::delete/$1');
});

// ── Vendor Applications (Internal) ─────────────────────────────────────────
$routes->get('vendor-applications',                 'VendorApplications::index',    ['filter' => 'auth']);
$routes->get('vendor-applications/(:num)',          'VendorApplications::show/$1',  ['filter' => 'auth']);
$routes->post('vendor-applications/(:num)/approve', 'VendorApplications::approve/$1',['filter' => 'auth']);
$routes->post('vendor-applications/(:num)/reject',  'VendorApplications::reject/$1', ['filter' => 'auth']);

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

// Phase 13: Project Progress Reports
$routes->post('projects/(:num)/upload-photos',      'Projects::uploadProgressPhotos/$1', ['filter' => 'auth']);
$routes->delete('projects/(:num)/delete-photo/(:num)', 'Projects::deleteProgressPhoto/$1/$2', ['filter' => 'auth']);
$routes->get('projects/(:num)/progress-report',    'Projects::generateProgressReport/$1', ['filter' => 'auth']);

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
    
    // Advanced builder features
    $routes->post('(:num)/items', 'Estimates::addItem/$1');
    $routes->post('(:num)/items/(:num)/delete', 'Estimates::deleteItem/$1/$2');
    $routes->get('(:num)/clone', 'Estimates::clone/$1');
    $routes->post('(:num)/status', 'Estimates::status/$1');
    $routes->get('(:num)/send', 'Estimates::send/$1');
    $routes->get('(:num)/pdf', 'Estimates::pdf/$1');
});

// Invoices Management
$routes->group('invoices', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Invoices::index');
    $routes->get('(:num)', 'Invoices::show/$1');
    $routes->post('(:num)/payment', 'Invoices::add_payment/$1');
});

$routes->get('about/(:segment)', 'About::index/$1');
