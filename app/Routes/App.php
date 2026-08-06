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
$routes->get('/page/(:any)', 'Page::show/$1');
$routes->get('/api', 'Api::index');
$routes->get('/api-playground', 'ApiDocs::index', ['filter' => 'admin']);