<?php
$statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
$statement->execute(array(':id' => $id));
$result = $statement->fetch();

$errors = [];

$estatement = $connection->prepare('SELECT * FROM usersCode WHERE userId = :userId LIMIT 1');
$estatement->execute(array(':userId' => $id));
$eresult = $estatement->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verifationCodeSubmit'])) {
    $submittedVerificationCode = trim($_POST['verificationCode']);
    if (empty($submittedVerificationCode)) {
        $errors[] = 'Por favor, introduce el código de verificación.';
    } 
    if (strlen($submittedVerificationCode) !== 6) {
        $errors[] = 'El código de verificación tiene una longitud incorrecta.';
    }
    if (!ctype_digit($submittedVerificationCode)) {
        $errors[] = 'El código de verificación contiene caracteres inválidos.';
    }

    if (empty($errors)) {
        if ($eresult && hash_equals((string)$eresult['verificationCode'], $submittedVerificationCode)) {
            if (strtotime($eresult['verificationCodeDate']) < strtotime('-1 hour')) {
                $errors[] = 'El código de verificación ha expirado ya que solo era válido por 1 hora. Por favor, solicita un nuevo código.';
            } else {
                try {
                    $connection->beginTransaction();
                    $statement = $connection->prepare('UPDATE users SET status = :status WHERE id = :id');
                    $statement->execute(array(
                        ':status' => "verified",
                        ':id' => $id,
                    ));

                    $estatement = $connection->prepare('UPDATE usersCode SET verificationCode = :verificationCode, verificationCodeDate = :verificationCodeDate, lastUserVerification = :lastUserVerification WHERE userId = :userId');
                    $estatement->execute(array(
                        ':verificationCode' => NULL,
                        ':userId' => $id,
                        ':verificationCodeDate' => NULL,
                        ':lastUserVerification' => date('Y-m-d H:i:s'),
                    ));

                    $connection->commit();
                    header('Location: /dashboard');
                    exit();
                } catch (PDOException $e) {
                    $connection->rollBack();
                    error_log("VERIFICATION_ERROR: Database error: " . $e->getMessage());
                    $errors[] = 'Ocurrió un error en la base de datos. Inténtalo de nuevo.';
                }
            }
        } else {
            $errors[] = 'El código de verificación no es correcto.';
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resendVerificationCode'])) {
    if (strtotime($eresult['verificationCodeDate']) > strtotime('-15 minutes')) {
        $errors[] = 'Ya te hemos enviado el código. Espera 15 minutos para solicitar un nuevo código.';
    } else{
        $verificationCode = strval(random_int(100000, 999999));
        try {
            $statement = $connection->prepare('INSERT INTO usersCode (userId, verificationCode, verificationCodeDate) VALUES (:userId, :verificationCode, :verificationCodeDate) ON DUPLICATE KEY UPDATE verificationCode = VALUES(verificationCode), verificationCodeDate = VALUES(verificationCodeDate);');
            $statement->execute(array(
                ':verificationCode' => $verificationCode,
                ':userId' => $id,
                ':verificationCodeDate' => date('Y-m-d H:i:s')
            ));
            require_once APP_ROOT . 'src/functions/sendEmail.php';
            $receiverMail = $result['email'];
            $receiverName = $result['user'] . ' ' . $result['lastName'];
            $subject = 'EnderDeploy - Código de verificación';
            $htmlBody = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta http-equiv="X-UA-Compatible" content="IE=Edge"/><meta name="x-apple-disable-message-reformatting"><title></title><style>html{-webkit-text-size-adjust:none;-ms-text-size-adjust:none}.only_mob{display:none!important}@media only screen and (max-device-width:600px),only screen and (max-width:600px){.mob_100{width:100%!important;max-width:100%!important}.mob_full{width:auto!important;display:block!important;padding:0 10px!important}.mob_center{text-align:center!important}.mob_center_bl{margin-left:auto;margin-right:auto}.mob_hidden{display:none!important}.only_mob{display:block!important}}@media only screen and (max-width:600px){.mob_100{width:100%!important;max-width:100%!important}.mob_100 img,.mob_100 table{max-width:100%!important}.mob_full{width:auto!important;display:block!important;padding:0 10px!important}.mob_center{text-align:center!important}.mob_center_bl{margin-left:auto;margin-right:auto}.mob_hidden{display:none!important}.only_mob{display:block!important}}.creative{width:100%!important;max-width:100%!important}.mail_preheader{display:none!important}form input,form textarea{font-family:Arial,sans-serif;width:100%;box-sizing:border-box;font-size:13px;color:#000;outline:none;padding:0 15px}form textarea{resize:vertical;line-height:normal;padding:10px 15px}form button{border:0 none;cursor:pointer}</style></head><body class="body" style="padding:0;margin:0"><div class="full-wrap"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="full-wrap"><tr><td align="center" bgcolor="#f7f7f7" style="line-height: normal; hyphens: none;"><div><div class="mail_preheader" style="font-size: 0px; color: transparent; opacity: 0;"><span style="font-family: Arial, Helvetica, sans-serif; font-size: 0px; color: transparent; line-height: 0px;"></span></div></div><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 700px;"><tr><td align="center" valign="top" bgcolor="#ffffff" style="padding: 10px;"><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 600px;"><tr><td align="center" valign="top" bgcolor="#efefef" style="padding: 20px; border-radius: 26px;"><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td align="center" valign="middle" width="64" style="width: 64px;"><img src="https://rendercores.com/assets/img/logo.png" width="64" height="64" alt="" border="0" style="display: block;"></td><td align="left" valign="middle"><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 145px;"><tr><td align="left"><div><div style="line-height: 24px;"><span style="font-family: Inter, sans-serif; font-weight: bold; font-size: 20px; color: #000000;">&nbsp;&nbsp;&nbsp;EnderDeploy</span></div></div></td></tr></table></div></td></tr></table></div></td></tr></table></div><div style="height: 20px; line-height: 20px; font-size: 18px;">&nbsp;</div><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 600px;"><tr><td align="left" valign="middle"><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td align="left" valign="middle" style="padding: 0px 0px 0px 10px;"><div><div style="line-height: 17px;"><span style="font-family: Inter, sans-serif; font-size: 14px; color: #1a2229;">Esto es un email de verificación, tienes que introducir el código de verificación en la página de verificación</span></div></div></td></tr></table></div></div></td></tr></table></div><div style="height: 20px; line-height: 20px; font-size: 18px;">&nbsp;</div><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 600px;"><tr><td align="center" valign="top" bgcolor="#efefef" style="padding: 20px; border-radius: 26px; border-width: 1px; border-color: #9f9f9f; border-style: solid;"><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td align="center" valign="top" style="padding: 0px 0px 1px;"><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 121px;"><tr><td align="left"><div><div style="line-height: 38px;"><span style="font-family: Inter, sans-serif; font-weight: bold; font-size: 32px; color: #1a2229;">' . $verificationCode . '</span></div></div></td></tr></table></div></div></td></tr></table></div></div></td></tr></table></div><div style="height: 20px; line-height: 20px; font-size: 18px;">&nbsp;</div><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 600px;"><tr><td align="center" valign="top"><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td align="left" valign="top"><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td align="left" valign="middle" style="padding: 0px 0px 0px 10px;"><div><div style="line-height: 17px; word-break: break-all;"><span style="font-family: Inter, sans-serif; font-size: 14px; color: #1a2229;"><a href="https://rendercores.com/dashboard/verify" target="_blank">https://rendercores.com/dashboard/verify</a></span></div></div></td></tr></table></div></div></td></tr></table></div><div style="height: 16px; line-height: 16px; font-size: 14px;">&nbsp;</div><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td align="left" valign="middle"><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td align="left" valign="middle" style="padding: 0px 0px 0px 10px;"><div><div style="line-height: 17px;"><span style="font-family: Poppins, sans-serif; font-size: 14px; color: #1a2229;">Si no te has registrado en este sitio, la cuenta se borrará automaticamente en 1 mes en caso</span></div></div></td></tr></table></div></div></td></tr></table></div></div></td></tr></table></div></div></td></tr></table></div><div style="height: 20px; line-height: 20px; font-size: 18px;">&nbsp;</div><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 600px;"><tr><td align="center" valign="top" bgcolor="#dae5ff" style="padding: 10px; border-radius: 26px;"><div><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td align="left" valign="top" style="font-size: 0px;"><div style="display: inline-block; vertical-align: top; width: 100px;"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;"><tr><td align="undefined" valign="top" class="outf14" style="font-size: large;"><div><img src="https://rendercores.com/assets/img/logo.png" width="100" alt="" border="0" style="display: block; max-width: 100px; width: 100%;" class="w100px"></div></td></tr></table></div><div style="display: inline-block; vertical-align: top; width: 100%; max-width: 290px;"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;"><tr><td align="left" valign="top" class="outf14" style="font-size: large;"><div><table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 290px;"><tr><td align="left" valign="top" style="padding: 10px;"><div><div><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td align="left" valign="top"><div><div style="line-height: 24px;"><span style="font-family: Inter, sans-serif; font-weight: bold; font-size: 20px; color: #1a2229;">EnderDeploy</span></div><div style="height: 10px; line-height: 10px; font-size: 8px;">&nbsp;</div><div style="line-height: 17px;"><span style="font-family: Inter, sans-serif; font-size: 14px; color: #828282;">Web operada por RenderCores S.A.S de C.V registrado en México</span></div><div style="height: 10px; line-height: 10px; font-size: 8px;">&nbsp;</div><div style="line-height: 17px;"><span style="font-family: Inter, sans-serif; font-size: 14px; color: #828282;"><a href="https://rendercores.com" target="_blank">https://rendercores.com</a></span></div></div></td></tr></table></div></div></td></tr></table></div></td></tr></table></div></td></tr></table></div></td></tr></table></div></td></tr></table></div></body></html>';
            $plainBody = 'Este es un email de verificación, tienes que introducir el código de verificación en la página de verificación.\n\nCódigo de verificación: ' . $verificationCode . '\n\nhttps://rendercores.com/dashboard/verify\n\nSi no te has registrado en este sitio, la cuenta se borrará automáticamente en 1 mes en caso.';
            
            sendMail($receiverMail, $receiverName, $subject, $htmlBody, $plainBody);
            
            header('Location: /dashboard/verify');
            exit();
        } catch (PDOException $e) {
            error_log("RESEND_CODE_ERROR: Database error: " . $e->getMessage());
            $errors[] = 'Ocurrió un error en la base de datos al enviar el nuevo código.';
        }
    }
}
if (isset($_SESSION['id'])) {
    if ($result['status'] === 'verified') {
        header('Location: /dashboard');
        exit();
    } else {
        require_once APP_ROOT . 'src/views/dashboard/verify.view.php';
    }
} else {
    header('Location: ../auth/signin');
    exit();
}