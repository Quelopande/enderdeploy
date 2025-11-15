<?php
$id = $_SESSION['id'];
if (isset($_SESSION['id'])) {
    $statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $statement->execute(array(':id' => $id));
    $result = $statement->fetch();

    if ($result['role'] != -1) {
        require_once APP_ROOT . 'src/views/staffPanel/index.view.php';
    } else {
        header("HTTP/1.0 403 Forbidden");
        require_once APP_ROOT . 'src/main/staffPanel/noAccess.php';
        exit();
    }
} else {
    header('Location: ../auth/signin');
    exit;
}
