<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/investments', 'Investment::index');
    $routes->post('/investments/create', 'Investment::create');
    $routes->get('/investments/(:num)/sell', 'Investment::sellForm/$1');
    $routes->post('/investments/(:num)/sell', 'Investment::sell/$1');
    $routes->get('/investments/(:num)/edit', 'Investment::editForm/$1');
    $routes->post('/investments/(:num)/edit', 'Investment::update/$1');
    $routes->post('/investments/(:num)/delete', 'Investment::delete/$1');
    $routes->get('/investments/history', 'Investment::history');
    $routes->get('/investments/transactions', 'Investment::transactions');
    $routes->get('/portfolio', 'Investment::portfolio');
});