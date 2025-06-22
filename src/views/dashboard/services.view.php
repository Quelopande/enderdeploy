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
    <link rel="stylesheet" href="/assets/styles/modal.css">
    <link rel="stylesheet" href="/assets/styles/dashboard/services.css">
    <script src="/assets/js/menu.js" defer></script>
    <link href="https://pro.fontawesome.com/releases/v6.0.0-beta1/css/all.css" rel="stylesheet"/>
</head>
<body data-page-id="4">
    <?php require "menutemplate.view.php";?>
    <main class="separate">
        <div class="serverStatus separate"><i class="fa-solid fa-leaf"></i> Todos nuestros servicios estan funcionales, disfruta sin problemas</div>
        <div class="displayModal" id="displayModal"><i class="fa-sharp-duotone fa-light fa-plus"></i> <p>Agregar servicio</p></div>
        <a class="billingPortal" href="<?php echo $billingPortalSessionLink->url ?>" target="_blank">Accede al panel de facturación</a>
        <div class="containers">
            <?php foreach ($subscriptions as $subscription): ?>
            <a class="container" href="https://<?php echo htmlspecialchars($subscription['serviceName'], ENT_QUOTES, 'UTF-8'); ?>.enderhosting.com.mx">
                <h2><?php echo htmlspecialchars($subscription['serviceName'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p>Versión: <?php echo ucfirst(htmlspecialchars($subscription['serviceVersion'], ENT_QUOTES, 'UTF-8')); ?></p>
                <?php $creationTime = new DateTime($subscription['creationTime']);?>
                <small>Creado el: <?php echo htmlspecialchars($creationTime->format('d/m/Y H:i:s'), ENT_QUOTES, 'UTF-8'); ?></small>
            </a>
            <?php endforeach; ?>
        </div>
        <?php if(!empty($serviceErrors)): ?>
        <div>
		    <ul><?php echo $serviceErrors; ?></ul>
		</div>
        <?php endif; ?>	
    </main>
    <div class="modal" id="modal">
        <form action="<?php echo htmlspecialchars('/dashboard/services'); ?>" method="post">
            <h1>Crear servicio</h1>
            <div class="first">
                <input type="text" name="serviceName" id="serviceName" required>
                <p>.enderdeploy.space</p>
            </div>
            <select name="serviceVersion" id="serviceVersion" required>
                <option value="endersuit">EnderSuit | Versión estable</option>
            </select>
            <section class="pricingTable">
                <?php foreach ($planes as $plan): ?>
                    <div class="plan">
                        <p class="price">$<?php echo $plan['price'];?> MXN/mes (IVA incluido)</p>
                        <ul>
                            <?php foreach ($plan['features'] as $caracteristica): ?>
                                <li><?php echo $caracteristica; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <label class="btna"><input type="radio" name="selectedPlan" class="selectedPlan" value="<?php echo $plan['planId']; ?>" required>Seleccionar</label>  
                    </div>
                <?php endforeach; ?>
            </section>
            <div class="legalSelect">
                <input type="checkbox" name="acceptLaws" id="acceptLaws" required>
                <label for="acceptLaws">Acepto que las leyes de la región (Tepic, Nayarit) seran aplicables a mi y a EnderDeploy.</label>
            </div>
            <div class="legalSelect">
                <input type="checkbox" name="shareData" id="shareData" required>
                <label for="shareData">Estoy de acuerdo si mis datos se comparten con el socio local. (EnderHosting & EnderDeploy)</label>
            </div>
            <div>
                <button id="exitModal">Salir</button>
                <button type="submit">Comprar</button>
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