<?php
define('BASE_URL', 'http://localhost/AI/');
define('SITE_NAME', 'eMarket');
define('CURRENCY', 'Ksh');

define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_db');
define('DB_USER', 'root');
define('DB_PASS', '');

define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function redirect($url) {
    header('Location: ' . BASE_URL . $url);
    exit;
}

function dd($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    exit;
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function formatPrice($price) {
    return CURRENCY . number_format($price, 2);
}
