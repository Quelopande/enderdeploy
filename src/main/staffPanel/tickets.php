<?php
if (isset($_SESSION['id'])) {
    $statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $statement->execute(array(':id' => $id));
    $result = $statement->fetch();    

    if ($result['role'] != '-1') {
        $roleId = $result['role'];
        $roleStatement = $connection->prepare('SELECT * FROM roles WHERE roleId = :roleId LIMIT 1');
        $roleStatement->execute(array(':roleId' => $roleId));
        $roleResult = $roleStatement->fetch();
        if ($roleResult['ticket'] == '1') {
            $messagesStatement = $connection->prepare('SELECT * FROM helpbody');
            $messagesStatement->execute();
            $messages = $messagesStatement->fetchAll();
            require_once APP_ROOT . 'src/views/staffPanel/tickets.view.php';
        } else {
            require 'noAccess.php';
        }
    }
} else {
    header('Location: ../auth/signin');
    exit;
}
