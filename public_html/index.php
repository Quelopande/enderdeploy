<?php
ini_set('session.cookie_secure', 0); // 1 in production
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax'); // Lax, with Strict Stripe won't work fine
ini_set('session.use_strict_mode', 0); // 1 in production
ini_set('session.use_only_cookies', 1);
//ini_set('session.cookie_domain', '.rendercores.com'); // Comment this line for local development
error_reporting(E_ALL);
session_start();

define('APP_ROOT', __DIR__ . '/../');


ini_set('display_errors', 0); // Disable error display in production
ini_set('log_errors', 1);
ini_set('error_log', APP_ROOT . 'storage/logs/generalError.log');

require APP_ROOT . 'vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(APP_ROOT);

$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = rtrim($path, '/');

if (empty($path) || $path === '/' || $path === '/index') {
    $path = '/index';
} else if ($path === '/dashboard') {
    $path = '/dashboard/index';
} else if ($path === '/staffPanel') {
    $path = '/staffPanel/index';
}

// staff panel only shows up when the path starts with /staffPanel and not with /staffpanel
if (strpos($path, '/dashboard') === 0 || strpos($path, '/staffPanel') === 0) {
    $dotenv->safeLoad();
    require_once APP_ROOT . 'src/config/connection.php';
    if (!isset($_SESSION['id'])) {
        header('Location: /auth/signin');
        exit;
    } else if(isset($_SESSION['id'])){
        $id = $_SESSION['id'];
    }
} else if(strpos($path, '/auth') === 0){
    $dotenv->safeLoad();
    require_once APP_ROOT . 'src/config/connection.php';
}

if (strpos($path, '/auth') === 0) {
    $target_file = APP_ROOT . 'src/main' . $path . '.php';
} else if (strpos($path, '/dashboard') === 0) {
    $target_file = APP_ROOT . 'src/main' . $path . '.php';
} else if (strpos($path, '/staffPanel') === 0) {
    $target_file = APP_ROOT . 'src/main' . $path . '.php';
} else {
    $target_file = APP_ROOT . 'src/main/pages' . $path . '.php';
}

if (file_exists($target_file)) {
    require_once $target_file;
    exit();
}

header("HTTP/1.0 404 Not Found");
echo "<h1>Error 404 - Página no encontrada.</h1>";
echo "<p>No existe la página: " . htmlspecialchars($path) . "</p>";
echo "<a href='/'>Volver al inicio</h1>";
exit();
?>