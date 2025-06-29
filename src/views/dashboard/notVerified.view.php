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
    <script src="/assets/js/menu.js" defer></script>
    <link href="https://pro.fontawesome.com/releases/v6.0.0-beta1/css/all.css" rel="stylesheet"/>
</head>
<body>
    <?php require "menutemplate.view.php";?>
    <main class="separate" style="text-align: center; margin-top: 20px;">
        <h1>Verificación de cuenta</h1>
        <p>Tu cuenta aún no ha sido verificada. Por favor, verifica tu cuenta para acceder a todas las funcionalidades del panel.</p>
        <a href="/dashboard/verify" style="padding: 10px 20px; background: black; color: white; text-decoration: none; border-radius: 16px;">Verificar cuenta</a>
    </main>
</body>
</html>