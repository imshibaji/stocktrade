<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Home::index');
$routes->get('/about', 'About::index');
$routes->get('/contact', 'Contact::index');
$routes->post('/contact/send', 'Contact::send');

$routes->get('/pricing', 'Pricing::index');
$routes->get('/terms', 'Terms::index');
$routes->get('/privacy', 'Privacy::index');
$routes->get('/faq', 'Faq::index');
$routes->get('/docs/user', 'Docs::user');
$routes->get('/docs/developer', 'Docs::developer');

$routes->get('/stocks', 'Stocks::index');
$routes->get('/stocks/(:num)', 'Stocks::show/$1');
$routes->get('/stocks/(:num)/predictions', 'Stocks::predictions/$1');

$routes->get('/page/(:any)', 'Page::show/$1');

$routes->get('/login', 'Auth::login', ['filter' => 'guest']);
$routes->post('/login', 'Auth::attemptLogin', ['filter' => 'guest']);
$routes->get('/register', 'Auth::register', ['filter' => 'guest']);
$routes->post('/register', 'Auth::attemptRegister', ['filter' => 'guest']);
$routes->get('/logout', 'Auth::logout');

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

$routes->get('/api', 'Api::index');
$routes->get('/api/search', 'Api::search');
$routes->get('/api/quote/(:any)/(:any)', 'Api::getQuote/$1/$2', ['filter' => 'auth']);
$routes->get('/api/quote/(:any)', 'Api::lookup/$1', ['filter' => 'auth']);
$routes->get('/api/quotes/(:any)/(:any)', 'Api::getQuotes/$1/$2', ['filter' => 'auth']);
$routes->get('/api/historical/(:any)/(:any)/(:any)', 'Api::getHistorical/$1/$2/$3');
$routes->get('/api/dividends/(:any)/(:any)/(:any)', 'Api::getDividends/$1/$2/$3');
$routes->get('/api/splits/(:any)/(:any)/(:any)', 'Api::getSplits/$1/$2/$3');
$routes->get('/api/exchange/(:any)/(:any)', 'Api::getExchangeRate/$1/$2', ['filter' => 'auth']);
$routes->get('/api/options/(:any)/(:any)', 'Api::getOptionChain/$1/$2');
$routes->get('/api/summary/(:any)/(:any)', 'Api::getSummary/$1/$2');
$routes->get('/api/screener/public-list/(:num)', 'Screener::publicList/$1');
$routes->get('/api/news/(:any)', 'Api::getNewsStream/$1', ['filter' => 'auth']);
$routes->get('/api/search/(:any)', 'Api::getSearch/$1', ['filter' => 'auth']);
$routes->post('/api/stocks/import', 'Api::importStock', ['filter' => 'auth']);
$routes->post('/api/stocks/bulk-import', 'Api::bulkImport', ['filter' => 'auth']);
$routes->post('/api/stocks/refresh', 'Api::refreshStock', ['filter' => 'auth']);

$routes->get('/api-playground', 'ApiDocs::index', ['filter' => 'admin']);

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/dashboard', 'Dashboard::index');
    $routes->get('/stocks/search', 'Stocks::search');
    $routes->post('/stocks/create', 'Stocks::create', ['filter' => 'admin']);
    $routes->get('/stocks/(:num)/edit', 'Stocks::edit/$1', ['filter' => 'admin']);
    $routes->post('/stocks/(:num)/edit', 'Stocks::update/$1', ['filter' => 'admin']);
    $routes->post('/stocks/(:num)/delete', 'Stocks::delete/$1', ['filter' => 'admin']);
    $routes->get('/watchlist', 'Watchlist::index');
    $routes->get('/watchlist/add/(:num)', 'Watchlist::add/$1');
    $routes->get('/watchlist/remove/(:num)', 'Watchlist::remove/$1');
    $routes->post('/watchlist/toggle/(:num)', 'Watchlist::toggle/$1');
    $routes->post('/watchlist/bucket/create', 'Watchlist::createBucket');
    $routes->post('/watchlist/bucket/(:num)/delete', 'Watchlist::deleteBucket/$1');
    $routes->post('/watchlist/move-to-bucket', 'Watchlist::moveToBucket');
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
    $routes->get('/settings', 'Settings::index');
    $routes->post('/settings/update-profile', 'Settings::updateProfile');
    $routes->post('/settings/update-password', 'Settings::updatePassword');
    $routes->post('/settings/update-tax', 'Settings::updateTax');
    $routes->post('/settings/update-fees', 'Settings::updateFees');
    $routes->post('/settings/update-base-currency', 'Settings::updateBaseCurrency');
});

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
