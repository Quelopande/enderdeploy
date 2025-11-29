<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <!-- Twitter card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="EnderDeploy - Registro">
    <meta name="twitter:description" content="EnderDeploy es una plataforma avanzada de despliegue de aplicaciones SaaS y administración de infraestructura como servicio (IaaS), diseñada para optimizar la implementación y gestión de soluciones tecnológicas. Desarrollada por EnderHosting, proporciona un entorno flexible y escalable para facilitar el crecimiento de tu empresa.">
    <meta name="twitter:image" content="/assets/img/logo.png">
    <!-- Facebook & discord -->
    <meta property="og:locale" content="es"/>
    <meta property="og:site_name" content="©RenderCores"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="EnderDeploy - Registro"/>
    <meta property="og:description" content="EnderDeploy es una plataforma avanzada de despliegue de aplicaciones SaaS y administración de infraestructura como servicio (IaaS), diseñada para optimizar la implementación y gestión de soluciones tecnológicas. Desarrollada por EnderHosting, proporciona un entorno flexible y escalable para facilitar el crecimiento de tu empresa."/>
    <meta property="og:url" content="https://rendercores.com"/>
    <meta property="og:image" content="/assets/img/logo.png"/>
    <meta property="og:image:width" content="540"/>
    <meta property="og:image:height" content="520"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/styles/auth.css">
    <title>EnderDeploy | Registrarse</title>
    <link rel="website icon" type="ico" href="/assets/img/logo.ico">
    <style>
        :root{
            --main: #1d5947;
            --main-hover: #208b48;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="info-section">
            <img src="/assets/img/logo.png" alt="Logo" class="logo-image">
            <h2>Bienvenido a EnderDeploy!</h2>
            <p class="info-text">Empieza en EnderDeploy ya! Regístrate para obtener acceso a tu panel y para poder gestionar tu información.</p>
        </div>
        <div class="form-section">
            <h1>Regístrate</h1>
            <form  action="<?php echo htmlspecialchars('/auth/signup'); ?>" method="POST" id="usuario-form" validate>
                <div class="form-group">
                    <label for="email">Correo:</label>
                    <input type="email" name="email" id="email" class="input-text" placeholder="Ingresa tu correo" required maxlength="254">
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" name="password" id="password" class="input-text" placeholder="Ingresa tu contraseña" required minlength="8">
                </div>
                <div class="form-group">
                    <input type="password" name="password2" id="password2" class="input-text" placeholder="Repite la contraseña" required minlength="8">
                </div>
                <label class="container">
                    <input type="checkbox" required name="agree">
                    <svg viewBox="0 0 64 64" height="20px" width="20px"><path d="M 0 16 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 16 L 32 48 L 64 16 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 16" pathLength="575.0541381835938" class="path"></path></svg>
                    <p>He leído y acepto los <a href="/terms" target="_blank">Términos de Servicio</a> y la <a href="/privacy" target="_blank">Política de Privacidad</a>.</p>
                </label>
                <?php
                if (!empty($errors) && is_array($errors)): ?>
                    <div style="color: red; margin-bottom: 10px;">
                        <?php 
                        foreach ($errors as $error) {
                            echo '<p>' . htmlspecialchars($error) . '</p>';
                        }
                        ?>
                    </div>
                <?php endif; ?>
                <button type="submit" class="login-button">Registrarse</button>
            </form>
            <p class="register-link">¿Ya tienes una cuenta? <a href="../auth/signin">Inicia Sesión</a></p>
        </div>
    </div>
</body>
</html>