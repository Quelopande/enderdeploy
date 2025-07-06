<?php
use SebastianDevs\SimpleAuthenticator;
require_once APP_ROOT . 'src/classes/SimpleAuthenticator.php';
$auth = new SimpleAuthenticator(6, 'SHA1');

$statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
$statement->execute(array(':id' => $id));
$result = $statement->fetch();
$id = $result['id'];
$userEmail = $result['email'];
$secret = $auth->createSecret();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    function checksimilarity($oldData, $newData) {
        return $oldData === $newData;
    }
    if (isset($_POST["firstInfoSubmit"])) {
        $fields = [
            'user' => htmlspecialchars(strtolower($_POST['name']), ENT_QUOTES, 'UTF-8'),
            'secondName' => htmlspecialchars(strtolower($_POST['secondName']), ENT_QUOTES, 'UTF-8'),
            'lastName' => htmlspecialchars(strtolower($_POST['lastName']), ENT_QUOTES, 'UTF-8'),
            'secondLastName' => htmlspecialchars(strtolower($_POST['secondLastName']), ENT_QUOTES, 'UTF-8'),
            'email' => htmlspecialchars(strtolower($_POST['email']), FILTER_SANITIZE_EMAIL)
        ];
    
        if (!empty($_POST['name']) && !empty($_POST['lastName']) && !empty($_POST['email'])) {
            $query = $connection->prepare('SELECT * FROM users WHERE email = :email AND id != :id');
            $query->execute([':email' => $fields['email'],':id' => $id,]);
            $eresult = $query->fetch();
            if ($eresult) {
                $userErrors = '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">El email ya existe en la base de datos.</p>';
            } else {
                $query = $connection->prepare('SELECT * FROM users WHERE id = :id');
                $query->execute([':id' => $id]);
                $result = $query->fetch();
                if ($result) {
                    $allowedFields = ['user', 'secondName', 'lastName', 'secondLastName', 'email'];
                    foreach ($fields as $field => $postField) {
                        if (in_array($field, $allowedFields) && !checkSimilarity($postField, $result[$field])) {
                            $statement = $connection->prepare("UPDATE users SET $field = :$field WHERE id = :id");
                            $statement->execute([
                                ':id' => $id,
                                ":$field" => $postField,
                            ]);
                        }
                    }
                    header("Location: settings");
                } else {
                    $userErrors = '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Usuario no encontrado.</p>';
                }
            }
        } else {
            $userErrors = '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Rellena todos los campos.</p>';
        }      
    }
    if (isset($_POST["secondInfoSubmit"])) {
        $fields = [
            'organization' => htmlspecialchars(strtolower($_POST['organization']), ENT_QUOTES, 'UTF-8'),
            'country' => htmlspecialchars(strtolower($_POST['country']), ENT_QUOTES, 'UTF-8'),
            'state' => htmlspecialchars(strtolower($_POST['state']), ENT_QUOTES, 'UTF-8'),
            'zipCode' => htmlspecialchars(strtolower($_POST['zipCode']), ENT_QUOTES, 'UTF-8'),
            'city' => htmlspecialchars(strtolower($_POST['city']), ENT_QUOTES, 'UTF-8'),
            'domicile' => htmlspecialchars(strtolower($_POST['domicile']), ENT_QUOTES, 'UTF-8')
        ];
        $requiredFields = ['country', 'state', 'zipCode', 'city', 'domicile'];
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $missingFields[] = $field;
            }
        }
        if (!empty($missingFields)) {
            $secondUsersErrors = '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Rellena todos los campos. Faltan: ' . implode(', ', $missingFields) . '.</p>';
        } else {
            $allowedFields = ['organization', 'country', 'state', 'zipCode', 'city', 'domicile'];
            foreach ($fields as $field => $postField) {
                if (in_array($field, $allowedFields) && !checkSimilarity($postField, $result[$field])) {
                    $statement = $connection->prepare("UPDATE usersLocation SET $field = :$field WHERE userId = :userId");
                    $statement->execute([
                        ':userId' => $id,
                        ":$field" => $postField,
                    ]);
                }
            }
            header("Location: settings");
        }       
    }
    if (isset($_POST["securitySubmit"])) {
        $passErrors = "";
        $actualPassword = trim($_POST['actualPassword']);
        $newPassword = trim($_POST['newPassword']);
        $newPassword2 = trim($_POST['newPassword2']);
        $password = $result['password'];
        $pepper = $_ENV['pepper'];
        
        if (empty($actualPassword) || empty($newPassword) || empty($newPassword2)) {
            $passErrors .= '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Rellena todos los campos.</p>';
        } else {
            if (!password_verify($actualPassword . $pepper . $result['salt'], $password)) {
                $passErrors .= '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Rellena el campo de "Contraseña Actual" con la contraseña que tienes establecida actualmente en la cuenta.</p>';
            } elseif ($actualPassword === $newPassword) {
                $passErrors .= '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">La nueva contraseña no debe ser la misma.</p>';
            } elseif ($newPassword !== $newPassword2) {
                $passErrors .= '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Los campos "Nueva contraseña" y "Nueva contraseña 2" deben de tener los mismos datos.</p>';
            } else {
                $newPassPepper = $newPassword . $pepper . $result['salt'];
                $hash = password_hash($newPassPepper, PASSWORD_BCRYPT, ['cost' => 12]);
    
                $statement = $connection->prepare('UPDATE users SET password = :password WHERE id = :id');
                $statement->execute([
                    ':id' => $id,
                    ':password' => $hash,
                ]);
                $passErrors = "<p style='border:solid 1px green;background: #00b12673;color: #002b00;padding:10px;border-radius:20px;display:block;'>Se ha cambiado la contraseña correctamente.</p>";
            }
        }
    }
    if (isset($_POST["codeTotpSubmit"])) {
        $secret = $_SESSION['tempTotpSecret'];
        $cypherMethod = 'AES-256-GCM';
        $key = random_bytes(32);
        $iv = random_bytes(12);
        $encryptedSecret = openssl_encrypt($secret, $cypherMethod, $key, OPENSSL_RAW_DATA, $iv, $tag);

        if ($encryptedSecret === false) {
            error_log("Error during encryption: " . openssl_error_string());
            die("ERROR AL ENCRIPTAR CLAVE. POR FAVOR CONTACTE CON SOPORTE.");
        } else {
            $finalEncryptedString = base64_encode($encryptedSecret);
            $hexKey = bin2hex($key);
            $hexIv = bin2hex($iv);
            $hexTag = bin2hex($tag);

            if ($auth->verifyCode($secret, $_POST['totpCode'], 2)) {
                $statement = $connection->prepare('INSERT INTO usersTotp (userId, totpSecret, totpKey, totpIv, totpTag) VALUES (:userId, :totpSecret, :totpKey, :totpIv, :totpTag) ON DUPLICATE KEY UPDATE totpSecret = VALUES(totpSecret), totpKey = VALUES(totpKey), totpIv = VALUES(totpIv), totpTag = VALUES(totpTag)');
                if ($statement->execute([
                    ':userId' => $id,
                    ':totpSecret' => $finalEncryptedString,
                    ':totpKey' => $hexKey,
                    ':totpIv' => $hexIv,
                    ':totpTag' => $hexTag
                ])) {
                unset($_SESSION['tempTotpSecret']);
                echo "<script>alert('Código correcto. TOTP establecido correctamente.');</script>";
                } else {
                    error_log("Database error: " . print_r($statement->errorInfo(), true));
                    echo "Error en la base de datos.";
                }
            } else {
                unset($_SESSION['tempTotpSecret']);
                echo "<script>alert('Código incorrecto. TOTP NO establecido.');</script>";
            }
        }
    }
}

$statementTotp = $connection->prepare('SELECT * FROM usersTotp WHERE userId = :userId LIMIT 1');
$statementTotp->execute(array(':userId' => $id));
$resultTotp = $statementTotp->fetch();
if (!$resultTotp) {
    if (!isset($_SESSION['tempTotpSecret'])) {
        $secret = $auth->createSecret();
        $_SESSION['tempTotpSecret'] = $secret;
    } else {
        $secret = $_SESSION['tempTotpSecret'];
    }
    $qrCodeUrl = $auth->getQRCodeGoogleUrl($secret, "EnderDeploy.space: $userEmail");
} else {
    unset($_SESSION['tempTotpSecret']);
    $qrCodeUrl = "/assets/img/logo.png";
}

if (isset($_SESSION['id'])){
    if($result['status'] === 'verified'){
        require_once APP_ROOT . 'src/views/dashboard/settings.view.php';
    } else{
        require_once APP_ROOT . 'src/views/dashboard/notVerified.view.php';
    }
} else if (!isset($_SESSION['id'])){
    header('Location: ../auth/signin');
} else {
    require_once APP_ROOT . 'src/main/auth/ban.php';
}