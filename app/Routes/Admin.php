<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->group('admin', ['filter' => 'admin'], static function ($routes) {
    $routes->get('', 'Admin\Dashboard::index');
    $routes->get('users', 'Admin\Users::users');
    $routes->get('users/make-admin/(:num)', 'Admin\Users::makeAdmin/$1');
    $routes->get('users/remove-admin/(:num)', 'Admin\Users::removeAdmin/$1');
    $routes->get('users/delete/(:num)', 'Admin\Users::deleteUser/$1');
    $routes->get('users/view/(:num)', 'Admin\Users::viewAsUser/$1');
    $routes->get('users/stop-viewing', 'Admin\Users::stopViewing');
    $routes->get('stocks', 'Admin\Stocks::stocks');
    $routes->get('stocks/create', 'Admin\Stocks::createStock');
    $routes->post('stocks/save', 'Admin\Stocks::saveStock');
    $routes->get('stocks/bulk', 'Admin\Stocks::bulkAddForm');
    $routes->get('stocks/edit/(:num)', 'Admin\Stocks::editStock/$1');
    $routes->post('stocks/update/(:num)', 'Admin\Stocks::updateStock/$1');
    $routes->post('stocks/bulk-add', 'Admin\Stocks::bulkAddStocks');
    $routes->post('stocks/bulk-edit', 'Admin\Stocks::bulkEditStocks');
    $routes->post('stocks/bulk-delete', 'Admin\Stocks::bulkDeleteStocks');
    $routes->get('stocks/delete/(:num)', 'Admin\Stocks::deleteStock/$1');
    $routes->get('screeners', 'Admin\Screeners::screeners');
    $routes->get('screeners/delete-all', 'Admin\Screeners::deleteScreeners');
    $routes->get('predictions', 'Admin\Predictions::predictions');
    $routes->get('predictions/toggle-public/(:num)', 'Admin\Predictions::togglePublic/$1');
    $routes->get('predictions/delete/(:num)', 'Admin\Predictions::deletePrediction/$1');
    $routes->get('pages', 'Admin\Pages::pages');
    $routes->match(['GET', 'POST'], 'pages/edit/(:num)', 'Admin\Pages::editPage/$1');
    $routes->match(['GET', 'POST'], 'pages/create', 'Admin\Pages::editPage');
    $routes->post('pages/save', 'Admin\Pages::savePage');
    $routes->get('pages/delete/(:num)', 'Admin\Pages::deletePage/$1');
    $routes->get('settings', 'Admin\Settings::settings');
    $routes->post('settings/update', 'Admin\Settings::updateSettings');
    $routes->get('featured-stocks', 'Admin\Settings::featuredStocks');
    $routes->get('featured-stocks/clear', 'Admin\Settings::clearFeaturedStocks');
    $routes->post('featured-stocks/save', 'Admin\Settings::saveFeaturedStocks');
});