<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('api', ['namespace' => 'App\Controllers'], static function ($routes) {
    $routes->post('register', 'User::register');
    $routes->post('login', 'User::login');
});
