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

// PROTECTED ROUTES (JWT required)
$routes->group('', ['filter' => 'jwtauth'], function($routes) {
    $routes->get('profile', 'AuthController::profile');
    $routes->post('refresh', 'AuthController::refresh');
    $routes->post('promote/(:num)', 'AuthController::promote/$1');
});

// ADMIN ROUTES
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], static function($routes) {
    $routes->get('users', 'Users::getUsers');
    $routes->post('users/update', 'Users::updateUser');
    $routes->post('users/delete', 'Users::deleteUser');

    $routes->get('bin-steps', 'BinSteps::getSteps');
    $routes->post('bin-steps/create', 'BinSteps::createStep');
    $routes->post('bin-steps/update', 'BinSteps::updateStep');
    $routes->post('bin-steps/delete', 'BinSteps::deleteStep');

    $routes->get('bin-types', 'BinTypes::getTypes');
});

