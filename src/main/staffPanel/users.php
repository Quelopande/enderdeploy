<?php
$statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
$statement->execute(array(':id' => $id));
$result = $statement->fetch();

if ($result['role'] != '-1') {
    $roleId = $result['role'];
    $roleStatement = $connection->prepare('SELECT * FROM roles WHERE roleId = :roleId LIMIT 1');
    $roleStatement->execute(array(':roleId' => $roleId));
    $roleResult = $roleStatement->fetch();
    if ($roleResult['viewUser'] == '1') {
        $allUsersStatement = $connection->prepare('SELECT * FROM users');
        $allUsersStatement->execute();
        $allUsers = $allUsersStatement->fetchAll();
        require_once APP_ROOT . 'src/views/staffPanel/users.view.php';
    } else {
        header("HTTP/1.0 403 Forbidden");
        require_once APP_ROOT . 'src/main/staffPanel/noAccess.php';
        exit();
    }
} else {
    header("HTTP/1.0 403 Forbidden");
    require_once APP_ROOT . 'src/main/staffPanel/noAccess.php';
    exit();
}