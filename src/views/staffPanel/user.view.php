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
        <h2>Staff Panel | <b style="background:red; padding: 2px 5px;">SENSIBLE</b></h2>
        <div>
            <a href="/staffPanel/tickets">Tickets</a>
            <a href="/staffPanel/services">Servicios</a>
            <a href="/staffPanel/users">Usuarios</a>
        </div>
    </div>

    <div class="main">
        <h1>Usuario (<?php echo htmlspecialchars($userResult['id'], ENT_QUOTES, 'UTF-8'); ?>)</h1>
        <div class="user">
            <form id="editUserForm" method="POST" action="<?php echo htmlspecialchars('/staffPanel/user' . '?' . $_SERVER['QUERY_STRING']);?>">
                <div class="userInfo">
                    <h2>Información del usuario</h2>
                    <?php $date = new DateTime($userResult['date']); if ($roleResult['manageUser'] == '1'): ?>
                        <div class="userDetailsEditable">
                            <div>
                                <label>Nombre:</label>
                                <input type="text" name="user" value="<?php echo htmlspecialchars($userResult['user'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            </div>
                            <div>
                                <label>Segundo Nombre:</label>
                                <input type="text" name="secondName" value="<?php echo htmlspecialchars($userResult['secondName'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            </div>
                            <div>
                                <label>Apellido:</label>
                                <input type="text" name="lastName" value="<?php echo htmlspecialchars($userResult['lastName'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            </div>
                            <div>
                                <label>Segundo Apellido:</label>
                                <input type="text" name="secondLastName" value="<?php echo htmlspecialchars($userResult['secondLastName'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            </div>
                            <div>
                                <label>Correo:</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($userResult['email'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            </div>
                        </div>
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
                <div class="userInfo" style="margin-top:10px;">
                    <h2>Información de contacto</h2>
                    <b>Es posible que cierta información no aparezca ya que el usuario no ha querido dar la información.</b>

                    <?php if ($roleResult['manageUser'] == '1'): ?>
                        <div class="userDetailsEditable">
                            <div>
                                <label>Dirección:</label>
                                <input type="text" name="domicile" value="<?php echo htmlspecialchars($userLocationResult['domicile'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            </div>
                            <div>
                                <label>Ciudad:</label>
                                <input type="text" name="city" value="<?php echo htmlspecialchars($userLocationResult['city'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            </div>
                            <div>
                                <label>Estado:</label>
                                <input type="text" name="state" value="<?php echo htmlspecialchars($userLocationResult['state'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            </div>
                            <div>
                                <label>País:</label>
                                <input type="text" name="country" value="<?php echo htmlspecialchars($userLocationResult['country'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            </div>
                            <div>
                                <label>Organización:</label>
                                <input type="text" name="organization" value="<?php echo htmlspecialchars($userLocationResult['organization'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            </div>
                            <div>
                                <label>Código postal:</label>
                                <input type="text" name="zipCode" value="<?php echo htmlspecialchars($userLocationResult['zipCode'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            </div>
                        </div>
                    <?php else: ?>
                        <p><b>Dirección:</b> <?php echo htmlspecialchars($userLocationResult['domicile'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><b>Ciudad:</b> <?php echo htmlspecialchars($userLocationResult['city'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><b>Estado:</b> <?php echo htmlspecialchars($userLocationResult['state'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><b>País:</b> <?php echo htmlspecialchars($userLocationResult['country'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><b>Organización:</b> <?php echo htmlspecialchars($userLocationResult['organization'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><b>Código postal:</b> <?php echo htmlspecialchars($userLocationResult['zipCode'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                </div>

                <?php if ($roleResult['manageUser'] == '1'): ?>
                    <button type="button" id="editButton">Editar</button>
                    <button type="submit" id="saveButton" name="userInformationUpdate" style="display:none;">Guardar cambios</button>
                <?php endif; ?>
            </form>
            <?php if ($roleResult['manageUser'] == '1'): ?>
            <div class="userActions">
                <div><?php echo $viewServices ?? '<a>Sin acceso</a>'; ?></div>
                <?php if ($userResult['status'] !== "verified"): ?>
                    <div id="verifyUserButton">Verificar</div>
                    <script>
                        document.getElementById("verifyUserButton").addEventListener("click", function() {
                            let verificationPrompt = prompt("Por favor, introduce 'verify_<?php echo htmlspecialchars($userResult['id'], ENT_QUOTES, 'UTF-8'); ?>':");
                            if (verificationPrompt === "verify_<?php echo htmlspecialchars($userResult['id'], ENT_QUOTES, 'UTF-8'); ?>") {
                                alert("Correcto, has introducido: " + verificationPrompt + "(Reinicia la página para ver los cambios)");
                            } else{
                                alert("Error: No se pudo verificar al usuario.");
                            }

                            let data = new FormData();
                            data.append('userVerification', verificationPrompt);

                            fetch("<?php echo htmlspecialchars('/staffPanel/user' . '?' . $_SERVER['QUERY_STRING']);?>", {
                                method: "POST",
                                body: data
                            }).then(res => {
                                console.log("Request complete! response:", res);
                            });
                        });
                    </script>
                <?php else: ?>
                    <div id="deVerifyUserButton">Desverificar</div>
                    <script>
                         document.getElementById("deVerifyUserButton").addEventListener("click", function() {
                            let isConfirmed = confirm("¿Quitar verificación?");

                            if (isConfirmed) {
                                alert("¡Cambios guardados con éxito! (Reinicia la página para ver los cambios)");

                                let data = new FormData();
                                data.append('deVerifyConfirmation', isConfirmed);

                                fetch("<?php echo htmlspecialchars('/staffPanel/user' . '?' . $_SERVER['QUERY_STRING']);?>", {
                                    method: "POST",
                                    body: data
                                }).then(res => {
                                    console.log("Request complete! response:", res);
                                });
                            } else {
                                alert("Has decidido cancelar. Los cambios no se guardarán.");
                            }
                         });
                    </script>
                <?php endif; ?>
                <div>Totp</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($_GET['userDataRegistrationStatus'] === 'success'): ?>
        <script>alert('Los 2 tipos datos del usuario se han actualizado correctamente.');</script>
    <?php elseif ($_GET['userDataRegistrationStatus'] === 'error'): ?>
        <script>alert('Error al actualizar los datos del usuario. Por favor, inténtelo de nuevo más tarde.');</script>
    <?php endif; ?>
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