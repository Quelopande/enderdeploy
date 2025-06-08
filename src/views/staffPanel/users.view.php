<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enderdeploy - Staff Panel | Users [Confidencial]</title>
    <link rel="stylesheet" href="/assets/styles/staffPanel/users.css">
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
    <div class="utils">
        <form action="/staffPanel/user" method="get">
            <input type="text" name="id" id="id" placeholder="Buscar usuario por ID"> 
            <input type="submit">
        </form>
        <form action="/staffPanel/user" method="get">
            <input type="text" name="email" id="email" placeholder="Buscar usuario por EMAIL"> 
            <input type="submit">
        </form>
    </div>
    <div class="allUsers">
        <h2>Usuarios</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado de cuenta</th>
            </tr>
            <?php 
            foreach($allUsers as $singleUser) {
                $id = htmlspecialchars(trim($singleUser['id']), ENT_QUOTES, 'UTF-8');
                $user = htmlspecialchars(ucwords(trim(preg_replace('/\s+/', ' ', $singleUser['user']))), ENT_QUOTES, 'UTF-8');
                $secondName = htmlspecialchars(ucwords(trim(preg_replace('/\s+/', ' ', $singleUser['secondName']))), ENT_QUOTES, 'UTF-8');
                $lastName = htmlspecialchars(ucwords(trim(preg_replace('/\s+/', ' ', $singleUser['lastName']))), ENT_QUOTES, 'UTF-8');
                $secondLastName = htmlspecialchars(ucwords(trim(preg_replace('/\s+/', ' ', $singleUser['secondLastName']))), ENT_QUOTES, 'UTF-8');
                $email = htmlspecialchars(trim($singleUser['email']), ENT_QUOTES, 'UTF-8');
                $role = htmlspecialchars(trim($singleUser['role']), ENT_QUOTES, 'UTF-8');
                $status = htmlspecialchars(trim($singleUser['status']), ENT_QUOTES, 'UTF-8');

                echo "<tr onclick='location.href=\"/staffPanel/user?\";'>\n";
                echo "<td>" . $id . "</td>\n";
                echo "<td>" . $user . " " . $secondName . " " . $lastName . " " . $secondLastName . "</td>\n";
                echo "<td class='private'>" . $email . "</td>\n";
                echo "<td>" . $role . "</td>\n";
                echo "<td>" . $status . "</td>\n";
                echo "</tr>\n";
            }
            ?>
        </table>
    </div>
</body>
</html>