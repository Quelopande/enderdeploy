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


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(trim($_POST['content']), ENT_QUOTES, 'UTF-8');

    $estatement = $connection->prepare('SELECT * FROM helpbody WHERE userId = :userId LIMIT 1');
    $estatement->execute(array(':userId' => $id));
    $eresult = $estatement->fetch();

    $estatement = $connection->prepare('INSERT INTO helpbody (messageId, userId, title, content, creationTime) VALUES (NULL, :userId, :title, :content, NOW())');
    $estatement->execute(array(
      ':userId' => $id,
      ':title' => $title,
      ':content' => $content,
    ));
}

$messagesStatement = $connection->prepare('SELECT * FROM helpbody WHERE userId = :userId');
$messagesStatement->execute(array(':userId' => $id));
$messages = $messagesStatement->fetchAll();

if (isset($_SESSION['id'])){
    require '../views/dashboard/support.view.php';
} else if (!isset($_SESSION['id'])){
    header('Location: ../auth/signin.php');
} else {
    header('Location: ../auth/ban.php');
}