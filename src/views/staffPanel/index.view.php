<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enderdeploy - Staff Panel [Confidencial]</title>
    <link rel="stylesheet" href="/assets/styles/staffPanel/index.css">
    <link rel="website icon" type="ico" href="/assets/img/logo.ico">
</head>
<body>
    <div class="menu">
        <h2><a href="/staffPanel" style="text-decoration: none;">StaffPanel</a></h2>
        <div>
            <a href="/staffPanel/tickets">Tickets</a>
            <a href="/staffPanel/services">Servicios</a>
            <a href="/staffPanel/users">Usuarios</a>
        </div>
    </div>
    <div class="welcome">
        <h1>Staff Panel</h1>
        <h2>Bienvenido, <span><?php echo $result['user'];?></span></h2>
    </div>
    <a href="/dashboard">Volver a dashboard común (Panel de usuarios)</a>
    <div class="fastLinks">
        <h2>Enlaces rápidos</h2>
        <a href="/staffPanel/tickets">Tickets</a>
        <a href="/staffPanel/services">Servicios</a>
        <a href="/staffPanel/users">Usuarios</a>
        <a href="/staffPanel/stats">Estadísticas</a>
    </div>
</body>
</html>