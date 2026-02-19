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
    $routes->get('general', 'Settings::general');
    $routes->get('email', 'Settings::email');
    $routes->get('invoices', 'Settings::invoices');
    $routes->get('client_permissions', 'Settings::client_permissions');
    $routes->get('modules', 'Settings::modules');
    $routes->get('cron_job', 'Settings::cron_job');
    $routes->get('notifications', 'Settings::notifications');
    $routes->get('integration', 'Settings::integration');
    $routes->get('events', 'Settings::events');
    $routes->get('tickets', 'Settings::tickets');
    $routes->get('tasks', 'Settings::tasks');
    $routes->get('ip_restriction', 'Settings::ip_restriction');
    $routes->get('db_backup', 'Settings::db_backup');
    $routes->post('save', 'Settings::save');
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

