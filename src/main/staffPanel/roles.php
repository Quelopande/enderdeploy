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

if (!$roleResult || $roleResult['manageRoles'] != '1') {
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

        $checkResult = $auth->verifyCode($secret, $oneCodeInput, 2);
        if ($checkResult) {
            $_SESSION['totpVerified'] = true;
            $_SESSION['totpVerifiedTime'] = time();
            header("Location: /staffPanel/roles");
            exit;
        } else {
            $errors = '<div class="alert alert-danger">Código incorrecto. Inténtalo de nuevo.</div>';
        }
    }

    echo "<form action='" . htmlspecialchars('/staffPanel/roles') . "' method='POST'>
        <input id='code' type='text' maxlength='6' size='6' name='code' placeholder='Código TOTP' autocomplete='one-time-code' required autofocus/>
        <input type='submit' value='Verificar'/>
      </form>";

if (!empty($errors)) {
    echo "<div class='error'>" . htmlspecialchars($errors) . "</div>";
}
    exit;
}

$sessionDuration = 1800;
if (isset($_SESSION['totpVerifiedTime']) && (time() - $_SESSION['totpVerifiedTime'] > $sessionDuration)) {
    unset($_SESSION['totpVerified']);
    unset($_SESSION['totpVerifiedTime']);
    header("Location: /staffPanel/roles");
    exit;
} else {
    $_SESSION['totpVerifiedTime'] = time();
}

$allRolesStatement = $connection->prepare('SELECT * FROM roles');
$allRolesStatement->execute();
$allRoles = $allRolesStatement->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['saveRoles'])) {
    foreach ($_POST['roleName'] as $roleId => $roleName) {
        if(empty($roleName)) {
            echo "<script>alert('El nombre del rol no puede estar vacío.');</script>";
        }else{
            $stmt = $connection->prepare("
                UPDATE roles SET roleName = ?, ticket = ?, viewSubscriptionData = ?, 
                manageSubscription = ?, addUser = ?, 
                manageUser = ?, viewUser = ?, viewLogs = ?, manageRoles = ?
                WHERE roleId = ?
            ");

            $stmt->execute([
                $roleName,
                $_POST['ticket'][$roleId] ?? '0',
                $_POST['viewSubscriptionData'][$roleId] ?? '0',
                $_POST['manageSubscription'][$roleId] ?? '0',
                $_POST['addUser'][$roleId] ?? '0',
                $_POST['manageUser'][$roleId] ?? '0',
                $_POST['viewUser'][$roleId] ?? '0',
                $_POST['viewLogs'][$roleId] ?? '0',
                $_POST['manageRoles'][$roleId] ?? '0',
                $roleId
            ]);
            header("Location: /staffPanel/roles");
        }
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['createRol'])){
    if(empty($_POST['name'])) {
        echo "<script>alert('El nombre del rol no puede estar vacío12.');</script>";
    }
    foreach (['ticket', 'viewSubscriptionData', 'manageSubscription', 'addUser', 'manageUser', 'viewUser', 'viewLogs', 'manageRoles'] as $field) {
        if (!isset($_POST[$field])) {
            $_POST[$field] = '0';
        }
    }
    $stmt = $connection->prepare('INSERT INTO users (roleName, ticket, viewSubscriptionData, manageSubscription, addUser, manageUser, viewUser, viewLogs, manageRoles) VALUES (:roleName, :ticket, :viewSubscriptionData, :manageSubscription, :addUser, :manageUser, :viewUser, :viewLogs, :manageRoles)');
    $stmt->execute(array(
        ':roleName' => htmlspecialchars(strtolower(trim($_POST['roleName'])), ENT_QUOTES, 'UTF-8'),
        ':ticket' => $_POST['ticket'],
        ':viewSubscriptionData' => $_POST['viewSubscriptionData'],
        ':manageSubscription' => $_POST['manageSubscription'],
        ':addUser' => $_POST['addUser'],
        ':manageUser' => $_POST['manageUser'],
        ':viewUser' => $_POST['viewUser'],
        ':viewLogs' => $_POST['viewLogs'],
        ':manageRoles' => $_POST['manageRoles']
    ));
} else if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addUserRol'])){
    $userId = htmlspecialchars(strtolower(trim($_POST['userId'])), ENT_QUOTES, 'UTF-8');
    $roleId = htmlspecialchars(strtolower(trim($_POST['roleId'])), ENT_QUOTES, 'UTF-8');
    $stmt = $connection->prepare('UPDATE users SET role = ? WHERE id = ?');
    $stmt->execute([$roleId, $userId]);
    header("Location: /staffPanel/roles");
}

