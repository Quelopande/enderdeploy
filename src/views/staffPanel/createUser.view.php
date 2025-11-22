<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enderdeploy - Staff Panel [Confidencial]</title>
    <link rel="stylesheet" href="/assets/styles/staffPanel/createUser.css">
    <link rel="website icon" type="ico" href="/assets/img/logo.ico">
</head>
<body>
    <div class="menu">
        <h2><a href="/staffPanel">StaffPanel</a></h2>
        <div>
            <a href="/staffPanel/tickets">Tickets</a>
            <a href="/staffPanel/services">Servicios</a>
            <a href="/staffPanel/users">Usuarios</a>
        </div>
    </div>
    <div>
        <h1>Crear usuario (<b style="background:red; padding: 2px 5px;">SENSIBLE</b> si contiene datos)</h1>
        <form id="createUserForm" method="POST" action="<?php echo htmlspecialchars('/staffPanel/createUser');?>">
            <div id="mainUserData">
                <div>
                    <label>Nombre<span style="color: rgb(201, 60, 0);">*</span>:</label>
                    <input type="text" name="user" required>
                </div>
                <div>
                    <label>Segundo Nombre:</label>
                    <input type="text" name="secondName">
                </div>
                <div>
                    <label>Apellido<span style="color: rgb(201, 60, 0);">*</span>:</label>
                    <input type="text" name="lastName" required>
                </div>
                <div>
                    <label>Segundo Apellido:</label>
                    <input type="text" name="secondLastName">
                </div>
                <div>
                    <label>Email<span style="color: rgb(201, 60, 0);">*</span>:</label>
                    <input type="email" name="email" required>
                </div>
                <div>
                    <label>Contraseña<span style="color: rgb(201, 60, 0);">*</span>:</label>
                    <input type="password" name="password" required>
                </div>
            </div>
            <div id="additionalUserData">
                <div>
                    <label>Organización:</label>
                    <input type="text" name="organization">
                </div>
                <div>
                    <label>País:</label>
                    <input type="text" name="country">
                </div>
                <div>
                    <label>Estado / Región / Provincia:</label>
                    <input type="text" name="state">
                </div>
                <div>
                    <label>Código postal:</label>
                    <input type="text" name="zipCode">
                </div>
                <div>
                    <label>Ciudad:</label>
                    <input type="text" name="city">
                </div>
                <div>
                    <label>Domicilio:</label>
                    <input type="text" name="domicile">
                </div>
            </div>
            <p>La cuenta se puede verificar desde el panel de control <i>user</i>, y los roles se puedes editar en el panel <i>roles</i></p>
            <button type="submit" name="createUserForm">Crear usuario</button>
        </form>
        <?php
        if (!empty($successMessage)): ?>
            <div style="background:green; padding: 2px 5px;">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <?php
        if (!empty($errors) && is_array($errors)): ?>
            <div style="background:red; padding: 2px 5px;">
                <?php 
                foreach ($errors as $error) {
                    echo '<p>' . htmlspecialchars($error) . '</p>';
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>