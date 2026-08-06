<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->group('api', ['filter' => 'auth'], static function ($routes) {
    $routes->get('quote/(:any)/(:any)', 'Api::getQuote/$1/$2');
    $routes->get('quote/(:any)', 'Api::lookup/$1');
    $routes->get('market-status/(:any)', 'Api::marketStatus/$1');
    $routes->get('quotes/(:any)/(:any)', 'Api::getQuotes/$1/$2');
    $routes->get('historical/(:any)/(:any)/(:any)', 'Api::getHistorical/$1/$2/$3');
    $routes->get('dividends/(:any)/(:any)/(:any)', 'Api::getDividends/$1/$2/$3');
    $routes->get('splits/(:any)/(:any)/(:any)', 'Api::getSplits/$1/$2/$3');
    $routes->get('exchange/(:any)/(:any)', 'Api::getExchangeRate/$1/$2');
    $routes->get('options/(:any)/(:any)', 'Api::getOptionChain/$1/$2');
    $routes->get('summary/(:any)/(:any)', 'Api::getSummary/$1/$2');
    $routes->get('news/(:any)', 'Api::getNewsStream/$1');
    $routes->get('search/(:any)', 'Api::getSearch/$1');
    $routes->post('stocks/import', 'Api::importStock');
    $routes->post('stocks/bulk-import', 'Api::bulkImport');
    $routes->post('stocks/refresh', 'Api::refreshStock');
});