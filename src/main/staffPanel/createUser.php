<?php
use SebastianDevs\SimpleAuthenticator;
require_once APP_ROOT . 'src/classes/SimpleAuthenticator.php';
$auth = new SimpleAuthenticator(6, 'SHA1');
if (!isset($_SESSION['id'])) {
    header('Location: ../auth/signin');
    exit;
}

$statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
$statement->execute([':id' => $id]);
$result = $statement->fetch();

if (!$result || $result['role'] == '-1') {
    header("HTTP/1.0 403 Forbidden");
    require_once APP_ROOT . 'src/main/staffPanel/noAccess.php';
    exit();
}

$roleId = $result['role'];
$roleStatement = $connection->prepare('SELECT * FROM roles WHERE roleId = :roleId LIMIT 1');
$roleStatement->execute([':roleId' => $roleId]);
$roleResult = $roleStatement->fetch();

if (!$roleResult || $roleResult['addUser'] != '1') {
    header("HTTP/1.0 403 Forbidden");
    require_once APP_ROOT . 'src/main/staffPanel/noAccess.php';
    exit();
}

if (!isset($_SESSION['totpVerified'])) {
    $totpStatement = $connection->prepare('SELECT * FROM userstotp WHERE userId = :userId LIMIT 1');
    $totpStatement->execute([':userId' => $id]);
    $totpResult = $totpStatement->fetch();

    if (!$totpResult) {
        exit("Todos los administradores de roles deben tener 2FA habilitado.<br><a href='/dashboard'>Volver</a>");
    }
    
    $errors[] = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
        $oneCodeInput = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_NUMBER_INT);
        $cypherMethod = 'AES-256-GCM';
        $encryptedData = base64_decode($totpResult['totpSecret']);
        $key = hex2bin($totpResult['totpKey']);
        $iv = hex2bin($totpResult['totpIv']);
        $tag = hex2bin($totpResult['totpTag']);
        $secret = openssl_decrypt($encryptedData, $cypherMethod, $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($secret === false) {
            die('Error al descifrar el secreto TOTP.');
        }

        $checkResult = $auth->verifyCode($secret, $oneCodeInput, 2);
        if ($checkResult) {
            session_regenerate_id(true);
            $_SESSION['totpVerified'] = true;
            $_SESSION['totpVerifiedTime'] = time();
            header("Location: /staffPanel/createUser");
            exit;
        } else {
            $errors = '<div class="alert alert-danger">Código incorrecto. Inténtalo de nuevo.</div>';
        }
    }

    echo "<form action='" . htmlspecialchars('/staffPanel/createUser') . "' method='POST'>
        <input id='code' type='text' maxlength='6' size='6' name='code' placeholder='Código TOTP' autocomplete='one-time-code' required autofocus/>
        <input type='submit' value='Verificar'/>
      </form>";

    if (!empty($errors)) {
        echo "<div class='error'>" . htmlspecialchars($errors) . "</div>";
    }
    exit();
}

