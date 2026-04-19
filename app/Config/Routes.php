<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// LOGIN
$routes->get('acceso/login', 'AccesoController::loginShowForm');
$routes->post('acceso/login', 'AccesoController::login');
$routes->get('acceso/logout', 'AccesoController::logout');
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Home::index');

    $routes->get('spotify/home', 'SpotifyController::home');
    $routes->get('spotify/buscar', 'SpotifyController::buscar');
    $routes->get('spotify/biblioteca', 'SpotifyController::biblioteca');
    $routes->get('spotify/playlist', 'SpotifyController::playlist');

    // Biblioteca
    $routes->post('spotify/biblioteca/agregar/(:num)', 'SpotifyController::agregarBiblioteca/$1');
    $routes->get('spotify/biblioteca/quitar/(:num)', 'SpotifyController::quitarBiblioteca/$1');
});
// Playlist
$routes->get('spotify/playlist', 'SpotifyController::playlist');
$routes->post('spotify/playlist/crear', 'SpotifyController::crearPlaylist');
$routes->get('spotify/playlist/ver/(:num)', 'SpotifyController::verPlaylist/$1');
$routes->post('spotify/playlist/agregar-cancion/(:num)', 'SpotifyController::agregarCancionPlaylist/$1');
$routes->get('spotify/playlist/quitar-cancion/(:num)/(:num)', 'SpotifyController::quitarCancionPlaylist/$1/$2');

// REGISTRO
$routes->get('register', 'RegisterController::index');
$routes->post('register/save', 'RegisterController::save');

// RUTAS PROTEGIDAS
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Home::index');

    $routes->get('spotify/home', 'SpotifyController::home');
    $routes->get('spotify/buscar', 'SpotifyController::buscar');
    $routes->get('spotify/biblioteca', 'SpotifyController::biblioteca');
    $routes->get('spotify/playlist', 'SpotifyController::playlist');
});

// RUTAS ADMIN
$routes->group('admin', ['filter' => 'admin'], static function ($routes) {
    $routes->get('canciones', 'Admin\CancionesController::index');
    $routes->get('canciones/create', 'Admin\CancionesController::create');
    $routes->post('canciones/store', 'Admin\CancionesController::store');
    $routes->get('canciones/edit/(:num)', 'Admin\CancionesController::edit/$1');
    $routes->post('canciones/update/(:num)', 'Admin\CancionesController::update/$1');
    $routes->get('canciones/delete/(:num)', 'Admin\CancionesController::delete/$1');

    
});