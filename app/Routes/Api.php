<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// No auth filter: these read endpoints back the public stock detail page
// (chart, company, dividends, options), which must work for guests *and*
// logged-in users. A `guest` filter would 302 logged-in users to /dashboard,
// leaving the chart stuck on "Couldn't reach the data feed".
$routes->group('api', static function ($routes) {
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
    // Query-string form used by the public /stocks search box and the
    // admin add-stock form: /api/search?q=...&exchange=...
    $routes->get('search', 'Api::search');
    $routes->get('search/(:any)', 'Api::getSearch/$1');
    $routes->post('stocks/import', 'Api::importStock');
    $routes->post('stocks/bulk-import', 'Api::bulkImport');
    $routes->post('stocks/refresh', 'Api::refreshStock');
});