<?php
$errors = [];

if(isset($_SESSION['id'])) {
  header('Location: /dashboard/');
  exit;
} else if (isset($_COOKIE['id'])) {
  header('Location: /auth/signin');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)); 
  $password = trim(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW));
  $password2 = trim(filter_input(INPUT_POST, 'password2', FILTER_UNSAFE_RAW));
  $agree = isset($_POST['agree']);
  $email = strtolower($email);
  $role = '-1';
  $status = 'notverified';

  $pepper = $_ENV['pepper'];

  if (empty($password) || empty($email) || empty($password2)) {
    $errors[] = 'Rellena todo el formulario.';
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Correo electrónico no válido.';
  }
  if (strlen($email) > 254) {
    $errors[] = '¡Esta dirección de correo es muy larga! Inserta un correo de 254 caracteres o menos.';
  }
  if (strlen($password) < 8) {
    $errors[] = 'La contraseña debe tener 8 caracteres o más.';
  }
  if ($password !== $password2) {
    $errors[] = 'Las contraseñas no coinciden.';
  }
  if (!$agree) {
    $errors[] = 'Debes aceptar la política de privacidad y los términos y condiciones para registrarte.';
  }

  require_once APP_ROOT . 'src/config/connection.php';
  $statement = $connection->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
  $statement->execute(array(':email' => $email));
  $result = $statement->fetch();
  if ($result != false) {
    $errors[] = '¡Este email está en uso! Si crees que es un error contacta con soporte.';
  }
  
  if (empty($errors)) {
    $pepper = $_ENV['pepper'];
    $passwordWithPepper = $password . $pepper; 
    $hash = password_hash($passwordWithPepper, PASSWORD_DEFAULT, ['cost' => 12]);

    try{
      $statement = $connection->prepare('INSERT INTO users (id, email, password, status, role) VALUES (NULL, :email, :password, :status, :role)');
      $statement->execute(array(
        ':email' => $email,
        ':password' => $hash,
        ':status' => $status,
        ':role' => $role
      ));
      $newUserId = $connection->lastInsertId();

      $customer = \Stripe\Customer::create([
        'name' => (string)$newUserId,
        'email' => $email,
        'metadata' => [
          'userId' => $newUserId
        ],
      ]);
      $stripeCustomerId = $customer->id;
      $userStatement = $connection->prepare('UPDATE users SET stripeCustomerId = :stripeCustomerId WHERE id = :userId');
      $userStatement->execute(array(
        ':stripeCustomerId' => $stripeCustomerId,
        ':userId' => $newUserId
      ));

      $userLocationstatement = $connection->prepare('INSERT INTO usersLocation (userId) VALUES (:userId)');
      $userLocationstatement->execute(array(
        ':userId' => $newUserId
      ));

      $usersCodeStatement = $connection->prepare('INSERT INTO usersCode (userId) VALUES (:userId)');
      $usersCodeStatement->execute([':userId' => $newUserId]);

      $connection->commit();

      function encryptCookie($data) {
        $encryptionKey = $_ENV['cookieEncryptKey'];
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-gcm'));
        $ciphertext = openssl_encrypt($data, 'aes-256-gcm', $encryptionKey, 0, $iv, $tag);
        return base64_encode($iv . $tag . $ciphertext);
      }
      $encryptedCookieValue = encryptCookie($newUserId);

      setcookie('user', $encryptedCookieValue, time() + 15 * 24 * 60 * 60, '/', '', true, true);
      header('Location: ../auth/signin');
      exit; 
    } catch (PDOException $e) {
      $connection->rollBack();
      error_log("SIGNUP (1): Database error during signup: " . $e->getMessage());
      $errors[] = 'Ocurrió un error en la base de datos al registrar tu cuenta. Inténtalo de nuevo.';
    } catch (\Stripe\Exception\ApiErrorException $e) {
      $connection->rollBack();
      error_log("SIGNUP (2): Stripe API error during signup for email " . $email . ": " . $e->getMessage());
      $errors[] = 'Ocurrió un error con el servicio de pago (Stripe). Por favor, inténtalo de nuevo o contacta con soporte.';
    } catch (Exception $e) {
      $connection->rollBack();
      error_log("SIGNUP (3): General error during signup for email " . $email . ": " . $e->getMessage());
      $errors[] = 'Ocurrió un error inesperado al registrar tu cuenta. Inténtalo de nuevo.';
    }
  }
}

require_once APP_ROOT . 'src/views/auth/signup.view.php';