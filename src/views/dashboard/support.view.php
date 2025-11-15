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
    <link rel="stylesheet" href="/assets/styles/dashboard/support.css">
    <link rel="stylesheet" href="/assets/styles/modal.css">
    <script src="/assets/js/menu.js" defer></script>
    <link href="https://pro.fontawesome.com/releases/v6.0.0-beta1/css/all.css" rel="stylesheet"/>
</head>
<body data-page-id="2">
    <?php require "menutemplate.view.php";?>
    <main class="separate">
        <div class="serverStatus separate"><i class="fa-solid fa-leaf"></i> Todos nuestros servicios estan funcionales, disfruta sin problemas</div>
        <div class="containers separate">
            <div class="container" id="displayModal">
                <h2><i class="fa-sharp-duotone fa-light fa-plus"></i> Crear ticket</h2>
                <h3>Crear un ticket para comunicar tus dudas o problemas con nuestro equipo de soporte. Te responderemos cuanto antes.</h3>
            </div>
            <h1>Tickets abiertos</h1>
            <?php foreach ($messages as $message): ?>
                <a class="container" href="/dashboard/supportMsg?msg=<?php echo htmlspecialchars($message['messageId'], ENT_QUOTES, 'UTF-8'); ?>">
                    <h2><?php echo htmlspecialchars($message['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($message['content'], ENT_QUOTES, 'UTF-8')); ?></p>
                    <?php $creationTime = new DateTime($message['creationTime']);?>
                    <small>Publicado el: <?php echo htmlspecialchars($creationTime->format('d/m/Y H:i:s'), ENT_QUOTES, 'UTF-8'); ?></small>
                </a>
            <?php endforeach; ?>
        </div>
    </main>
    <div class="modal" id="modal">
        <form action="<?php echo htmlspecialchars('/dashboard/support'); ?>" method="post">
            <h1>Crear ticket</h1>
            <div>
                <label for="title">Título</label><br>
                <input type="text" name="title" id="title" required>
            </div>
            <div>
                <label for="content">Describa el problema</label><br>
                <textarea name="content" id="content" required></textarea>
            </div>
            <div>
                <button id="exitModal">Salir</button>
                <button type="submit">Enviar</button>
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
</body>
</html>