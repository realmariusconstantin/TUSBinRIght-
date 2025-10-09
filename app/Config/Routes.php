<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', fn() => 'Backend is running!');

// Handle CORS preflight
$routes->options('(:any)', function() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    exit;
});

// Actual endpoints
$routes->post('register', 'User::register');
$routes->post('login', 'User::login');