$sessionDuration = 1800;
if (isset($_SESSION['totpVerifiedTime']) && (time() - $_SESSION['totpVerifiedTime'] > $sessionDuration)) {
    unset($_SESSION['totpVerified']);
    unset($_SESSION['totpVerifiedTime']);
    header("Location: /staffPanel/roles");
    exit;
} else {
    $_SESSION['totpVerifiedTime'] = time();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createUserForm'])) {
        $user = trim(filter_input(INPUT_POST, 'user', FILTER_SANITIZE_SPECIAL_CHARS));
        $lastName = trim(filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_SPECIAL_CHARS));
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)); 
        $password = trim(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW));
        // Optional fields
        $secondName = trim(filter_input(INPUT_POST, 'secondName', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $secondLastName = trim(filter_input(INPUT_POST, 'secondLastName', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $organization = trim(filter_input(INPUT_POST, 'organization', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $country = trim(filter_input(INPUT_POST, 'country', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $state = trim(filter_input(INPUT_POST, 'state', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $city = trim(filter_input(INPUT_POST, 'city', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $domicile = trim(filter_input(INPUT_POST, 'domicile', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        $zipCode = trim(filter_input(INPUT_POST, 'zipCode', FILTER_SANITIZE_ADD_SLASHES) ?? '');

        $errors = [];

        if (empty($user)) {
            $errors[] = "El campo Nombre es obligatorio.";
        }
        if (empty($lastName)) {
            $errors[] = "El campo Apellido es obligatorio.";
        }
        if (!$email) {
            $errors[] = "El email no es válido.";
        }
        if (empty($password)) {
            $errors[] = "La contraseña es obligatoria.";
        } elseif (strlen($password) < 8) {
            $errors[] = "La contraseña debe tener al menos 8 caracteres.";
        }

        if (empty($errors)) {
            $emailCheck = $connection->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $emailCheck->execute([':email' => $email]);
            if ($emailCheck->fetch()) {
                 $errors[] = "El email '$email' ya está en uso.";
            }
        }

        if (empty($errors)) {
            try {
                $connection->beginTransaction();
                $pepper = $_ENV['pepper'];
                $passwordWithPepper = $password . $pepper; 
                $hash = password_hash($passwordWithPepper, PASSWORD_DEFAULT, ['cost' => 12]);
                $status = "notverified"; 
                $role = "-1";
                $stripeCustomerId = 'notset';
                
                $statement = $connection->prepare('INSERT INTO users (id, email, password, status, role, stripeCustomerId) VALUES (NULL, :email, :password, :status, :role, :stripeCustomerId)');
                $statement->execute(array(
                    ':email' => $email,
                    ':password' => $hash,
                    ':status' => $status,
                    ':role' => $role,
                    ':stripeCustomerId' => $stripeCustomerId
                ));
                $newUserId = $connection->lastInsertId();

                \Stripe\Stripe::setApiKey($_ENV['stripeSecret']);
                $customer = \Stripe\Customer::create([
                    'name' => (string)$newUserId,
                    'email' => $email,
                    'metadata' => ['userId' => $newUserId, 'userEmail' => $email],
                ]);
                $stripeCustomerId = $customer->id;
                
                $userStatement = $connection->prepare('UPDATE users SET stripeCustomerId = :stripeCustomerId WHERE id = :userId');
                $userStatement->execute([':stripeCustomerId' => $stripeCustomerId, ':userId' => $newUserId]);

                $userLocationstatement = $connection->prepare('INSERT INTO userslocation (userId) VALUES (:userId)');
                $userLocationstatement->execute([':userId' => $newUserId]);

                $usersCodeStatement = $connection->prepare('INSERT INTO userscode (userId) VALUES (:userId)');
                $usersCodeStatement->execute([':userId' => $newUserId]);

                $usersRecoveryStatement = $connection->prepare('INSERT INTO userrecovery (userId) VALUES (:userId)');
                $usersRecoveryStatement->execute([':userId' => $newUserId]);

                $connection->commit();

                $successMessage = '¡Usuario creado con éxito! Los datos han sido validados y la contraseña cifrada.';
                $errors = [];                
            } catch (PDOException $e) {
                if ($connection->inTransaction()) $connection->rollBack();
                error_log("CREATE_USER (1): Database error: " . $e->getMessage());
                $errors[] = 'Ocurrió un error en la base de datos al crear la cuenta. Inténtalo de nuevo.';
            } catch (\Stripe\Exception\ApiErrorException $e) {
                if ($connection->inTransaction()) $connection->rollBack();
                error_log("CREATE_USER (2): Stripe API error: " . $e->getMessage());
                $errors[] = 'Ocurrió un error con el servicio de pago (Stripe). Por favor, inténtalo de nuevo.';
            } catch (Exception $e) {
                if ($connection->inTransaction()) $connection->rollBack();
                error_log("CREATE_USER (3): General error: " . $e->getMessage());
                $errors[] = 'Ocurrió un error inesperado al crear la cuenta. Inténtalo de nuevo.';
            }
        }
    }

    require_once APP_ROOT . 'src/views/staffPanel/createUser.view.php';
}
?>