<?php 
$statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
$statement->execute(array(':id' => $id));
$result = $statement->fetch();

$messageId = $_GET['msg'];

if(!isset($messageId)){
    echo "Este chat de soporte no existe. Número de mensaje no encontrado: " . $messageId . "<a href='/dashboard/'>Volver al panel de control</a>";
}

$estatement = $connection->prepare('SELECT * FROM helpBody WHERE messageId = :messageId LIMIT 1');
$estatement->execute(array(':messageId' => $messageId));
$eresult = $estatement->fetch();

$answersStatement = $connection->prepare('SELECT * FROM helpAnswers WHERE messageId = :messageId');
$answersStatement->execute(array(':messageId' => $messageId));
$answers = $answersStatement->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST["answerInsert"])) {
        $postAnswer = htmlspecialchars(trim($_POST['postAnswer']), ENT_QUOTES, 'UTF-8');

        $estatement = $connection->prepare('INSERT INTO helpAnswers (answerId, answerPublisherId, messageId, message, creationDate) VALUES (NULL, :answerPublisherId, :messageId, :message, NOW())');
        $estatement->execute(array(
          ':answerPublisherId' => $id,
          ':messageId' => $messageId,
          ':message' => $postAnswer,
        ));
        header('Location: ' . htmlspecialchars('/dashboard/supportMsg?msg=' . $messageId));
        exit;
    }
    if (isset($_POST["answerDelete"])) {
        $estatement = $connection->prepare('DELETE FROM helpBody WHERE messageId = :messageId');
        $estatement->execute(array(':messageId' => $messageId));        
        header('Location: support');
        exit;
    }
}

if (!isset($_SESSION['id'])) {
    header('Location: ../auth/signin');
    exit;
} else {
    if ($result['role'] != '-1') {
        $roleId = $result['role'];
        $rstatement = $connection->prepare('SELECT * FROM roles WHERE roleId = :roleId LIMIT 1');
        $rstatement->execute(array(':roleId' => $roleId));
        $rresult = $rstatement->fetch();
        if ($rresult['ticket'] == '1') {
            require_once APP_ROOT . 'src/views/dashboard/supportMsg.view.php';
        } else {
            require_once APP_ROOT . 'src/main/staffPanel/noAccess.php';
        }
    } else {
        if ($eresult['userId'] === $id) {
            require_once APP_ROOT . 'src/views/dashboard/supportMsg.view.php';
        } else {
            require_once APP_ROOT . 'src/main/staffPanel/noAccess.php';
        }
    }
}
