<?php
use SebastianDevs\SimpleAuthenticator;
require_once APP_ROOT . 'src/classes/SimpleAuthenticator.php';
$auth = new SimpleAuthenticator(6, 'SHA1');

$statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
$statement->execute(array(':id' => $id));
$result = $statement->fetch(PDO::FETCH_ASSOC);
$id = $result['id'];
$userEmail = $result['email'];
$stripeCustomerId = $result['stripeCustomerId'] ?? null;
$secret = $auth->createSecret();

$userErrors = '';
$secondUsersErrors = '';
$passErrors = '';


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
            try {
                $query = $connection->prepare('SELECT * FROM users WHERE email = :email AND id != :id');
                $query->execute([':email' => $fields['email'],':id' => $id,]);
                $eresult = $query->fetch();
                
                if ($eresult) {
                    $userErrors = '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">El email ya existe en la base de datos.</p>';
                } else {
                    $query = $connection->prepare('SELECT * FROM users WHERE id = :id');
                    $query->execute([':id' => $id]);
                    $result = $query->fetch(PDO::FETCH_ASSOC);
                    
                    if ($result) {
                        $connection->beginTransaction();
                        $allowedFields = ['user', 'secondName', 'lastName', 'secondLastName', 'email'];
                        $changesMade = false;
                        
                        foreach ($fields as $field => $postField) {
                            if (in_array($field, $allowedFields) && !checkSimilarity($postField, $result[$field])) {
                                $statement = $connection->prepare("UPDATE users SET $field = :$field WHERE id = :id");
                                $statement->execute([
                                    ':id' => $id,
                                    ":$field" => $postField,
                                ]);
                                $changesMade = true;
                            }
                        }
                        
                        $currentStripeCustomerId = $result['stripeCustomerId'] ?? null;
                        
                        if ($changesMade && $currentStripeCustomerId) {
                            \Stripe\Stripe::setApiKey($_ENV['stripeSecret']);
                            $stripe = new \Stripe\StripeClient($_ENV['stripeSecret']);
                            
                            $customer = $stripe->customers->update(
                                $currentStripeCustomerId,
                                ['email' => $fields['email'], 'metadata' => ['userEmail' => $fields['email']]]
                            );
                        }

                        $connection->commit();
                        header("Location: settings");
                        exit;
                    } else {
                        $userErrors = '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Usuario no encontrado.</p>';
                    }
                }
            } catch (\PDOException $e) {
                if ($connection->inTransaction()) {
                    $connection->rollBack();
                }
                error_log("Database Error (firstInfoSubmit): " . $e->getMessage());
                $userErrors = '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Error en la base de datos al actualizar la información.</p>';
            } catch (\Stripe\Exception\ApiErrorException $e) {
                if ($connection->inTransaction()) {
                    $connection->rollBack();
                }
                error_log("Stripe Error (firstInfoSubmit): " . $e->getMessage());
                $userErrors = '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Error con el servicio de pago (Stripe) al actualizar el email.</p>';
            } catch (\Exception $e) {
                if ($connection->inTransaction()) {
                    $connection->rollBack();
                }
                error_log("General Error (firstInfoSubmit): " . $e->getMessage());
                $userErrors = '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Ocurrió un error inesperado. Intente de nuevo.</p>';
            }
        } else {
            $userErrors = '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Rellena todos los campos obligatorios: Nombre, apellido y email.</p>';
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
            try {
                $connection->beginTransaction();
                $allowedFields = ['organization', 'country', 'state', 'zipCode', 'city', 'domicile'];
                
                $queryLocation = $connection->prepare('SELECT * FROM userslocation WHERE userId = :userId LIMIT 1');
                $queryLocation->execute([':userId' => $id]);
                $locationResult = $queryLocation->fetch(PDO::FETCH_ASSOC);

                foreach ($fields as $field => $postField) {
                    if (in_array($field, $allowedFields) && (!isset($locationResult[$field]) || !checkSimilarity($postField, $locationResult[$field]))) {
                        $statement = $connection->prepare("UPDATE userslocation SET $field = :$field WHERE userId = :userId");
                        $statement->execute([
                            ':userId' => $id,
                            ":$field" => $postField,
                        ]);
                    }
                }
                $connection->commit();
                header("Location: settings");
                exit;
            } catch (\PDOException $e) {
                if ($connection->inTransaction()) {
                    $connection->rollBack();
                }
                error_log("Database Error (secondInfoSubmit): " . $e->getMessage());
                $secondUsersErrors = '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Error en la base de datos al actualizar la ubicación.</p>';
            } catch (\Exception $e) {
                error_log("General Error (secondInfoSubmit): " . $e->getMessage());
                $secondUsersErrors = '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Ocurrió un error inesperado. Intente de nuevo.</p>';
            }
        }
    }
    
    if (isset($_POST["securitySubmit"])) {
        $actualPassword = trim(filter_input(INPUT_POST, 'actualPassword', FILTER_UNSAFE_RAW));
        $newPassword = trim(filter_input(INPUT_POST, 'newPassword', FILTER_UNSAFE_RAW));
        $newPassword2 = trim(filter_input(INPUT_POST, 'newPassword2', FILTER_UNSAFE_RAW));
        $password = $result['password'];
        $pepper = $_ENV['pepper'];
        
        try {
            if (empty($actualPassword) || empty($newPassword) || empty($newPassword2)) {
                $passErrors .= '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Rellena todos los campos.</p>';
            } else {
                if (!password_verify($actualPassword . $pepper, $password)) {
                    $passErrors .= '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">La contraseña actual no es correcta.</p>';
                } elseif ($actualPassword === $newPassword) {
                    $passErrors .= '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">La nueva contraseña no debe ser la misma.</p>';
                } elseif ($newPassword !== $newPassword2) {
                    $passErrors .= '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Los campos "Nueva contraseña" y "Nueva contraseña 2" deben de tener los mismos datos.</p>';
                } else {
                    $newPassPepper = $newPassword . $pepper;
                    $hash = password_hash($newPassPepper, PASSWORD_DEFAULT, ['cost' => 12]);
        
                    $statement = $connection->prepare('UPDATE users SET password = :password WHERE id = :id');
                    if ($statement->execute([':id' => $id, ':password' => $hash])) {
                        $passErrors = "<p style='border:solid 1px green;background: #00b12673;color: #002b00;padding:10px;border-radius:20px;display:block;'>Se ha cambiado la contraseña correctamente.</p>";
                    } else {
                         throw new \PDOException("Error al ejecutar la actualización de la contraseña.");
                    }
                }
            }
        } catch (\PDOException $e) {
            error_log("Database Error (securitySubmit): " . $e->getMessage());
            $passErrors = '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Error en la base de datos al cambiar la contraseña.</p>';
        } catch (\Exception $e) {
            error_log("General Error (securitySubmit): " . $e->getMessage());
            $passErrors = '<p style="border: solid 1px #8d0000;background: #ad00005c;color: #8d0000;padding:10px;border-radius:20px;display:block;">Ocurrió un error inesperado al cambiar la contraseña.</p>';
        }
    }
    
    if (isset($_POST["codeTotpSubmit"])) {
        $secret = $_SESSION['tempTotpSecret'] ?? null;
        $totpCode = $_POST['totpCode'] ?? '';
        
        if (empty($secret)) {
            echo "<script>alert('Error de sesión: La clave secreta de TOTP no está disponible.');</script>";
            die();
        }

        if (empty($totpCode)) {
            echo "<script>alert('Por favor, introduce el código TOTP.');</script>";
            unset($_SESSION['tempTotpSecret']);
            die();
        }
        
        try {
            $cypherMethod = 'AES-256-GCM';
            $key = random_bytes(32);
            $iv = random_bytes(12);
            $tag = '';
            
            $encryptedSecret = openssl_encrypt($secret, $cypherMethod, $key, OPENSSL_RAW_DATA, $iv, $tag);
            
            if ($encryptedSecret === false) {
                error_log("Error during TOTP encryption: " . openssl_error_string());
                die("ERROR AL ENCRIPTAR CLAVE. POR FAVOR CONTACTE CON SOPORTE.");
            } else {
                $finalEncryptedString = base64_encode($encryptedSecret);
                $hexKey = bin2hex($key);
                $hexIv = bin2hex($iv);
                $hexTag = bin2hex($tag);

                if ($auth->verifyCode($secret, $totpCode, 2)) {
                    $statement = $connection->prepare('INSERT INTO userstotp (userId, totpSecret, totpKey, totpIv, totpTag) VALUES (:userId, :totpSecret, :totpKey, :totpIv, :totpTag) ON DUPLICATE KEY UPDATE totpSecret = VALUES(totpSecret), totpKey = VALUES(totpKey), totpIv = VALUES(totpIv), totpTag = VALUES(totpTag)');
                    
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
                        throw new \PDOException("Error al ejecutar la inserción/actualización de TOTP.");
                    }
                } else {
                    unset($_SESSION['tempTotpSecret']);
                    echo "<script>alert('Código incorrecto. TOTP NO establecido.');</script>";
                }
            }
        } catch (\PDOException $e) {
            error_log("Database Error (codeTotpSubmit): " . $e->getMessage());
            echo "<script>alert('Error en la base de datos al establecer TOTP.');</script>";
        } catch (\Exception $e) {
            error_log("General Error (codeTotpSubmit): " . $e->getMessage());
            echo "<script>alert('Ocurrió un error inesperado al establecer TOTP.');</script>";
        }
    }
}

$statementTotp = $connection->prepare('SELECT * FROM userstotp WHERE userId = :userId LIMIT 1');
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
    exit;
} else {
    require_once APP_ROOT . 'src/main/auth/ban.php';
}