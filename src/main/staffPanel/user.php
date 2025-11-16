<?php
if (isset($_SESSION['id'])) {
    $viewServices = '';
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

        if ($roleResult['viewUser'] == '1') {
            // The first two variables of the fuction are used to obtain the variables of the rest of the code into the function.
            function getUserData($connection, $roleResult, $obteinedUserData, $dataType){
                if ($dataType === "idType"){
                    $userStatement = $connection->prepare('SELECT * FROM users WHERE id = :userId LIMIT 1');
                    $userStatement->execute(array(':userId' => $obteinedUserData));
                } elseif ($dataType === "emailType") {
                    $userStatement = $connection->prepare('SELECT * FROM users WHERE email = :userEmail LIMIT 1');
                    $userStatement->execute(array(':userEmail' => $obteinedUserData));
                }
                $userResult = $userStatement->fetch();

                if($userResult){
                    $userLocationStatement = $connection->prepare('SELECT * FROM userslocation WHERE userId = :userId LIMIT 1');
                    $userLocationStatement->execute(array(':userId' => $userResult['id']));
                    $userLocationResult = $userLocationStatement->fetch();
                    if ($roleResult['viewServiceData'] == '1') {
                        $viewServices = "<a href='/staffPanel/services?userId=" . htmlspecialchars($userResult['id']) ."'>Ver Servicios</a>";
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

            if($_GET['userId']){
                $userId = filter_input(INPUT_GET, 'userId', FILTER_VALIDATE_INT);
                $userIdToModify = getUserData($connection, $roleResult, $userId, "idType");
            } elseif($_GET['userEmail']){
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
                        $userStatement = $connection->prepare('UPDATE users SET user = :user, secondName = :secondName, lastName = :lastName, secondLastName = :secondLastName, email = :email WHERE id = :userId');
                        $userStatement->execute(array(':user' => $user, ':secondName' => $secondName, ':lastName' => $lastName, ':secondLastName' => $secondLastName, ':email' => $email, ':userId' => $userIdToModify));
                        
                        
                        $userLocationStatement = $connection->prepare('UPDATE userslocation SET domicile = :domicile, city = :city, state = :state, country = :country, zipCode = :zipCode WHERE userId = :userId');
                        $userLocationStatement->execute(array(':domicile' => $domicile, ':city' => $city, ':state' => $state, ':country' => $country, ':userId' => $userIdToModify, ':zipCode' => $zipCode));
                    } catch (PDOException $e) {
                        error_log('Error updating user data: ' . $e->getMessage());
                        echo '<script type="text/javascript">window.location.href ="/staffPanel/user?userId=' . $userId . '&userDataRegistrationStatus=error";</script>';
                        exit;
                    }
                    echo '<script type="text/javascript">window.location.href ="/staffPanel/user?userId=' . $userId . '&userDataRegistrationStatus=success";</script>';
                    exit;                    
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
                        $connection->beginTransaction();
                        $statement = $connection->prepare('DELETE FROM users WHERE id = :id');
                        $statement->execute(array(
                            ':id' => $userIdToModify,
                        ));
                        
                        // NOTE: When creating the staff's logs system, add the id of the deleted user, the services he used to hold and email in case we need to contact him.
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
} else {
    header('Location: ../auth/signin');
    exit;
} 