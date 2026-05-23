<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ===============================================
// 1. Rute Publik (Frontend & Autentikasi Dasar)
// ===============================================

// Rute Default Home Page
$routes->get('/', 'Home::index');

// Rute Login dan Logout
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

// Rute Google Auth
$routes->get('auth/google', 'Auth::googleLogin');
$routes->get('auth/google/callback', 'Auth::googleCallback');

// Rute Subscription (Midtrans)
$routes->get('subscribe', 'Subscription::index');
$routes->post('subscribe/pay', 'Subscription::pay');
$routes->post('subscribe/notification', 'Subscription::notification');
$routes->post('subscribe/finish_local', 'Subscription::finishLocal');
// ===============================================
// 2. Rute Admin (Dilindungi Filter 'auth')
// ===============================================
$routes->group('admin', ['filter' => 'auth'], function($routes){
    
    // Dashboard Admin
    $routes->get('/', 'Admin\Dashboard::index');
    
    // CRUD Articles (Menggunakan Resource Controller)
    $routes->resource('articles', ['controller' => 'Admin\ArticleController']);

    $routes->post('articles/publish/(:num)', 'Admin\ArticleController::publish/$1');
    $routes->post('articles/delete/(:num)', 'Admin\ArticleController::delete/$1'); // Menggunakan POST untuk simulasi DELETE via AJAX

    // Manajemen Komentar
    $routes->get('comments', 'Admin\CommentController::index');
    $routes->post('comments/status/(:num)', 'Admin\CommentController::updateStatus/$1');
    $routes->delete('comments/delete/(:num)', 'Admin\CommentController::delete/$1');

    // Manajemen Peran
    $routes->get('roles', 'Admin\RoleController::index'); 
    $routes->post('roles/save-permissions', 'Admin\RoleController::savePermissions'); 
    $routes->post('roles/store', 'Admin\RoleController::storeRole'); // <-- TAMBAHKAN INI
    $routes->post('roles/delete/(:num)', 'Admin\RoleController::deleteRole/$1'); // <-- Tambahkan ini

    // Manajemen Pengguna
    $routes->get('users', 'Admin\UserController::index'); 
    $routes->get('users/create', 'Admin\UserController::create'); // Rute untuk Form Tambah Pengguna (GET)
    $routes->post('users/store', 'Admin\UserController::store');   // Rute untuk Memproses Data Form (POST)
    $routes->post('users/update/(:num)', 'Admin\UserController::updateUser/$1');
});

$routes->get('article/(:segment)', 'Article::detail/$1');
$routes->post('article/comment', 'Article::submitComment');
$routes->post('article/rate', 'Article::updateStat');

// Rute Kategori Dinamis: Wajib menggunakan (:segment) atau (:any)
$routes->get('category/(:segment)', 'Category::index/$1'); 


// Rute Admin (Dilindungi Filter 'auth')
$routes->group('admin', ['filter' => 'auth'], function($routes){
    // ... (rute admin Anda) ...
});