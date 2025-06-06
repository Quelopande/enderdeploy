<?php
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
$id = $_SESSION['id'];

function deleteCookie($name) {
    setcookie($name, '', time() - 3600, '/', '', true, true);
}

deleteCookie('id');
session_unset();
session_destroy();

header('Location: ../auth/signin');
exit;
?>