function isChecked($value) {
    return $value == '1' ? 'checked' : '';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enderdeploy | Roles SENSIBLE - Staff Panel [Confidencial]</title>
    <link rel="stylesheet" href="/assets/styles/staffPanel/roles.css">
    <link rel="icon" type="image/x-icon" href="/assets/img/logo.ico">
</head>
<body>
    <div class="menu">
        <h2><a href="/staffPanel" style="text-decoration: none;">Staff Panel | <b style="background:red; padding: 2px 5px;">SENSIBLE</b></a></h2>
        <div>
            <a href="/staffPanel/tickets">Tickets</a>
            <a href="/staffPanel/subscriptions">Servicios</a>
            <a href="/staffPanel/users">Usuarios</a>
        </div>
    </div>

    <div class="roles">
        <form method="POST" action="<?php echo htmlspecialchars('/staffPanel/roles'); ?>" validate>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Ticket</th>
                    <th>Ver Servicios</th>
                    <th>Ver Precios</th>
                    <th>Administrar Servicios</th>
                    <th>Agregar Usuarios</th>
                    <th>Administrar Usuarios</th>
                    <th>Ver Usuarios</th>
                    <th>Ver Logs</th>
                    <th>Administrar Roles</th>
                </tr>
                <?php foreach ($allRoles as $singleRole): ?>
                    <?php $roleId = htmlspecialchars($singleRole['roleId'], ENT_QUOTES, 'UTF-8'); ?>
                    <tr>
                        <td><p><?= $roleId ?></p></td>
                        <td><input type="text" name="roleName[<?= $roleId ?>]" value="<?= htmlspecialchars($singleRole['roleName']) ?>" required></td>

                        <?php foreach (['ticket', 'viewSubscriptionData', 'manageSubscription', 'addUser', 'manageUser', 'viewUser', 'viewLogs', 'manageRoles'] as $field): ?>
                            <td>
                                <input type="hidden" name="<?= $field ?>[<?= $roleId ?>]" value="0">
                                <input type="checkbox" name="<?= $field ?>[<?= $roleId ?>]" value="1" <?= isChecked($singleRole[$field]) ?>>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>

            <button type="submit" name="saveRoles">Guardar Roles</button>
        </form>
    </div>
    <form class="createRol" method="POST" action="<?php echo htmlspecialchars('/staffPanel/roles'); ?>" validate>
        <h1>Creador de roles</h1>
        <table>
            <tr>
                <th>Nombre</th>
                <?php
                $permissions = [
                    'ticket' => 'Ticket',
                    'viewSubscriptionData' => 'Ver Servicios',
                    'manageSubscription' => 'Administrar Servicios',
                    'addUser' => 'Agregar Usuarios',
                    'manageUser' => 'Administrar Usuarios',
                    'viewUser' => 'Ver Usuarios',
                    'viewLogs' => 'Ver Logs',
                    'manageRoles' => 'Administrar Roles'
                ];
                foreach ($permissions as $field => $label): ?>
                    <th><?= $label ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td><input type="text" name="roleName" id="roleName" required></td>
                <?php foreach ($permissions as $field => $label): ?>
                    <td>
                        <input type="hidden" name="<?= $field ?>" value="0">
                        <input type="checkbox" name="<?= $field ?>" value="1">
                    </td>
                <?php endforeach; ?>
            </tr>
        </table>
        <button type="submit" name="createRol">Crear Rol</button>           
    </form>
    <form action="<?php echo htmlspecialchars('/staffPanel/roles'); ?>" method="post" class="addRoleUser" validate>
        <h1>Agregar usuario a rol</h1>
        <label for="userId">ID del usuario</label>
        <input type="text" name="userId" id="userId" placeholder="ID de usuario" required>
        <label for="userId">ID del rol</label>
        <input type="text" name="roleId" id="roleId" placeholder="ID de rol" required>
        <button type="submit" name="addRoleUser">Agregar</button>
    </form>
</body>
</html>
