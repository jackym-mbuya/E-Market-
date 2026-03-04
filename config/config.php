<!------?php
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


<?php
$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$port = getenv('DB_PORT');

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!";
} catch(PDOException $e) {
    echo "Connection Error: " . $e->getMessage();
}