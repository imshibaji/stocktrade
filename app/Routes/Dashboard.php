<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/dashboard', 'Dashboard::index');
    $routes->get('/settings', 'Settings::index');
    $routes->post('/settings/update-profile', 'Settings::updateProfile');
    $routes->post('/settings/update-password', 'Settings::updatePassword');
    $routes->post('/settings/update-tax', 'Settings::updateTax');
    $routes->post('/settings/update-fees', 'Settings::updateFees');
    $routes->post('/settings/update-base-currency', 'Settings::updateBaseCurrency');
});