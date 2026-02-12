<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Dashboard::index');

// Auth
$routes->get('auth/signin', 'Auth::signin');
$routes->post('auth/signin', 'Auth::attemptSignin');
$routes->post('auth/signout', 'Auth::signout');

$routes->get('auth/password/forgot', 'Auth::forgotPassword');
$routes->post('auth/password/forgot', 'Auth::sendResetLink');
$routes->get('auth/password/reset/(:segment)', 'Auth::resetPassword/$1');
$routes->post('auth/password/reset', 'Auth::doResetPassword');

// Approval
$routes->get('approval/requests', 'Approval::index');
$routes->get('approval/requests/(:num)', 'Approval::view/$1');
$routes->post('approval/requests/(:num)/approve', 'Approval::approve/$1');
$routes->post('approval/requests/(:num)/reject', 'Approval::reject/$1');
