<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Practical Lab
$routes->get('/login', 'LabController::User');
$routes->post('/login', 'LabController::User');

