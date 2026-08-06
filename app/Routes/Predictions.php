<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->group('predictions', static function ($routes) {
    $routes->get('', 'PredictionQuery::index');
    $routes->get('create', 'PredictionQuery::create');
    $routes->post('save', 'PredictionQuery::save');
    $routes->get('(:num)', 'PredictionQuery::show/$1');
    $routes->get('(:num)/edit', 'PredictionQuery::edit/$1');
    $routes->post('(:num)/update', 'PredictionQuery::update/$1');
    $routes->post('(:num)/delete', 'PredictionQuery::delete/$1');
    $routes->post('(:num)/run', 'PredictionQuery::run/$1');
    $routes->get('(:num)/results', 'PredictionQuery::results/$1');
    $routes->get('public', 'PredictionQuery::publicList');
    $routes->get('public/(:num)', 'PredictionQuery::publicShow/$1');
    $routes->post('(:num)/toggle-public', 'PredictionQuery::togglePublic/$1');
});