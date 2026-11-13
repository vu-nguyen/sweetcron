<?php

namespace Config;

use Config\Services;
use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes = Services::routes();
$routes->setDefaultNamespace('App\\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

$routes->get('/', 'Home::index');
$routes->get('page/(:num)', 'Home::index/$1');
$routes->get('items/view/(:num)', 'Items::view/$1');
$routes->get('items/search/(:segment)/(:num)', 'Items::search/$1/$2');
$routes->get('items/search/(:segment)', 'Items::search/$1');
$routes->get('items/tag/(:segment)/(:num)', 'Items::tag/$1/$2');
$routes->get('items/tag/(:segment)', 'Items::tag/$1');
$routes->get('items/site/(:segment)/(:num)', 'Items::site/$1/$2');
$routes->get('items/site/(:segment)', 'Items::site/$1');
$routes->post('items/search', 'Items::doSearch');
$routes->get('feed', 'Feed::index');
$routes->get('feed/search/(:segment)', 'Feed::search/$1');
$routes->get('feed/tag/(:segment)', 'Feed::tag/$1');
$routes->get('feed/site/(:segment)', 'Feed::site/$1');
$routes->get('cron/fetch-items/(:segment)', 'Cron::fetchItems/$1');
$routes->get('admin/login', 'Admin\\Login::index');
$routes->post('admin/login', 'Admin\\Login::index');
$routes->get('admin/install', 'Admin\\Install::index');
$routes->post('admin/install', 'Admin\\Install::create');
$routes->get('admin/logout', 'Admin\\Login::logout');
$routes->get('admin', 'Admin\\Dashboard::index');
$routes->get('admin/items', 'Admin\\Items::index');
$routes->post('admin/items/(:num)/status', 'Admin\\Items::status/$1');
$routes->post('admin/items/(:num)/delete', 'Admin\\Items::delete/$1');
$routes->get('admin/feeds', 'Admin\\Feeds::index');
$routes->post('admin/feeds', 'Admin\\Feeds::create');
$routes->post('admin/feeds/(:num)/delete', 'Admin\\Feeds::delete/$1');
$routes->get('admin/write', 'Admin\\Write::index');
$routes->post('admin/write', 'Admin\\Write::create');
$routes->get('admin/options', 'Admin\\Options::index');
$routes->post('admin/options', 'Admin\\Options::save');

