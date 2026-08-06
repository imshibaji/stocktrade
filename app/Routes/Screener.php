<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/screener/lists', 'Screener::publicListPage');
$routes->get('/screener/(:num)', 'Screener::publicListDetail/$1');
$routes->get('/api/screener/public-list/(:num)', 'Screener::publicList/$1');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/screener', 'Screener::index');
    $routes->get('/screener/docs', 'Screener::docs');
    $routes->get('/api/screener/run', 'Screener::run');
    $routes->post('/api/screener/run-manual', 'Screener::runManualQuery');
    $routes->post('/api/screener/save', 'Screener::save');
    $routes->get('/api/screener/lists', 'Screener::lists');
    $routes->get('/api/screener/public-lists', 'Screener::publicLists');
    $routes->get('/api/screener/load-list', 'Screener::loadList');
    $routes->post('/api/screener/delete-list', 'Screener::deleteList');
    $routes->post('/api/screener/toggle-public', 'Screener::togglePublic');
});