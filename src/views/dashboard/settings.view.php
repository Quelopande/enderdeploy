<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Twitter card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="EnderDeploy - Dashboard">
    <meta name="twitter:image" content="/assets/img/logo.png">
    <!-- Facebook & discord -->
    <meta property="og:locale" content="es"/>
    <meta property="og:site_name" content="©EnderHosting"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="EnderDeploy - Dashboard"/>
    <meta property="og:url" content="https://deploy.enderhosting.com.mx"/>
    <meta property="og:image" content="/assets/img/logo.png"/>
    <meta property="og:image:width" content="540"/>
    <meta property="og:image:height" content="520"/>
    <title>Enderdeploy dashboard</title>
    <link rel="website icon" type="ico" href="/assets/img/logo.ico">
    <link rel="stylesheet" href="/assets/styles/menu.css">
    <link rel="stylesheet" href="/assets/styles/dashboard/settings.css">
    <link rel="stylesheet" href="/assets/styles/modal.css">
    <script src="/assets/js/menu.js" defer></script>
    <link href="https://pro.fontawesome.com/releases/v6.0.0-beta1/css/all.css" rel="stylesheet"/>
</head>
<body data-page-id="5">
    <?php require APP_ROOT . 'src/views/dashboard/menutemplate.view.php';?>
    <main class="separate">
        <div class="serverStatus separate"><i class="fa-solid fa-leaf"></i> Todos nuestros servicios estan funcionales, disfruta sin problemas</div>
        <?php
            function callForData($dbName, $data, $mustContain) {
                global $connection; 
                global $id; 
                
                if($dbName === "users"){
                    $query = "SELECT * FROM {$dbName} WHERE id = :id LIMIT 1";
                    $statement = $connection->prepare($query);
                    $statement->execute(array(':id' => $id));
                    $result = $statement->fetch();
                } else if($dbName === "usersLocation"){
                    $query = "SELECT * FROM {$dbName} WHERE userId = :userId LIMIT 1";
                    $statement = $connection->prepare($query);
                    $statement->execute(array(':userId' => $id));
                    $result = $statement->fetch();   
                }

                $dataNotFoundError = $mustContain ? "Error, por favor contacte con soporte." : "";

                if (isset($result[$data])) {
                    $sanitizedata = ucfirst(htmlspecialchars(trim($result[$data]), ENT_QUOTES, 'UTF-8'));
                    return $sanitizedata;
                } else {
                    return $dataNotFoundError;
                }
            }
        ?>
        <section class="separate">
            <h1>Detalles Básicos del Usuario</h1>
            <div class="showInformation">
                <div>
                    <p>Correo Electrónico</p>
                    <div id="contenteditable" contenteditable="false"><?php echo callForData('users', 'email', true); ?></div>
                </div>
                <div class="margin-left">
                    <p>Nombre Completo</p>
                    <div id="contenteditable" contenteditable="false"><?php echo callForData('users', 'user', true) . " " . callForData('users', 'secondName', false) . " " . callForData('users', 'lastName', true) . " " . callForData('users', 'secondLastName', false)?></div>
                </div>
            </div>
            <form action="<?php echo htmlspecialchars('/dashboard/settings'); ?>" method="POST">
                <div class="firstInfo">
                    <div>
                        <label for="name">Nombre <span class="asterisk">*</span></label>
                        <input type="text" id="name" name="name" required value="<?php echo callForData('users', 'user', true); ?>">
                    </div>
                    <div class="margin-left">
                        <label for="secondName">Segundo nombre</label>
                        <input type="text" id="secondName" name="secondName" value="<?php echo callForData('users', 'secondName', false); ?>">
                    </div>
                    <div class="margin-left">
                        <label for="lastName">Primer Apellido <span class="asterisk">*</span></label>
                        <input type="text" id="lastName" name="lastName" required value="<?php echo callForData('users', 'lastName', true); ?>">
                    </div>
                    <div class="margin-left">
                        <label for="secondLastName">Segundo Apellido</label>
                        <input type="text" id="secondLastName" name="secondLastName" value="<?php echo callForData('users', 'secondLastName', false); ?>">
                    </div>
                </div>
                <div class="secondInfo">
                    <label for="email">Correo Electrónico <span class="asterisk">*</span></label><br>
                    <input type="text" id="email" name="email" required value="<?php echo callForData('users', 'email', true); ?>">
                </div>
                <?php if(!empty($userErrors)): ?>
				<div style="display:flex;">
					<?php echo $userErrors; ?>
				</div>
				<?php endif; ?>	
                <button type="submit" name="firstInfoSubmit"><b>Guardar</b> Detalles Básicos</button>
            </form>
        </section>
        <section class="separate">
            <h1>Detalles Avanzados del Usuario</h1>
            <form action="<?php echo htmlspecialchars('/dashboard/settings'); ?>" method="POST">
                <div class="secondInfo">
                    <label for="organization">Organización</label><br>
                    <input type="text" id="organization" name="organization" value="<?php echo callForData('usersLocation', 'organization', false); ?>">
                </div>
                <div class="firstInfo">
                    <div>
                        <label for="country">País <span class="asterisk">*</span></label>
                        <input type="text" id="country" name="country" required value="<?php echo callForData('usersLocation', 'country', true); ?>">
                    </div>
                    <div class="margin-left">
                        <label for="state">Estado / Región / Provincia <span class="asterisk">*</span></label>
                        <input type="text" id="state" name="state" required value="<?php echo callForData('usersLocation', 'state', true); ?>">
                    </div>
                    <div class="margin-left">
                        <label for="zipCode">Código postal <span class="asterisk">*</span></label>
                        <input type="text" id="zipCode" name="zipCode" required min="0" max="99999" value="<?php echo callForData('usersLocation', 'zipCode', true); ?>">
                    </div>
                </div>
                <div class="firstInfo">
                    <div>
                        <label for="city">Ciudad <span class="asterisk">*</span></label>
                        <input type="text" id="city" name="city" required value="<?php echo callForData('usersLocation', 'city', true); ?>">
                    </div>
                    <div class="margin-left">
                        <label for="domicile">Domicilio <span class="asterisk">*</span></label>
                        <input type="text" id="domicile" name="domicile" required value="<?php echo callForData('usersLocation', 'domicile', true); ?>">
                    </div>
                </div>
                <?php if(!empty($secondUsersErrors)): ?>
				<div style="display:flex;">
					<?php echo $secondUsersErrors; ?>
				</div>
				<?php endif; ?>	
                <button type="submit" name="secondInfoSubmit"><b>Guardar</b> Detalles Avanzados</button>
            </form>
        </section>
        <section class="separate">
            <h1><i class="fa-solid fa-shield"></i> Seguridad</h1>
            <form action="<?php echo htmlspecialchars('/dashboard/settings'); ?>" method="POST">
                <div class="firstInfo">
                    <div>
                        <label for="actualPassword">Contraseña Actual <span class="asterisk">*</span></label>
                        <input type="password" id="actualPassword" name="actualPassword" required>
                    </div>
                    <div class="margin-left">
                        <label for="newPassword">Nueva Contraseña <span class="asterisk">*</span></label>
                        <input type="password" id="newPassword" name="newPassword" required>
                    </div>
                    <div class="margin-left">
                        <label for="newPassword2">Nueva Contraseña 2 <span class="asterisk">*</span></label>
                        <input type="password" id="newPassword2" name="newPassword2" required>
                    </div>
                </div>
                <?php if(!empty($passErrors)): ?>
				<div style="display:flex;">
					<?php echo $passErrors; ?>
				</div>
				<?php endif; ?>	
                <button type="submit" name="securitySubmit"><b>Guardar</b> Seguridad</button>
            </form>
            <?php if($result['role'] != '-1') : ?>
            <form action="<?php echo htmlspecialchars('/dashboard/settings'); ?>" method="POST">
                <button id="displayModal" type="reset" style="background: #363cff; position:static; margin-top: 10px;">Activar <b style="font-weight: 600;">verificacíon en dos pasos</b> (Totp)</button>
            </form>
            <?php endif; ?>
        </section>
        <?php if($result['role'] != '-1') :  //Oculta el código?>
        <!-- <section class="separate link">
            <h1><i class="fa-solid fa-link"></i> Enlazar cuenta</h1>
            <div>
                <div class="item">
                    <i class="fa-brands fa-google"></i>
                    <h2>Google</h2>
                    <a href="">Enlazar</a>
                </div>
                <div class="item">
                    <i class="fa-brands fa-microsoft"></i>
                    <h2>Microsoft</h2>
                    <a href="">Enlazar</a>
                </div>
                <div class="item">
                    <i class="fa-brands fa-github"></i>
                    <h2>Github</h2>
                    <a href="">Enlazar</a>
                </div>
                <div class="item">
                    <i class="fa-brands fa-discord"></i>
                    <h2>Discord</h2>
                    <a href="">Enlazar</a>
                </div>
            </div>
        </section> -->
        <?php endif; // Oculta el código ?>
    </main>
    <?php if($result['role'] != '-1') : ?>
    <div class="modal" id="modal">
        <form action="<?php echo htmlspecialchars('/dashboard/settings'); ?>" method="post">
            <h1 style="font-size: 35px;">Crear ticket</h1>
            <div>
                <p>Escanea el código QR en tu aplicación <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2">Google authenticator</a>, <a href="https://support.microsoft.com/es-es/account-billing/descargar-microsoft-authenticator-351498fc-850a-45da-b7b6-27e523b8702a">Microsoft authenticator</a>, <a href="https://getaegis.app/">Aegis authenticator (Android)</a> y <a href="https://www.authy.com/">Authy authenticator (recomendado IOS)</a></p>
            </div>
            <div>
                <img src="<?php echo $qrCodeUrl;?>" alt="QR de 2fa">
            </div>
            <div>
                <input type="text" name="totpCode" id="totpCode" style="width:150px" required>
            </div>
            <div>
                <span id="exitModal">Salir</span>
                <button type="submit" name="codeTotpSubmit">Guardar</button>
            </div>
        </form>
    </div>
    <div class="overlay" id="overlay">a</div>
    <script>
        let modal = document.getElementById("modal");
        let overlay = document.getElementById("overlay");
        let exitModal = document.getElementById("exitModal");
        document.getElementById("displayModal").addEventListener("click", function() {
            modal.style.display = "block";
            overlay.style.display = "block";
        });
        overlay.addEventListener("click", function() {
            modal.style.display = "none";
            overlay.style.display = "none";
        });
        exitModal.addEventListener("click", function() {
            modal.style.display = "none";
            overlay.style.display = "none";
        });
    </script>
    <?php endif; ?>
</body>
</html>