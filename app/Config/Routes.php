<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Handle OPTIONS preflight requests for all routes
$routes->options('(:any)', static function () {});

$routes->get('/', fn() => 'Backend is running!');

// PUBLIC ROUTES (No JWT required)
$routes->post('register', 'AuthController::register');
$routes->post('login', 'AuthController::login');
$routes->post('logout', 'AuthController::logout');
$routes->get('total-scans', 'StatsController::totalScans');
$routes->get('total-users', 'StatsController::totalUsers');
$routes->get('disposal-rules-location', '\App\Controllers\Admin\DisposalRules::getDisposalRulesByItemAndLocationId');

// PROTECTED ROUTES (JWT required)
$routes->group('', ['filter' => 'jwtauth'], function($routes) {
    $routes->get('profile', 'AuthController::profile');
    $routes->put('profile/email', 'AuthController::updateEmail');
    $routes->put('profile/password', 'AuthController::updatePassword');
    $routes->post('refresh', 'AuthController::refresh');
    $routes->post('promote/(:num)', 'AuthController::promote/$1');
    $routes->post('create-scan', '\App\Controllers\Admin\UserScans::createScan');
});

// ADMIN ROUTES
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], static function($routes) {
    $routes->get('users', 'Users::getUsers');
    $routes->post('users/update', 'Users::updateUser');
    $routes->post('users/delete', 'Users::deleteUser');

    $routes->get('disposal-rules', 'DisposalRules::getDisposalRules');
    $routes->post('disposal-rules/create', 'DisposalRules::createDisposalRule');
    $routes->post('disposal-rules/update', 'DisposalRules::updateDisposalRule');
    $routes->post('disposal-rules/delete', 'DisposalRules::deleteDisposalRule');

    $routes->get('locations', 'Locations::getLocations');
    $routes->get('bin-types', 'BinTypes::getTypes');
    $routes->get('item-types', 'ItemTypes::getTypes');

    $routes->get('user-scans', 'UserScans::getScans');
    $routes->delete('user-scans', 'UserScans::deleteScans');
});

