<?phpini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
$id = $_SESSION['id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $user = htmlspecialchars(strtolower(trim($_POST['user'])), ENT_QUOTES, 'UTF-8');
  $password = strtolower($_POST['password']);
  $email = filter_var(strtolower($_POST['email']), FILTER_SANITIZE_EMAIL);
  $password2 = trim($_POST['password2']);
  $rank = 'user';
  $status = 'notverified';
  $code= mt_rand(211111,999999);
  require '../vendor/autoload.php';

  \Stripe\Stripe::setApiKey('sk_test_tu_clave_secreta');
  
  $errors = '';

  if (empty($user) || empty($password) || empty($email) || empty($password2)) {
    $errors .= 'Rellena todo el formulario.';
  } else if (!preg_match('/^[a-zA-Z0-9]+$/', $user)) {
    $errors .= 'El nombre de usuario solo puede contener letras y números.';
  } else if (strlen($password) < 8) {
    $errors .= 'La contraseña debe tener 8 carácteres o más';
  } else if (strlen($user) > 20) {
    $errors .= 'La máxima longitud del nombre de usuario es de 20 carácteres.';
  } else if (strlen($email) > 254) {
    $errors .= '¡Este dirección de correo es muy larga! Inserta un correo 254 carácteres o menos.';
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors .= 'Correo electrónico no valido.';
  } else {
    require '../connection.php';

    $statement = $connection->prepare('SELECT * FROM users WHERE user = :user LIMIT 1');
    $statement->execute(array(':user' => $user));
    $result = $statement->fetch();

    $statement = $connection->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $statement->execute(array(':email' => $email));
    $eresult = $statement->fetch();

    if (!isset($_POST['agree'])) {
      $errors .= 'Acepta la política de privad y los terminos y condiciones para registrarse.';
    }

    if ($result != false) {
      $errors .= 'Este usuario ya existe.';
    }
    if ($eresult != false) {
      $errors .= '¡Este email está en uso! Si crees que es un error contacta con soporte.';
    }

    $pepper = getenv('pepper');
    $salt = openssl_random_pseudo_bytes(32);
    $passPepper = $password . $pepper . $salt;
    $hash = password_hash($passPepper, PASSWORD_BCRYPT, ['cost' => 12]);

    if ($password !== $password2) {
      $errors .= 'Las contraseñas no coinciden.';
    }
  }

  if ($errors === '') {
    $statement = $connection->prepare('INSERT INTO users (id, user, email, password, salt, code, status) VALUES (NULL, :user, :email, :password, :salt, :code, :status)');
    $statement->execute(array(
      ':user' => $user,
      ':email' => $email,
      ':password' => $hash,
      ':code' => $code,
      ':status' => $status,
      ':salt' => $salt,
    ));
    $id = $connection->lastInsertId();
    $customer = \Stripe\Customer::create([
      'name' => "$id",
      'email' => "$email",
      'metadata' => [
        'userId' => $id
      ],
    ]);
    $stripeId = $customer->id;
    $userStatement = $connection->prepare('UPDATE users SET stripeId = :stripeId WHERE id = :userId');
    $userStatement->execute(array(':stripeId' => $stripeId, ':userId' => $id));
    function encryptCookie($data) {
      $encryptionKey = getenv('cookieEncryptKey');
      $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-gcm'));
      $ciphertext = openssl_encrypt($data, 'aes-256-gcm', $encryptionKey, 0, $iv, $tag);
      return base64_encode($iv . $tag . $ciphertext);
  }

  $encryptedCookieValue = encryptCookie($id);

  setcookie('user', $encryptedCookieValue, time() + 15 * 24 * 60 * 60, '/', '', true, true);
  header('Location: ../auth/signin.php');
    exit;
  }
}

require '../views/auth/signup.view.php';?>