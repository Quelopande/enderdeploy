<?php
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
$id = $_SESSION['id'];

require '../connection.php';

$statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
$statement->execute(array(':id' => $id));
$result = $statement->fetch();


if (isset($_SESSION['id'])){
    require '../views/dashboard/dashboard.view.php';
} else if (!isset($_SESSION['id'])){
    header('Location: ../auth/signin.php');
} else {
    header('Location: ../auth/ban.php');
}