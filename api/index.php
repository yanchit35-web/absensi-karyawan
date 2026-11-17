<?php
/**
 * Entry point for Vercel Serverless Function
 */

// Pastikan flag VERCEL aktif
putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

// Pastikan direktori writable di serverless (/tmp)
foreach (['cache', 'session', 'logs', 'uploads', 'uploads/absensi'] as $dir) {
    $path = '/tmp/' . $dir;
    if (!is_dir($path)) {
        @mkdir($path, 0777, true);
    }
}

// Forward to CodeIgniter 4 Front Controller
define('FCPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

chdir(FCPATH);
require __DIR__ . '/../public/index.php';
