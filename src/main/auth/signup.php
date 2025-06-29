<?php
$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
  $password = $_POST['password'] ?? '';
  $password2 = $_POST['password2'] ?? '';
  $agree = isset($_POST['agree']);
  $email = strtolower($email);
  $role = '-1';
  $status = 'notverified';

  $pepper = $_ENV['pepper'] ?? ''; // Ensure this env var exists and is set

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
  if (empty($errors)) {
    require_once APP_ROOT . 'src/config/connection.php';
    $statement = $connection->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $statement->execute(array(':email' => $email));
    $result = $statement->fetch();

    if (!isset($_POST['agree'])) {
      $errors .= 'Acepta la política de privad y los terminos y condiciones para registrarse.';
    }

    if ($result != false) {
      $errors .= '¡Este email está en uso! Si crees que es un error contacta con soporte.';
    }

    $pepper = $_ENV['pepper'];
    $salt = openssl_random_pseudo_bytes(32);
    $passPepper = $password . $pepper . $salt;
    $hash = password_hash($passPepper, PASSWORD_BCRYPT, ['cost' => 12]);

    if ($password !== $password2) {
      $errors .= 'Las contraseñas no coinciden.';
    }
  }

  if ($errors === '') {
    try{
      $statement = $connection->prepare('INSERT INTO users (id, email, password, salt, status, role) VALUES (NULL, :email, :password, :salt, :status, :role)');
      $statement->execute(array(
        ':email' => $email,
        ':password' => $hash,
        ':status' => $status,
        ':salt' => $salt,
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

      $userLocationstatement = $connection->prepare('INSERT INTO usersCode (userId) VALUES (:userId)');
      $userLocationstatement->execute(array(
        ':userId' => $newUserId
      ));

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