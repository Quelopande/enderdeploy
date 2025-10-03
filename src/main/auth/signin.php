<?php
$errors = '';

function encryptCookie($data) {
    $cookieEncryptKey = $_ENV['cookieEncryptKey'];
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-gcm'));
    $ciphertext = openssl_encrypt($data, 'aes-256-gcm', $cookieEncryptKey, 0, $iv, $tag);
    return base64_encode($iv . $tag . $ciphertext);
}

function decryptCookie($data) {
    $cookieEncryptKey = $_ENV['cookieEncryptKey'];
    $data = base64_decode($data);
    $iv_length = openssl_cipher_iv_length('aes-256-gcm');
    $iv = substr($data, 0, $iv_length);
    $tag = substr($data, $iv_length, 16);
    $ciphertext = substr($data, $iv_length + 16);
    return openssl_decrypt($ciphertext, 'aes-256-gcm', $cookieEncryptKey, 0, $iv, $tag);
}

if (isset($_COOKIE['id'])) {
    $decryptedId = decryptCookie($_COOKIE['id']);
    if ($decryptedId) {
        $_SESSION['id'] = $decryptedId;
        header('Location: /dashboard/');
        exit;
    }
} else if(isset($_SESSION['id'])) {
    header('Location: /dashboard/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(strtolower(trim($_POST['email'])), ENT_QUOTES, 'UTF-8');
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $errors .= '<div class="alert alert-danger d-flex align-items-center" role="alert">Please, fill the gaps.</div>';
    } else {
        require_once APP_ROOT . 'src/config/connection.php';
        $statement = $connection->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute([':email' => $email]);
        $result = $statement->fetch();

        if ($result === false) {
            $errors .= '<div class="alert alert-danger d-flex align-items-center" role="alert">Email account not found.</div>';
        } else {
            $id = $result['id'];
            $pepper = $_ENV['pepper'];
            $passPepper = $password . $pepper . $result['salt'];

            if (!password_verify($passPepper, $result['password'])) {
                $errors .= '<div class="alert alert-danger d-flex align-items-center" role="alert">Incorrect password.</div>';
            }
        }
    }

    if ($errors === '') {
        $encryptedCookieValue = encryptCookie($id);
        setcookie('id', $encryptedCookieValue, time() + 3 * 24 * 60 * 60, '/', '', true, true); // 15 days cookie
        $_SESSION['id'] = $id;
        header("Location: /dashboard/");
        exit;
    }
}
require_once APP_ROOT . 'src/views/auth/signin.view.php';?>