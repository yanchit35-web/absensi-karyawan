<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'AuthController::index');

// Auth
$routes->get('/login', 'AuthController::index');
$routes->post('/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

// Dashboard
$routes->get('/dashboard', 'DashboardController::index', ['filter' => 'auth']);

// Profil & Ganti Password (Karyawan & Admin)
$routes->get('/profil', 'ProfileController::index', ['filter' => 'auth']);
$routes->post('/profil/update-password', 'ProfileController::updatePassword', ['filter' => 'auth']);

// Self Absensi (Foto Selfie & GPS)
$routes->get('/self-absen', 'AbsensiController::selfAbsen', ['filter' => 'auth']);
$routes->post('/self-absen', 'AbsensiController::submitSelfAbsen', ['filter' => 'auth']);

// Karyawan (Admin Only)
$routes->group('karyawan', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'KaryawanController::index');
    $routes->get('', 'KaryawanController::index');
    $routes->get('create', 'KaryawanController::create');
    $routes->post('store', 'KaryawanController::store');
    $routes->get('edit/(:num)', 'KaryawanController::edit/$1');
    $routes->post('update/(:num)', 'KaryawanController::update/$1');
    $routes->get('delete/(:num)', 'KaryawanController::delete/$1');
});

// Absensi
$routes->group('absensi', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'AbsensiController::index');
    $routes->get('', 'AbsensiController::index');
    $routes->get('create', 'AbsensiController::create');
    $routes->post('store', 'AbsensiController::store');
    $routes->get('edit/(:num)', 'AbsensiController::edit/$1');
    $routes->post('update/(:num)', 'AbsensiController::update/$1');
    $routes->get('delete/(:num)', 'AbsensiController::delete/$1');
});

// Laporan (Admin: semua karyawan, Karyawan: hanya miliknya sendiri)
$routes->group('laporan', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'LaporanController::index');
    $routes->get('', 'LaporanController::index');
    $routes->post('generate', 'LaporanController::generate');
    $routes->get('export-pdf', 'LaporanController::exportPdf');
    $routes->get('export-excel', 'LaporanController::exportExcel');
});

// Pengajuan Cuti / Izin / Sakit
$routes->group('pengajuan', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PengajuanController::index');
    $routes->get('', 'PengajuanController::index');
    $routes->get('create', 'PengajuanController::create');
    $routes->post('store', 'PengajuanController::store');
    $routes->post('approve/(:num)', 'PengajuanController::approve/$1');
    $routes->post('reject/(:num)', 'PengajuanController::reject/$1');
});

// Pengajuan Lembur
$routes->group('lembur', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'LemburController::index');
    $routes->get('', 'LemburController::index');
    $routes->get('create', 'LemburController::create');
    $routes->post('store', 'LemburController::store');
    $routes->post('approve/(:num)', 'LemburController::approve/$1');
    $routes->post('reject/(:num)', 'LemburController::reject/$1');
});

// Pengaturan Perusahaan & Jam Kerja (Admin Only)
$routes->group('pengaturan', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PengaturanController::index');
    $routes->get('', 'PengaturanController::index');
    $routes->post('update', 'PengaturanController::update');
});
