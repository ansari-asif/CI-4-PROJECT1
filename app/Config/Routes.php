<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/auth/registration', 'Auth::registration');
$routes->post('/auth/login', 'Auth::login');
$routes->post('/user/add-user','User::add_user');
