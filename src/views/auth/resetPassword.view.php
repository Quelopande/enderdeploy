<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <!-- Twitter card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="EnderDeploy - Inicio de sesión">
    <meta name="twitter:description" content="EnderDeploy es una plataforma avanzada de despliegue de aplicaciones SaaS y administración de infraestructura como servicio (IaaS), diseñada para optimizar la implementación y gestión de soluciones tecnológicas. Desarrollada por EnderHosting, proporciona un entorno flexible y escalable para facilitar el crecimiento de tu empresa.">
    <meta name="twitter:image" content="/assets/img/logo.png">
    <!-- Facebook & discord -->
    <meta property="og:locale" content="es"/>
    <meta property="og:site_name" content="©RenderCores"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="EnderDeploy - Inicio de sesión"/>
    <meta property="og:description" content="EnderDeploy es una plataforma avanzada de despliegue de aplicaciones SaaS y administración de infraestructura como servicio (IaaS), diseñada para optimizar la implementación y gestión de soluciones tecnológicas. Desarrollada por EnderHosting, proporciona un entorno flexible y escalable para facilitar el crecimiento de tu empresa."/>
    <meta property="og:url" content="https://rendercores.com"/>
    <meta property="og:image" content="/assets/img/logo.png"/>
    <meta property="og:image:width" content="540"/>
    <meta property="og:image:height" content="520"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/styles/auth.css">
    <title>EnderDeploy | Iniciar Sesión</title>
    <link rel="website icon" type="ico" href="/assets/img/logo.ico">
    <style>
        :root{
            --main: #383838ff;
            --main-hover: #797979ff;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="info-section">
            <img src="/assets/img/logo.png" alt="Logo" class="logo-image">
            <h2>Bienvenido a EnderDeploy!</h2>
            <p class="info-text">Recupera tu cuenta poniendo el email. Si el email existe en nuestra base de datos te enviaremos un email, donde se te facilitará una página para establecer tu nueva contraseña.</p>
        </div>
        <div class="form-section">
            <h1>Inicio de Sesión</h1>
            <form action="<?php echo htmlspecialchars('/auth/resetPassword' . '?' . $_SERVER['QUERY_STRING']);?>" method="POST" id="usuario-form" validate>                <div class="form-group">
                    <label for="newPassword">Contraseña:</label>
                    <input type="password" name="newPassword" id="newPassword" class="input-text" placeholder="Ingresa tu contraseña" required minlength="8">
                </div>
                <div class="form-group">
                    <label for="newPassword2">Repetir Contraseña:</label>
                    <input type="password" name="newPassword2" id="newPassword2" class="input-text" placeholder="Repite la contraseña" required minlength="8">
                </div>
                <?php if (!empty($errors) && is_array($errors)): ?>
                    <div style="color: red; margin-bottom: 10px;">
                        <?php 
                        foreach ($errors as $error) {
                            echo '<p>' . htmlspecialchars($error) . '</p>';
                        }
                        ?>
                    </div>
                <?php endif; ?>
                <button type="submit" class="login-button">Recuperar cuenta</button>
            </form>
            <p class="register-link">¿Tienes cuenta y sabes la contraseña? <a href="/auth/signin">Iniciar Sesión</a></p>
        </div>
    </div>
</body>
</html>