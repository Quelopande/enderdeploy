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
    <meta property="og:url" content="https://rendercores.com"/>
    <meta property="og:image" content="/assets/img/logo.png"/>
    <meta property="og:image:width" content="540"/>
    <meta property="og:image:height" content="520"/>
    <title>Enderdeploy dashboard</title>
    <link rel="website icon" type="ico" href="/assets/img/logo.ico">
    <link rel="stylesheet" href="/assets/styles/menu.css">
    <link rel="stylesheet" href="/assets/styles/dashboard/supportMsg.css">
    <script src="/assets/js/menu.js" defer></script>
    <link href="https://pro.fontawesome.com/releases/v6.0.0-beta3/css/all.css" rel="stylesheet"/>
</head>
<body data-page-id="2">
    <?php require "menutemplate.view.php";?>
    <main class="separate">
        <section class="top separate">
            <a class="btn" href="/dashboard/support"><i class="fa-solid fa-circle-left"></i> Atrás</a>
            <form action="<?php echo htmlspecialchars('/dashboard/supportMsg?msg=' . $messageId); ?>" method="post">
                <button id="answerDelete" name="answerDelete" class="btn"><i class="fa-solid fa-hexagon-xmark"></i> Cerrar ticket</button>
            </form>
        </section>
        <section class="problemExplanation">
            <h1><?php echo $eresult['title'] ?></h1>
            <p><?php echo $eresult['content'] ?></p>
            <?php if($result['role'] != '-1'){    echo '<a href="/staffPanel/user?userId=' . $eresult['userId'] . '" target="_blank">Datos del usuario</a>';}?>
        </section>
        <h2>Todas las respuestas</h2>
        <section class="answers">
             <?php foreach ($answers as $answer): ?>
                <?php $answerPublisherId = $answer['answerPublisherId'];
                $pubStatement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
                $pubStatement->execute(array(':id' => $answerPublisherId));
                $pubResult = $pubStatement->fetch();
                ?>
                <div class="answer <?php if($pubResult['role'] != '-1'){echo "staff";}?>">
                    <p class="author"><?php echo $pubResult['user']; if($pubResult['role'] != '-1'){echo "<span>Staff</span>";}?></p>
                    <p><?php echo $answer['message'];?></p>
                </div>
            <?php endforeach; ?>
            <form action="<?php echo htmlspecialchars('/dashboard/supportMsg?msg=' . $messageId); ?>" method="post">
                <input type="text" name="postAnswer" id="postAnswer" required placeholder="Respuesta">
                <button type="submit" name="answerInsert" id="answerInsert">Enviar</button>
                <p style="margin-left: 20px;"><b>Alerta:</b> Los mensajes no se pueden borrar.</p>
            </form>
        </section>
    </main>
</body>
</html>