<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */ 
$routes->get('/', 'Home::index');
$routes->get('/login', 'Auth::login_page');
$routes->post('/login', 'Auth::login_page');
$routes->post('/auth/registration', 'Auth::registration');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/users','User::get_users');
$routes->get('/users/(:any)', 'User::get_users/$1');
$routes->post('/user/add-user','User::add_user');
$routes->post('/user/edit-user/(:any)','User::edit_user/$1');
$routes->post('/user/delete-user/(:num)','User::delete_user/$1');
$routes->get('/courses','Course::index',['filter' => 'AuthGuard']);
