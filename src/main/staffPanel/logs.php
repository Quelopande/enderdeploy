<?php
if (isset($_SESSION['id'])) {
    require_once APP_ROOT . 'src/classes/GoogleAuthenticator.php';

    $statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $statement->execute(array(':id' => $id));
    $result = $statement->fetch();

    if ($result['role'] != '-1') {
        $roleId = $result['role'];
        $roleStatement = $connection->prepare('SELECT * FROM roles WHERE roleId = :roleId LIMIT 1');
        $roleStatement->execute(array(':roleId' => $roleId));
        $roleResult = $roleStatement->fetch();
        
        if ($roleResult['viewRoles'] == '1') {
            if(isset($_SESSION['totpVerified'])){
                $sessionDuration = 1800;
                if (isset($_SESSION['totpVerifiedTime']) && (time() - $_SESSION['totpVerifiedTime'] > $sessionDuration)){
                    unset($_SESSION['totpVerified']);
                    unset($_SESSION['totpVerifiedTime']);
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $_SESSION['totpVerifiedTime'] = time();
                }
            } else{
                $totpStatement = $connection->prepare('SELECT * FROM usersTotp WHERE id = :id LIMIT 1');
                $totpStatement->execute(array(':id' => $id));
                $totpResult = $totpStatement->fetch();
    
                if ($totpResult) {
                    $ga = new PHPGangsta_GoogleAuthenticator();
                    $errors = '';
    
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
                        $oneCodeInput = trim($_POST['code']);
                        $cypherMethod = 'AES-256-GCM';
                        $encryptedData = base64_decode($totpResult['totpSecret']);
                        $key = hex2bin($totpResult['totpKey']);
                        $iv = hex2bin($totpResult['totpIv']);
                        $tag = hex2bin($totpResult['totpTag']);
                        $secret = openssl_decrypt($encryptedData, $cypherMethod, $key, OPENSSL_RAW_DATA, $iv, $tag);
                        if ($secret === false) {
                            die('Error al descifrar el secreto TOTP.');
                        }
    
                        $checkResult = $ga->verifyCode($secret, $oneCodeInput, 2);
                        if ($checkResult) {
                            $_SESSION['totpVerified'] = true;
                            $_SESSION['totpVerifiedTime'] = time();
                            header("Location: " . $_SERVER['PHP_SELF']);
                            exit;
                        } else {
                            $errors = '<div class="alert alert-danger">Código incorrecto. Inténtalo de nuevo.</div>';
                        }
                    }
                    
                    echo "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='POST'>
                            <input id='code' type='text' maxlength='6' size='6' name='code' placeholder='Código TOTP'/>
                            <input type='submit' value='Verificar'/>
                          </form>";
                    echo $errors;
                    exit;
                } else {
                    exit ("Todos los miembros con permisos de administración de roles deben tener habilitado el 2FA para acceder a esta página.<br><a href='/dashboard'>Volver</a>");
                }
            }
        } else {
            require 'noAccess.php';
            exit;
        }
    }
} else {
    header('Location: ../auth/signin');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enderdeploy | Roles - Staff Panel [Confidencial]</title>
    <link rel="stylesheet" href="/assets/styles/staffPanel/logs.css">
    <link rel="website icon" type="ico" href="/assets/img/logo.ico">
</head>
<body>
    <div class="menu">
        <h2>Staff Panel</h2>
        <div>
            <a href="/staffPanel/tickets">Tickets</a>
            <a href="/staffPanel/services">Servicios</a>
            <a href="/staffPanel/users">Usuarios</a>
        </div>
    </div>
</body>
</html>