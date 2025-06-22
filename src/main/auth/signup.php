<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $password = strtolower($_POST['password']);
  $email = filter_var(strtolower($_POST['email']), FILTER_SANITIZE_EMAIL);
  $password2 = trim($_POST['password2']);
  $rank = 'user';
  $status = 'notverified';
  $code= mt_rand(211111,999999);

  \Stripe\Stripe::setApiKey('' . $_ENV['stripeSecret'] . '');
  
  $errors = '';

  if (empty($password) || empty($email) || empty($password2)) {
    $errors .= 'Rellena todo el formulario.';
  } else if (strlen($password) < 8) {
    $errors .= 'La contraseña debe tener 8 carácteres o más';
  } else if (strlen($email) > 254) {
    $errors .= '¡Este dirección de correo es muy larga! Inserta un correo 254 carácteres o menos.';
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors .= 'Correo electrónico no valido.';
  } else {
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
    $statement = $connection->prepare('INSERT INTO users (id, email, password, salt, code, status) VALUES (NULL, :email, :password, :salt, :code, :status)');
    $statement->execute(array(
      ':email' => $email,
      ':password' => $hash,
      ':code' => $code,
      ':status' => $status,
      ':salt' => $salt,
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
  }
}

require_once APP_ROOT . 'src/views/auth/signup.view.php';