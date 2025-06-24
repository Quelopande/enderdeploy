<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enderdeploy - Staff Panel | Users [Confidencial]</title>
    <link rel="stylesheet" href="/assets/styles/staffPanel/user.css">
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

    <div class="main">
        <h1>Usuario</h1>
        <div class="user">
            <form id="editUserForm" method="POST" action="<?php echo htmlspecialchars('/staffPanel/user' . '?' . $_SERVER['QUERY_STRING']); ?>">
            <input type="hidden" name="userId" value="<?php echo htmlspecialchars($userResult['id'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="user-info">
                    <h2>Información del usuario</h2>
                    <?php $date = new DateTime($userResult['date']); if ($roleResult['manageUser'] == '1'): ?>
                        <label>Nombre:</label>
                        <input type="text" name="user" value="<?php echo htmlspecialchars($userResult['user'], ENT_QUOTES, 'UTF-8'); ?>" disabled>

                        <label>Segundo Nombre:</label>
                        <input type="text" name="secondName" value="<?php echo htmlspecialchars($userResult['secondName'], ENT_QUOTES, 'UTF-8'); ?>" disabled>

                        <label>Apellido:</label>
                        <input type="text" name="lastName" value="<?php echo htmlspecialchars($userResult['lastName'], ENT_QUOTES, 'UTF-8'); ?>" disabled>

                        <label>Segundo Apellido:</label>
                        <input type="text" name="secondLastName" value="<?php echo htmlspecialchars($userResult['secondLastName'], ENT_QUOTES, 'UTF-8'); ?>" disabled>

                        <label>Correo:</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($userResult['email'], ENT_QUOTES, 'UTF-8'); ?>" disabled>

                        <label>Rol:</label>
                        <input type="text" name="role" value="<?php echo htmlspecialchars($userResult['role'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                        <p><b>Última edición:</b> <?php echo htmlspecialchars($date->format('H:i:s d/m/Y'), ENT_QUOTES, 'UTF-8'); ; ?></p>
                    <?php else: ?>
                        <p><b>Nombre:</b> <?php echo htmlspecialchars(ucwords(trim($userResult['user'])), ENT_QUOTES, 'UTF-8') . " " .
                            htmlspecialchars(ucwords(trim($userResult['secondName'])), ENT_QUOTES, 'UTF-8') . " " .
                            htmlspecialchars(ucwords(trim($userResult['lastName'])), ENT_QUOTES, 'UTF-8') . " " .
                            htmlspecialchars(ucwords(trim($userResult['secondLastName'])), ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <p><b>Correo:</b> <?php echo htmlspecialchars($userResult['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><b>Rol:</b> <?php echo htmlspecialchars($userResult['role'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><b>Última edición:</b> <?php echo htmlspecialchars($date->format('H:i:s d/m/Y'), ENT_QUOTES, 'UTF-8'); ; ?></p>
                    <?php endif; ?>
                </div>
                <div class="user-info" style="margin-top:10px;">
                    <h2>Información de contacto</h2>
                    <b>Es posible que cierta información no aparezca ya que el usuario no ha querido dar la información.</b>

                    <?php 
                    function getUserLocationData($dataToObtain){
                        if($userLocationResult[$dataToObtain]){
                            return htmlspecialchars($userLocationResult[$dataToObtain], ENT_QUOTES, 'UTF-8');
                        } else{
                            return "";
                        }
                    }
                    if ($roleResult['manageUser'] == '1'): ?>
                        <label>Dirección:</label>
                        <input type="text" name="domicile" value="<?php echo getUserLocationData('domicile'); ?>" disabled>

                        <label>Ciudad:</label>
                        <input type="text" name="city" value="<?php echo getUserLocationData('city'); ?>" disabled>

                        <label>Estado:</label>
                        <input type="text" name="state" value="<?php echo getUserLocationData('state'); ?>" disabled>

                        <label>País:</label>
                        <input type="text" name="country" value="<?php echo getUserLocationData('country'); ?>" disabled>

                        <label>Organización:</label>
                        <input type="text" name="organization" value="<?php echo getUserLocationData('organization'); ?>" disabled>

                        <label>Código postal:</label>
                        <input type="text" name="zipCode" value="<?php echo getUserLocationData('zipCode'); ?>" disabled>
                    <?php else: ?>
                        <p><b>Dirección:</b> <?php echo getUserLocationData('domicile'); ?></p>
                        <p><b>Ciudad:</b> <?php echo getUserLocationData('city'); ?></p>
                        <p><b>Estado:</b> <?php echo getUserLocationData('state'); ?></p>
                        <p><b>País:</b> <?php echo getUserLocationData('country'); ?></p>
                        <p><b>Organización:</b> <?php echo getUserLocationData('organization'); ?></p>
                        <p><b>Código postal:</b> <?php echo getUserLocationData('zipCode'); ?></p>
                    <?php endif; ?>
                </div>

                <?php if ($roleResult['manageUser'] == '1'): ?>
                    <button type="button" id="editButton">Editar</button>
                    <button type="submit" id="saveButton" style="display:none;">Guardar cambios</button>
                <?php endif; ?>
                <?php echo $viewServices; ?>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editButton = document.getElementById("editButton");
            const saveButton = document.getElementById("saveButton");
            const inputs = document.querySelectorAll("#editUserForm input, #editUserForm select");

            editButton.addEventListener("click", function() {
                inputs.forEach(input => input.removeAttribute("disabled"));
                editButton.style.display = "none";
                saveButton.style.display = "inline-block";
            });
        });
    </script>

</body>
</html>
