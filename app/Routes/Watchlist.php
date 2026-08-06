<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/watchlist', 'Watchlist::index');
    $routes->get('/watchlist/add/(:num)', 'Watchlist::add/$1');
    $routes->get('/watchlist/remove/(:num)', 'Watchlist::remove/$1');
    $routes->post('/watchlist/toggle/(:num)', 'Watchlist::toggle/$1');
    $routes->post('/watchlist/bucket/create', 'Watchlist::createBucket');
    $routes->post('/watchlist/bucket/(:num)/delete', 'Watchlist::deleteBucket/$1');
    $routes->post('/watchlist/move-to-bucket', 'Watchlist::moveToBucket');
});