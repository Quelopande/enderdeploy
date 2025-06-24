<?php
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
session_start();
define('APP_ROOT', __DIR__ . '/../');

$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

require_once APP_ROOT . 'vendor/autoload.php';

ini_set('display_errors', 0); // Disable error display in production
ini_set('log_errors', 1);
ini_set('error_log', APP_ROOT . 'storage/logs/generalError.log');
error_reporting(E_ALL);

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(APP_ROOT);
$dotenv->load();

$path = rtrim($path, '/');
if (empty($path)) {
    $path = '/';
}

if ($path === '/') {
    $target_file = 'index1.html';
    if (file_exists($target_file)) {
        require_once $target_file;
        exit();
    }
}

if (strpos($path, '/dashboard') === 0) {
    $subpath = substr($path, strlen('/dashboard/'));
    $target_file = APP_ROOT . 'src/main/dashboard/' . $subpath . '.php';

    require_once APP_ROOT . 'src/config/connection.php';
    $id = $_SESSION['id'];
    if (file_exists($target_file)) {
        require_once $target_file;
        exit();
    } else if ($subpath === 'index' || $subpath === '') {
        require_once APP_ROOT . 'src/main/dashboard/index.php';
        exit();
    }
}

if (strpos($path, '/auth') === 0) {
    $subpath = substr($path, strlen('/auth/'));
    $target_file = APP_ROOT . 'src/main/auth/' . $subpath . '.php';
    if (file_exists($target_file)) {
        require_once $target_file;
        exit();
    } else{
        require_once APP_ROOT . 'src/main/auth/signin.php';
        exit();
    }
}

if (strpos($path, '/staffPanel') === 0) {
    // staffPanel === Works | staffpanel === Doesn't work
    $subpath = substr($path, strlen('/staffPanel/'));
    $target_file = APP_ROOT . 'src/main/staffPanel/' . $subpath . '.php';
    require_once APP_ROOT . 'src/config/connection.php';
    $id = $_SESSION['id'];
    if (file_exists($target_file)) {
        require_once $target_file;
        exit();
    } else{
        require_once APP_ROOT . 'src/main/staffPanel/index.php';
        exit();
    }
}

header("HTTP/1.0 404 Not Found");
echo "<h1>Error 404 - Página no encontrada.</h1>";
echo "<p>No existe la página: " . htmlspecialchars($path) . "</p>";
echo "<a href='/'>Volver al inicio</h1>";
exit();

?>