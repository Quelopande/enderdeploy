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
    <meta property="og:site_name" content="©RenderCores"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="EnderDeploy - Dashboard"/>
    <meta property="og:url" content="https://deploy.enderhosting.com.mx"/>
    <meta property="og:image" content="/assets/img/logo.png"/>
    <meta property="og:image:width" content="540"/>
    <meta property="og:image:height" content="520"/>
    <title>Enderdeploy dashboard</title>
    <link rel="website icon" type="ico" href="/assets/img/logo.ico">
    <link rel="stylesheet" href="/assets/styles/menu.css">
    <link rel="stylesheet" href="/assets/styles/dashboard/verify.css">
    <script src="/assets/js/menu.js" defer></script>
    <link href="https://pro.fontawesome.com/releases/v6.0.0-beta1/css/all.css" rel="stylesheet"/>
</head>
<body>
    <?php require "menutemplate.view.php";?>
    <main class="separate">
        <div class="serverStatus separate"><i class="fa-solid fa-leaf"></i> Todos nuestros servicios estan funcionales, disfruta sin problemas</div>
        <div class="verifyContainer">
            <div>
                <h1>Verifica tu cuenta</h1>
                <p>Para continuar, verifica tu cuenta ingresando el código de verificación que te hemos enviado a tu correo electrónico.</p>
                <form class="verifyForm" action="<?php echo htmlspecialchars('/dashboard/verify'); ?>" method="post" validate>
                    <label for="verificationCode">Código de verificación</label>
                    <input type="text" id="verificationCode" name="verificationCode" required>
                    <button type="verifationCodeSubmit">Verificar</button>           
                </form>
                <form class="resendForm" action="<?php echo htmlspecialchars('/dashboard/verify'); ?>" method="post">
                    <p>No has recibido el código?</p>
                    <button type="submit" class="resendVerificationCode"> Reenviar código</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>