<?php
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
define('APP_ROOT', __DIR__ . '/../');

$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

require_once APP_ROOT . 'vendor/autoload.php';

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

if (strpos($path, '/dashboard/') === 0) {
    $subpath = substr($path, strlen('/dashboard/'));
    if (empty($subpath)) {
        header("Location: /dashboard/index.php", true, 302); // Default page
        exit();
    }
    $target_file = APP_ROOT . 'src/main/dashboard/' . $subpath . '.php';
    if (file_exists($target_file)) {
        $id = $_SESSION['id'];
        require_once APP_ROOT . 'src/config/connection.php';
        require_once $target_file;
        exit();
    }
}

if (strpos($path, '/auth/') === 0) {
    $subpath = substr($path, strlen('/auth/'));
    if (empty($subpath)) {
        header("Location: /auth/signin", true, 302); // Default page
        exit();
    }
    $target_file = APP_ROOT . 'src/main/auth/' . $subpath . '.php';
    if (file_exists($target_file)) {
        require_once $target_file;
        exit();
    }
}

if (strpos($path, '/staffpanel/') === 0) {
    $subpath = substr($path, strlen('/staffpanel/'));
    if (empty($subpath)) {
        header("Location: /staffpanel/signin", true, 302); // Default page
        exit();
    }
    $target_file = APP_ROOT . 'src/main/staffpanel/' . $subpath . '.php';
    if (file_exists($target_file)) {
        $id = $_SESSION['id'];
        require_once APP_ROOT . 'src/config/connection.php';
        require_once $target_file;
        exit();
    }
}

header("HTTP/1.0 404 Not Found");
echo "<h1>Error 404 - Página no encontrada.</h1>";
echo "<p>No existe la página: " . htmlspecialchars($path) . "</p>";
echo "<a href='/'>Volver al inicio</h1>";
exit();

?>