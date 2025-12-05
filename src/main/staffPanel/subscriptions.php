<?php
$id = $_SESSION['id'];
if (isset($_SESSION['id'])) {
    $statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $statement->execute(array(':id' => $id));
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    $roleStatement = $connection->prepare('SELECT * FROM roles WHERE roleId = :roleId LIMIT 1');
    $roleStatement->execute(array(':roleId' => $result['role']));
    $roleResult = $roleStatement->fetch(PDO::FETCH_ASSOC);

    if ($result['role'] != -1 && ($roleResult['viewSubscriptionData'] == 1 OR $roleResult['manageSubscription'] == '1')) {
        $subscriptionsStatement = $connection->prepare('SELECT * FROM subscriptions WHERE userId = :userId LIMIT 1');
        $subscriptionsStatement->execute(array(':userId' => (int)$_GET['userId']));
        $subscriptionsResult = $subscriptionsStatement->fetchAll(PDO::FETCH_ASSOC);

        require_once APP_ROOT . 'src/views/staffPanel/subscriptions.view.php';
    } else {
        header("HTTP/1.0 403 Forbidden");
        require_once APP_ROOT . 'src/main/staffPanel/noAccess.php';
        exit();
    }
} else {
    header('Location: ../auth/signin');
    exit;
}
