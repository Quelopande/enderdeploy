<?php
$statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
$statement->execute(array(':id' => $id));
$result = $statement->fetch();

$subscriptionsStatement = $connection->prepare('SELECT * FROM subscriptions WHERE userId = :userId ORDER BY FIELD(subscriptionStatus, "active", "suspended", "canceled"), subscriptionStartTime DESC');
$subscriptionsStatement->execute(array(':userId' => $id));
$subscriptions = $subscriptionsStatement->fetchAll(PDO::FETCH_ASSOC);

if (isset($_SESSION['id'])){
    require_once APP_ROOT . 'src/views/dashboard/dashboard.view.php';
} else if (!isset($_SESSION['id'])){
    header('Location: ../auth/signin');
} else {
    require_once APP_ROOT . 'src/main/auth/ban.php';
}