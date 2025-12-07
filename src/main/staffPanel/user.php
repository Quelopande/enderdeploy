<?php
$viewSubscriptions = '';
$statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
$statement->execute(array(':id' => $id));
$result = $statement->fetch();

if ($result['role'] != '-1') {
    $roleId = $result['role'];
    $roleStatement = $connection->prepare('SELECT * FROM roles WHERE roleId = :roleId LIMIT 1');
    $roleStatement->execute(array(':roleId' => $roleId));
    $roleResult = $roleStatement->fetch();

    $userResult = null; 
    $userLocationResult = null;
    $errors = [];

    if ($roleResult['viewUser'] == '1') {
        function getUserData($connection, $roleResult, $obteinedUserData, $dataType, $returnOnlyData = false){
            global $viewSubscriptions, $userResult, $userLocationResult;
            
            if ($dataType === "idType"){
                $userStatement = $connection->prepare('SELECT * FROM users WHERE id = :userId LIMIT 1');
                $userStatement->execute(array(':userId' => $obteinedUserData));
            } elseif ($dataType === "emailType") {
                $userStatement = $connection->prepare('SELECT * FROM users WHERE email = :userEmail LIMIT 1');
                $userStatement->execute(array(':userEmail' => $obteinedUserData));
            }
            $userResult = $userStatement->fetch(PDO::FETCH_ASSOC);

            if($userResult){
                $userLocationStatement = $connection->prepare('SELECT * FROM userslocation WHERE userId = :userId LIMIT 1');
                $userLocationStatement->execute(array(':userId' => $userResult['id']));
                $userLocationResult = $userLocationStatement->fetch(PDO::FETCH_ASSOC);
                
                if ($roleResult['viewSubscriptionData'] == '1') {
                    $viewSubscriptions = "<a href='/staffPanel/subscriptions?userId=" . htmlspecialchars($userResult['id']) ."'>Ver Servicios</a>";
                }
                
                if ($returnOnlyData) {
                    return ['user' => $userResult, 'location' => $userLocationResult];
                }

                if ($userResult['role'] != '-1') {
                    header("HTTP/1.0 403 Forbidden");
                    require_once APP_ROOT . 'src/main/staffPanel/noAccess.php';
                    exit();
                } else {
                    require_once APP_ROOT . 'src/views/staffPanel/user.view.php';
                    return $userResult['id'];
                }
            } else {
                echo 'No se ha encontrado el usuario.<br>';
                echo '<a href="/staffPanel/users">Regresar</a>';
            }
        }

        if(isset($_GET['userId'])){
            $userId = filter_input(INPUT_GET, 'userId', FILTER_VALIDATE_INT);
            $userIdToModify = getUserData($connection, $roleResult, $userId, "idType");
        } elseif(isset($_GET['userEmail'])){
            $userEmail = filter_input(INPUT_GET, 'userEmail', FILTER_VALIDATE_EMAIL);
            $userIdToModify = getUserData($connection, $roleResult, $userEmail, "emailType");
        } else {
            echo 'No se ha encontrado el usuario asignado a ese ID o email.<br>';
            echo '<a href="/staffPanel/users">Regresar</a>';
        }
    } else {
        header("HTTP/1.0 403 Forbidden");
        require_once APP_ROOT . 'src/main/staffPanel/noAccess.php';
        exit();
    }
    if ($roleResult['manageUser'] == '1') {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userInformationUpdate'])) {
            if ($_POST['user'] && $_POST['lastName'] && $_POST['email']) {
                $email = strip_tags(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
                $user = strip_tags(filter_input(INPUT_POST, 'user', FILTER_UNSAFE_RAW));
                $secondName = strip_tags(filter_input(INPUT_POST, 'secondName', FILTER_UNSAFE_RAW));
                $lastName = strip_tags(filter_input(INPUT_POST, 'lastName', FILTER_UNSAFE_RAW));
                $secondLastName = strip_tags(filter_input(INPUT_POST, 'secondLastName', FILTER_UNSAFE_RAW));
                $domicile = strip_tags(filter_input(INPUT_POST, 'domicile', FILTER_UNSAFE_RAW));
                $city = strip_tags(filter_input(INPUT_POST, 'city', FILTER_UNSAFE_RAW));
                $state = strip_tags(filter_input(INPUT_POST, 'state', FILTER_UNSAFE_RAW));
                $country = strip_tags(filter_input(INPUT_POST, 'country', FILTER_UNSAFE_RAW));
                $zipCode = strip_tags(filter_input(INPUT_POST, 'zipCode', FILTER_UNSAFE_RAW));
                try {
                    $userEmailStatement = $connection->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
                    $userEmailStatement->execute(array(':email' => $email));
                    $userEmailResult = $userEmailStatement->fetch(PDO::FETCH_ASSOC);
                    
                    if ($userEmailResult) {
                        if ($userEmailResult['id'] != $userIdToModify) {
                            echo "<script>alert('El email proporcionado ya está en uso por otro usuario.');</script>";
                            $errors[] = 'El email proporcionado ya está en uso por otro usuario.';
                            exit(); 
                        }
                    }
                    $connection->beginTransaction();
                    $userStatement = $connection->prepare('UPDATE users SET user = :user, secondName = :secondName, lastName = :lastName, secondLastName = :secondLastName, email = :email WHERE id = :userId');
                    $userStatement->execute(array(
                        ':user' => $user, 
                        ':secondName' => $secondName, 
                        ':lastName' => $lastName, 
                        ':secondLastName' => $secondLastName, 
                        ':email' => $email, 
                        ':userId' => $userIdToModify
                    ));

                    $userLocationStatement = $connection->prepare('UPDATE userslocation SET domicile = :domicile, city = :city, state = :state, country = :country, zipCode = :zipCode WHERE userId = :userId');
                    $userLocationStatement->execute(array(
                        ':domicile' => $domicile, 
                        ':city' => $city, 
                        ':state' => $state, 
                        ':country' => $country, 
                        ':userId' => $userIdToModify, 
                        ':zipCode' => $zipCode
                    ));
                    
                    $userEmailCheck = $userEmailResult;
                    $stripeCustomerId = $userEmailCheck['stripeCustomerId'] ?? null;
                    
                    if (!$stripeCustomerId) {
                        $userCurrentData = getUserData($connection, $roleResult, $userIdToModify, "idType", true); 
                        $stripeCustomerId = $userCurrentData['user']['stripeCustomerId'] ?? null;
                    }

                    \Stripe\Stripe::setApiKey($_ENV['stripeSecret']);
                    $stripe = new \Stripe\StripeClient($_ENV['stripeSecret']);
                    $customer = $stripe->customers->update(
                        $stripeCustomerId,
                        ['email' => $email, 'metadata' => ['userEmail' => $email]]
                    );

                    $connection->commit();
                    echo '<script type="text/javascript">window.location.href ="/staffPanel/user?userId=' . $userIdToModify . '&userDataRegistrationStatus=success";</script>';
                    exit;

                } catch (\PDOException $e) {
                    if ($connection->inTransaction()) {
                        $connection->rollBack();
                    }
                    error_log('Error al actualizar datos de usuario (BD): ' . $e->getMessage());
                    echo '<script type="text/javascript">window.location.href ="/staffPanel/user?userId=' . $userIdToModify . '&userDataRegistrationStatus=error";</script>';
                    exit;
                    
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    if ($connection->inTransaction()) {
                        $connection->rollBack();
                    }
                    error_log("USER EDITION: Stripe API error: " . $e->getMessage());
                    $errors[] = 'Ocurrió un error con el servicio de pago (Stripe). Por favor, inténtalo de nuevo.';
                    echo '<script type="text/javascript">window.location.href ="/staffPanel/user?userId=' . $userIdToModify . '&userDataRegistrationStatus=error";</script>';
                    exit;
                    
                } catch (\Exception $e) {
                    if ($connection->inTransaction()) {
                        $connection->rollBack();
                    }
                    error_log('Error inesperado al actualizar usuario: ' . $e->getMessage());
                    echo '<script type="text/javascript">window.location.href ="/staffPanel/user?userId=' . $userIdToModify . '&userDataRegistrationStatus=error";</script>';
                    exit;
                }
            } else {
                echo "<script>alert('Faltan los siguientes datos obligatorios: Nombre, apellido y email');</script>";
                exit;
            }
        } else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userVerification'])) {
            $userVerification = filter_input(INPUT_POST, 'userVerification', FILTER_SANITIZE_SPECIAL_CHARS);
            if($userVerification === "verify_" . $userIdToModify) {
                try {
                    $connection->beginTransaction();
                    $statement = $connection->prepare('UPDATE users SET status = :status WHERE id = :id');
                    $statement->execute(array(
                        ':status' => "verified",
                        ':id' => $userIdToModify,
                    ));

                    $estatement = $connection->prepare('UPDATE userscode SET verificationCode = :verificationCode, verificationCodeDate = :verificationCodeDate, lastUserVerification = :lastUserVerification WHERE userId = :userId');
                    $estatement->execute(array(
                        ':verificationCode' => NULL,
                        ':userId' => $userIdToModify,
                        ':verificationCodeDate' => NULL,
                        ':lastUserVerification' => date('Y-m-d H:i:s'),
                    ));

                    $connection->commit();
                    exit();
                } catch (PDOException $e) {
                    $connection->rollBack();
                    error_log("VERIFICATION_ERROR: Database error: " . $e->getMessage());
                    $errors[] = 'Ocurrió un error en la base de datos. Inténtalo de nuevo.';
                }
            } else {
                echo "<script>alert('Error: No se pudo verificar al usuario.');</script>";
            }
        } else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deVerifyConfirmation'])) {
            $deVerifyConfirmation = filter_input(INPUT_POST, 'deVerifyConfirmation', FILTER_SANITIZE_SPECIAL_CHARS);
            if($deVerifyConfirmation === "true") {
                try {
                    $connection->beginTransaction();
                    $statement = $connection->prepare('UPDATE users SET status = :status WHERE id = :id');
                    $statement->execute(array(
                        ':status' => "notverified",
                        ':id' => $userIdToModify,
                    ));

                    $replaceStatement = $connection->prepare('REPLACE INTO userscode (userId) VALUES (:userId)');
                    $replaceStatement->execute([':userId' => $userIdToModify]);

                    $connection->commit();
                    exit();
                } catch (PDOException $e) {
                    $connection->rollBack();
                    error_log("DEVERIFICATION_ERROR: Database error: " . $e->getMessage());
                    $errors[] = 'Ocurrió un error en la base de datos. Inténtalo de nuevo.';
                }
            } else {
                echo "<script>alert('Error: No se pudo verificar al usuario.');</script>";
            }
        } else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['removeUserAccount'])) {
            $removeUserAccount = filter_input(INPUT_POST, 'removeUserAccount', FILTER_SANITIZE_SPECIAL_CHARS);
            if($removeUserAccount === "removeAccount_" . $userIdToModify) {
                try {
                    $userStatement = $connection->prepare('SELECT * FROM users WHERE id = :userId LIMIT 1');
                    $userStatement->execute(array(':userId' => $userIdToModify));
                    $userResult = $userStatement->fetch();
                    $stripeCustomerId = $userResult['stripeCustomerId'];
                    
                    \Stripe\Stripe::setApiKey($_ENV['stripeSecret']);
                    \Stripe\Customer::retrieve($stripeCustomerId)->delete();

                    $connection->beginTransaction();
                    $statement = $connection->prepare('DELETE FROM users WHERE id = :id');
                    $statement->execute(array(
                        ':id' => $userIdToModify,
                    ));
                    
                    $connection->commit();
                    exit();
                } catch (PDOException $e) {
                    $connection->rollBack();
                    error_log("ACCOUNT_REMOVAL_ERROR: Database error: " . $e->getMessage());
                    $errors[] = 'Ocurrió un error en la base de datos. Inténtalo de nuevo.';
                }
            } else {
                echo "<script>alert('Error: No se pudo verificar al usuario.');</script>";
            }
        }
    }
} else{
    header("HTTP/1.0 403 Forbidden");
    require_once APP_ROOT . 'src/main/staffPanel/noAccess.php';
    exit();
}