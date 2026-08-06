<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/stocks', 'Stocks::index');
$routes->get('/stocks/(:num)', 'Stocks::show/$1');
$routes->get('/stocks/(:num)/predictions', 'Stocks::predictions/$1');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/stocks/search', 'Stocks::search');
    $routes->post('/stocks/create', 'Stocks::create', ['filter' => 'admin']);
    $routes->get('/stocks/(:num)/edit', 'Stocks::edit/$1', ['filter' => 'admin']);
    $routes->post('/stocks/(:num)/edit', 'Stocks::update/$1', ['filter' => 'admin']);
    $routes->post('/stocks/(:num)/delete', 'Stocks::delete/$1', ['filter' => 'admin']);
});