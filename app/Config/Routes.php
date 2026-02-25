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

// Leads Management
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
