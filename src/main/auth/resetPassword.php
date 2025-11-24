<?php
if(isset($_SESSION['id'])) {
  header('Location: /dashboard/');
  exit;
} else if (isset($_COOKIE['id'])) {
  header('Location: /auth/signin');
  exit;
}

if (!isset($_GET['token']) || empty($_GET['token'])) {
  header('Location: /auth/signin');
  exit;
} else{
  $recoveryToken = trim(filter_input(INPUT_GET, 'token', FILTER_UNSAFE_RAW));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $recoveryStatement = $connection->prepare('SELECT * FROM userrecovery WHERE recoveryToken = :recoveryToken LIMIT 1');
  $recoveryStatement->execute(array(':recoveryToken' => $recoveryToken));
  $recoveryResult = $recoveryStatement->fetch();

  if (!$recoveryResult) {
    header('Location: /auth/signin?token=invalid');
    exit;
  }
  $userId = $recoveryResult['userId'];

  $statement = $connection->prepare('SELECT * FROM users WHERE id = :userId LIMIT 1');
  $statement->execute(array(':userId' => $userId));
  $result = $statement->fetch();

  if (!$result) {
    header('Location: /auth/signin?user=deleted');
    exit;
  }

  $newPassword = trim(filter_input(INPUT_POST, 'newPassword', FILTER_UNSAFE_RAW));
  $newPassword2 = trim(filter_input(INPUT_POST, 'newPassword2', FILTER_UNSAFE_RAW));
  $pepper = $_ENV['pepper'];

  if (empty($newPassword) || empty($newPassword2)) {
    $errors[] = 'Rellena todo el formulario.';
  } elseif (strlen($newPassword) < 8) {
    $errors[] = 'La contraseña debe tener 8 caracteres o más.';
  } elseif ($newPassword !== $newPassword2) {
    $errors[] = 'Las contraseñas no coinciden.';
  }

  $tokenTime = strtotime($recoveryResult['lastRecoveryTokenDate']);
  $oneHourAgo = strtotime('-1 hour');

  if ($tokenTime < $oneHourAgo) {
    $errors[] = 'El token de recuperación ha expirado. Por favor, solicita uno nuevo.';
  }

  if(empty($errors)){
    try{
      $connection->beginTransaction();
      $newPassPepper = $newPassword . $pepper;
      $hash = password_hash($newPassPepper, PASSWORD_DEFAULT, ['cost' => 12]);
            
      $statement = $connection->prepare('UPDATE users SET password = :password WHERE id = :id');
      $statement->execute([
        ':id' => $userId,
        ':password' => $hash,
      ]);

      $estatement = $connection->prepare('UPDATE userrecovery SET recoveryToken = :recoveryToken, lastRecoveryTokenDate = :lastRecoveryTokenDate, lastRecoveryDate = :lastRecoveryDate WHERE userId = :userId');
      $estatement->execute(array(
        ':recoveryToken' => NULL,
        ':userId' => $userId,
        ':lastRecoveryTokenDate' => NULL,
        ':lastRecoveryDate' => date('Y-m-d H:i:s'),
      ));

      $connection->commit();
      header('Location: /auth/signin?reset=success');
      exit;
    } catch (PDOException $e) {
      $connection->rollBack();
      error_log("ACCOUNT RECOVERY (1): Database error during account recivery: " . $e->getMessage());
      $errors[] = 'Ocurrió un error en la base de datos al intentar registrar su nueva contraseña. Inténtalo de nuevo o contacta al staff.';
    }
  }
}

require_once APP_ROOT . 'src/views/auth/resetPassword.view.php';