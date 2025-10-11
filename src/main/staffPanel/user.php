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
        if ($roleResult['viewUser'] == '1') {
            // The first two variables of the fuction are used to obtain the variables of the rest of the code into the function.
            function getUserData($connection, $roleResult, $obteinedUserData, $dataType){
                if ($dataType === "idType"){
                    $userStatement = $connection->prepare('SELECT * FROM users WHERE id = :userId LIMIT 1');
                    $userStatement->execute(array(':userId' => $obteinedUserData));
                    $userResult = $userStatement->fetch();
                } elseif ($dataType === "emailType") {
                    $userStatement = $connection->prepare('SELECT * FROM users WHERE email = :userEmail LIMIT 1');
                    $userStatement->execute(array(':userEmail' => $obteinedUserData));
                    $userResult = $userStatement->fetch();
                }
                if($userResult){
                    $userLocationStatement = $connection->prepare('SELECT * FROM userslocation WHERE userId = :userId LIMIT 1');
                    $userLocationStatement->execute(array(':userId' => $userResult['id']));
                    $userLocationResult = $userLocationStatement->fetch();
                    if ($roleResult['viewServiceData'] == '1') {
                        $viewServices = "<div class='user-info'><h2>Servicios</h2><a href='/staffPanel/services?userId=" . htmlspecialchars($userResult['id']) ."'>Ver Servicios</a></div>";
                    }
                    if ($userResult['role'] != '-1') {
                        header("HTTP/1.0 403 Forbidden");
                        echo "<h1>Error 403 - Ni tienes acceso a esta página.</h1>";
                        echo "<a href='/'>Volver al inicio</h1>";
                        exit();
                    } else {
                        require_once APP_ROOT . 'src/views/staffPanel/user.view.php';
                    }
                } else {
                    echo 'No se ha encontrado el usuario.<br>';
                    echo '<a href="/staffPanel/users">Regresar</a>';
                }
            }
            if($_GET['userId']){
                $userId = filter_input(INPUT_GET, 'userId', FILTER_VALIDATE_INT);
                getUserData($connection, $roleResult, $userId, "idType");
            } elseif($_GET['userEmail']){
                $userEmail = filter_input(INPUT_GET, 'userEmail', FILTER_VALIDATE_EMAIL);
                getUserData($connection, $roleResult, $userEmail, "emailType");
            } else {
                echo 'No se ha encontrado el usuario asignado a ese ID o email.<br>';
                echo '<a href="/staffPanel/users">Regresar</a>';
            }
        } else {
            require_once APP_ROOT . 'src/main/staffPanel/noAccess.php';
        }
        if ($roleResult['manageUser'] == '1') {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if ($_POST['userId'] && $_POST['user'] && $_POST['lastName'] && $_POST['email']) {
                    $userId = filter_input(INPUT_POST, 'userId', FILTER_VALIDATE_INT);
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
                        $userStatement->execute(array(':user' => $user, ':secondName' => $secondName, ':lastName' => $lastName, ':secondLastName' => $secondLastName, ':email' => $email, ':userId' => $userId));
                        
                        
                        $userLocationStatement = $connection->prepare('UPDATE userslocation SET domicile = :domicile, city = :city, state = :state, country = :country, zipCode = :zipCode WHERE userId = :userId');
                        $userLocationStatement->execute(array(':domicile' => $domicile, ':city' => $city, ':state' => $state, ':country' => $country, ':userId' => $userId, ':zipCode' => $zipCode));
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
            }
        }
    }
} else {
    header('Location: ../auth/signin');
    exit;
} 