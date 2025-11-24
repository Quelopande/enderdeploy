<?php
$errors = [];

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
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)); 
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW));

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    if (empty($email) || empty($password)) {
        $errors[] = 'Rellena todo el formulario.';
    } else {
        require_once APP_ROOT . 'src/config/connection.php';
        $statement = $connection->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute([':email' => $email]);
        $result = $statement->fetch();

        if ($result === false) {
            $errors[] = 'La cuenta no fué encontrada.';
        } else {
            $id = $result['id'];
            $pepper = $_ENV['pepper'];
            $passPepper = $password . $pepper;

            if (!password_verify($passPepper, $result['password'])) {
                $errors[] = 'Contraseña incorrecta.';
            }
        }
    }

    if (empty($errors)) {
        session_regenerate_id(true);
        
        $encryptedCookieValue = encryptCookie($id);
        setcookie('id', $encryptedCookieValue, time() + 3 * 24 * 60 * 60, '/', '', true, true); // 15 days cookie
        $_SESSION['id'] = $id;

        $actualMonth = date('m');
        $actualYear = date('Y');
        $logFileName = "usersLogins-" . $actualMonth . "-" . $actualYear . ".log";
        $logFileDirection = APP_ROOT . 'storage/logs/' . $logFileName;
        $logToRegister = "[" . date('Y-m-d H:i:s') . "] - Email($email) - IP($ip) - UserAgent(" . $_SERVER['HTTP_USER_AGENT'] . ")";
        
        error_log($logToRegister . PHP_EOL, 3, $logFileDirection);

        require_once APP_ROOT . 'src/functions/sendEmail.php';
        $receiverMail = $email;
        $receiverName = $result['user'] . ' ' . $result['lastName'];
        $subject = 'EnderDeploy - Nuevo inicio de sesión detectado';
        $htmlBody = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta http-equiv="X-UA-Compatible" content="IE=Edge"><meta name="x-apple-disable-message-reformatting"><title></title><style>html{-webkit-text-size-adjust:none;-ms-text-size-adjust:none}.only_mob{display:none!important}@media only screen and (max-device-width:600px),only screen and (max-width:600px){.mob_100{width:100%!important;max-width:100%!important}.mob_full{width:auto!important;display:block!important;padding:0 10px!important}.mob_center{text-align:center!important}.mob_center_bl{margin-left:auto;margin-right:auto}.mob_hidden{display:none!important}.only_mob{display:block!important}}@media only screen and (max-width:600px){.mob_100{width:100%!important;max-width:100%!important}.mob_100 img,.mob_100 table{max-width:100%!important}.mob_full{width:auto!important;display:block!important;padding:0 10px!important}.mob_center{text-align:center!important}.mob_center_bl{margin-left:auto;margin-right:auto}.mob_hidden{display:none!important}.only_mob{display:block!important}}.creative{width:100%!important;max-width:100%!important}.mail_preheader{display:none!important}form input,form textarea{font-family:Arial,sans-serif;width:100%;box-sizing:border-box;font-size:13px;color:#000;outline:none;padding:0 15px}form textarea{resize:vertical;line-height:normal;padding:10px 15px}form button{border:0 none;cursor:pointer}</style></head><body class="body" style="padding:0;margin:0"><div class="full-wrap"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="full-wrap"><tbody><tr><td align="center" bgcolor="#f7f7f7" style="line-height: normal; hyphens: none;"><div><div class="mail_preheader" style="font-size: 0px; color: transparent; opacity: 0;"><span style="font-family: Arial, Helvetica, sans-serif; font-size: 0px; color: transparent; line-height: 0px;"></span></div></div><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 700px;"><tbody><tr><td align="center" valign="top" bgcolor="#ffffff" style="padding: 10px;"><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 600px;"><tbody><tr><td align="center" valign="top" bgcolor="#efefef" style="padding: 20px; border-radius: 26px;"><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tbody><tr><td align="center" valign="middle" width="64" style="width: 64px;"><img src="https://rendercores.com/assets/img/logo.png" width="64" height="64" alt="" border="0" style="display: block;"></td><td align="left" valign="middle"><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 145px;"><tbody><tr><td align="left"><div><div style="line-height: 24px;"><span style="font-family: Inter, sans-serif; font-weight: bold; font-size: 20px; color: #000000;">&nbsp;&nbsp;&nbsp;EnderDeploy</span></div></div></td></tr></tbody></table></div></td></tr></tbody></table></div></td></tr></tbody></table></div><div style="height: 20px; line-height: 20px; font-size: 18px;">&nbsp;</div><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 600px;"><tbody><tr><td align="left" valign="middle"><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tbody><tr><td align="left" valign="middle" style="padding: 0px 0px 0px 10px;"><div><div style="line-height: 17px;"><span style="font-family: Inter, sans-serif; font-size: 14px; color: #1a2229;">Esto es un email para informarle de que alguien ha accedido a su cuenta de rendercores.com. En caso de no haber sido usted cambie la contraseña. Estos son los detalles:</span></div></div></td></tr></tbody></table></div></div></td></tr></tbody></table></div><div style="height: 20px; line-height: 20px; font-size: 18px;">&nbsp;</div><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 600px;"><tbody><tr><td><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tbody><tr><td valign="top"><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tbody><tr><td align="left" style="padding: 0px 0px 0px 10px;"><div><div style="line-height: 38px;"><span style="font-family: Inter, sans-serif; color: #1a2229;">Fecha: ' . date('d/m/Y H:i:s'). '<br>Dirección IP: ' . $ip . '<br>UserAgent (Navegador): ' . $_SERVER['HTTP_USER_AGENT'] . '</span></div></div></td></tr></tbody></table></div></div></td></tr></tbody></table></div></div></td></tr></tbody></table></div><div style="height: 20px; line-height: 20px; font-size: 18px;">&nbsp;</div><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 600px;"><tbody><tr><td align="center" valign="top"><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tbody><tr><td align="left" valign="top"><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tbody><tr><td align="left" valign="middle" style="padding: 0px 0px 0px 10px;"><div><div style="line-height: 17px; word-break: break-all;"><span style="font-family: Inter, sans-serif; font-size: 14px; color: #1a2229;"><a href="https://rendercores.com/dashboard/signin" target="_blank">https://rendercores.com/dashboard/signin</a></span></div></div></td></tr></tbody></table></div></div></td></tr></tbody></table></div><div style="height: 16px; line-height: 16px; font-size: 14px;">&nbsp;</div><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tbody><tr><td align="left" valign="middle"><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tbody><tr><td align="left" valign="middle" style="padding: 0px 0px 0px 10px;"><div><div style="line-height: 17px;"><span style="font-family: Poppins, sans-serif; font-size: 14px; color: #1a2229;">Si no te has registrado en este sitio, la cuenta se borrará automaticamente en 1 mes en caso de que nadie haya verificado el email.</span></div></div></td></tr></tbody></table></div></div></td></tr></tbody></table></div></div></td></tr></tbody></table></div></div></td></tr></tbody></table></div><div style="height: 20px; line-height: 20px; font-size: 18px;">&nbsp;</div><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 600px;"><tbody><tr><td align="center" valign="top" bgcolor="#dae5ff" style="padding: 10px; border-radius: 26px;"><div><table width="100%" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td align="left" valign="top" style="font-size: 0px;"><div style="display: inline-block; vertical-align: top; width: 100px;"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;"><tbody><tr><td align="undefined" valign="top" class="outf14" style="font-size: large;"><div><img src="https://rendercores.com/assets/img/logo.png" width="100" alt="" border="0" style="display: block; max-width: 100px; width: 100%;" class="w100px"></div></td></tr></tbody></table></div><div style="display: inline-block; vertical-align: top; width: 100%; max-width: 290px;"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;"><tbody><tr><td align="left" valign="top" class="outf14" style="font-size: large;"><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 290px;"><tbody><tr><td align="left" valign="top" style="padding: 10px;"><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tbody><tr><td align="left" valign="top"><div><div style="line-height: 24px;"><span style="font-family: Inter, sans-serif; font-weight: bold; font-size: 20px; color: #1a2229;">EnderDeploy</span></div><div style="height: 10px; line-height: 10px; font-size: 8px;">&nbsp;</div><div style="line-height: 17px;"><span style="font-family: Inter, sans-serif; font-size: 14px; color: #828282;">Web operada por PSCEDA S.A.S. de C.V. registrado en México</span></div><div style="height: 10px; line-height: 10px; font-size: 8px;">&nbsp;</div><div style="line-height: 17px;"><span style="font-family: Inter, sans-serif; font-size: 14px; color: #828282;"><a href="https://rendercores.com" target="_blank">https://rendercores.com</a></span></div></div></td></tr></tbody></table></div></div></td></tr></tbody></table></div></td></tr></tbody></table></div></td></tr></tbody></table></div></td></tr></tbody></table></div></td></tr></tbody></table></div></body></html>';
        $plainBody = 'Nuevo inicio de sesión detectado en tu cuenta de EnderDeploy, si no te has registrado la cuenta se borrará al de un mes. Datos del inicio de sesión Fecha: ' . date('d/m/Y H:i:s') . ' Dirección IP: ' . $ip . ' UserAgent (Navegador): ' . $_SERVER['HTTP_USER_AGENT'];
            
        sendMail($receiverMail, $receiverName, $subject, $htmlBody, $plainBody);

        header("Location: /dashboard/");
        exit;
    }
}
require_once APP_ROOT . 'src/views/auth/signin.view.php';?>