<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Usuarios');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Usuarios::login');
$routes->get('inicio', 'Inicio::index');
$routes->get('unidades', 'Unidades::index');
$routes->get('unidades/baja', 'Unidades::index/0');
$routes->get('categorias', 'Categorias::index');
$routes->get('categorias/baja', 'Categorias::index/0');
$routes->get('clientes', 'Clientes::index');
$routes->get('clientes/baja', 'Clientes::index/0');
$routes->get('productos', 'Productos::index');
$routes->get('productos/baja', 'Productos::index/0');
$routes->get('roles', 'Roles::index');
$routes->get('roles/baja', 'Roles::index/0');
$routes->get('cajas', 'Cajas::index');
$routes->get('cajas/baja', 'Cajas::index/0');
$routes->get('ventas', 'Ventas::index');
$routes->get('ventas/baja', 'Ventas::index/0');
$routes->get('usuarios', 'Usuarios::index');
$routes->get('usuarios/baja', 'Usuarios::index/0');


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
