<?php
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
$id = $_SESSION['id'];
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
    
    require '../connection.php';

    $statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $statement->execute(array(':id' => $id));
    $result = $statement->fetch();

    if ($result['role'] != -1) {
        require '../views/staffPanel/index.view.php';
    } else {
        require '../dashboard/noAccess.php';
    }
} else {
    header('Location: ../auth/signin.php');
    exit;
}
