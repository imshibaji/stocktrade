<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/login', 'Auth::login', ['filter' => 'guest']);
$routes->post('/login', 'Auth::attemptLogin', ['filter' => 'guest']);
$routes->get('/register', 'Auth::register', ['filter' => 'guest']);
$routes->post('/register', 'Auth::attemptRegister', ['filter' => 'guest']);
$routes->get('/logout', 'Auth::logout');