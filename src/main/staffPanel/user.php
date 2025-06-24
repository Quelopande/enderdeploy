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
                    $userLocationStatement = $connection->prepare('SELECT * FROM usersLocation WHERE userId = :userId LIMIT 1');
                    $userLocationStatement->execute(array(':userId' => $userResult['id']));
                    $userLocationResult = $userLocationStatement->fetch();
                    if ($roleResult['viewServiceData'] == '1') {
                        $viewServices = "<div class='user-info'><h2>Servicios</h2><a href='/staffPanel/service?userId=" . $userResult['id'] ."'>Ver Servicios</a></div>";
                    }
                    require_once APP_ROOT . 'src/views/staffPanel/user.view.php';
                } else {
                    echo 'No se ha encontrado el usuario.<br>';
                    echo '<a href="/staffPanel/users">Regresar</a>';
                }
            }
            if($_GET['userId']){
                $userId = $_GET['userId'];
                getUserData($connection, $roleResult, $userId, "idType");
            } elseif($_GET['userEmail']){
                $userEmail = $_GET['userEmail'];
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
                if ($_POST['userId'] && $_POST['user'] && $_POST['secondName'] && $_POST['lastName'] && $_POST['secondLastName'] && $_POST['email'] && $_POST['role']) {
                    $userId = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_NUMBER_INT);
                    $user = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING);
                    $secondName = filter_input(INPUT_POST, 'secondName', FILTER_SANITIZE_STRING);
                    $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
                    $secondLastName = filter_input(INPUT_POST, 'secondLastName', FILTER_SANITIZE_STRING);
                    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_NUMBER_INT);
                    $domicile = filter_input(INPUT_POST, 'domicile', FILTER_SANITIZE_STRING);
                    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
                    $state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING);
                    $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);                    
                    try {
                        $userStatement = $connection->prepare('UPDATE users SET user = :user, secondName = :secondName, lastName = :lastName, secondLastName = :secondLastName, email = :email, role = :role WHERE id = :userId');
                        $userStatement->execute(array(':user' => $user, ':secondName' => $secondName, ':lastName' => $lastName, ':secondLastName' => $secondLastName, ':email' => $email, ':role' => $role, ':userId' => $userId));
                    
                        $userLocationStatement = $connection->prepare('UPDATE usersLocation SET domicile = :domicile, city = :city, state = :state, country = :country WHERE userId = :userId');
                        $userLocationStatement->execute(array(':domicile' => $domicile, ':city' => $city, ':state' => $state, ':country' => $country, ':userId' => $userId));
                        
                        echo "Datos actualizados correctamente.";
                    } catch (Exception $e) {
                        echo 'Error al actualizar los datos: ' . $e->getMessage();
                    }
                    header('Location: ' . htmlspecialchars('/staffPanel/user' . '?' . $_SERVER['QUERY_STRING']));
                    exit;                    
                }
            }
        }
    }
} else {
    header('Location: ../auth/signin');
    exit;
}