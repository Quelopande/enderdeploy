<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Twitter card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="EnderDeploy - Service Management">
    <meta name="twitter:image" content="/assets/img/logo.png">
    <!-- Facebook & discord -->
    <meta property="og:locale" content="es"/>
    <meta property="og:site_name" content="©RenderCores"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="EnderDeploy - Service Management"/>
    <meta property="og:url" content="https://rendercores.com"/>
    <meta property="og:image" content="/assets/img/logo.png"/>
    <meta property="og:image:width" content="540"/>
    <meta property="og:image:height" content="520"/>
    <title>Enderdeploy - Service Management</title>
    <link rel="website icon" type="ico" href="/assets/img/logo.ico">
    <link rel="stylesheet" href="/assets/styles/menu.css">
    <link rel="stylesheet" href="/assets/styles/modal.css">
    <link rel="stylesheet" href="/assets/styles/dashboard/components/pricingTable.css">
    <link rel="stylesheet" href="/assets/styles/dashboard/serviceManagement.css">
    <script src="/assets/js/menu.js" defer></script>
    <link href="https://pro.fontawesome.com/releases/v6.0.0-beta1/css/all.css" rel="stylesheet"/>
</head>
<body data-page-id="4">
    <?php require "menutemplate.view.php";?>
    <main class="separate">
        <div class="serverStatus separate"><i class="fa-solid fa-leaf"></i> Todos nuestros servicios estan funcionales, disfruta sin problemas</div>
        <div class="serviceManagementContainer separate">
            <div class="serviceManagementBox">
                <h3>Plan y Billing</h3>
                <div>
                    <?php if($showBtn === true): ?><a id="displayModal" style="--btn: <?php echo htmlspecialchars($btnColor) ?>; --btn-hover: <?php echo htmlspecialchars($btnSecColor) ?>;"><?php echo htmlspecialchars($btnTxt) ?> plan</a> <?php endif; ?>
                    <a href="<?php echo htmlspecialchars($billingPortalSessionLink)?>" style="--btn: #ffffff; --btn-hover: #d4d4d4;">Panel de facturación</a>
                </div>
            </div>
            <div class="serviceManagementInformationTitle">
                <h3>Plan Actual</h3>
                <?php if($subscriptionResult['serviceId'] !== 3): ?>
                <a href="<?php echo htmlspecialchars('/dashboard/serviceManagement?subscriptionId=' . $subscriptionId . '&changePlan=1'); ?>" style="all: unset;"><span>Cambiar plan</span></a>
                <?php endif; ?>
            </div>
            <div class="serviceManagementInformationBox">
                <div class="serviceManagementInformationInnerBox">
                    <div class="serviceManagementInformationInnerBoxIcon">
                        <p>Plan Mensual</p>
                        <span style="--notify: <?php echo htmlspecialchars($notifyColor) ?>; --notify-strong: <?php echo htmlspecialchars($notifySecColor) ?>;">◉ <?php echo htmlspecialchars($notifyTxt) ?></span>
                    </div>
                    <h2><?php echo htmlspecialchars(getServiceData($subscriptionResult['serviceId'], 'serviceName', $connection)) ?> / mes</h2>      
                </div> 
                <div class="serviceManagementInformationInnerBox">
                    <div class="serviceManagementInformationInnerBoxIcon">
                        <p>Próximo ciclo de facturación</p>
                    </div>
                    <h2><?php echo htmlspecialchars($formattedDate) ?></h2>      
                </div> 
            </div>
        </div>
    </main>
    <div class="modal" id="modal">
        <?php if($stripeStatus === 'active'): ?>
        <form action="<?php echo htmlspecialchars('/dashboard/serviceManagement?subscriptionId=' . $subscriptionId); ?>" method="post" name="subscriptionStatusUpdateBtn">
            <h1><?php echo htmlspecialchars($btnTxt) ?> plan</h1>
            <p>Esto es un panel para verificar y autorificar que va a <span style="text-transform: lowercase;"><?php echo htmlspecialchars($btnTxt) ?></span> su plan.</p>
            <div>
                <button id="exitModal">Cancelar</button>
                <button type="submit" name="subscriptionStatusUpdateBtn" style="--btn: <?php echo htmlspecialchars($btnColor) ?>; --btn-hover: <?php echo htmlspecialchars($btnSecColor) ?>;"><?php echo htmlspecialchars($btnTxt) ?> plan</button>
            </div>
        </form>
        <?php elseif($stripeStatus === 'past_due' || $stripeStatus === 'unpaid'): ?>
        <div class="modalContent">
            <h1><?php echo htmlspecialchars($btnTxt) ?> plan</h1>
            <p>No se puede realizar este acción desde este panel, para poder siguer adelante con los pagos tendra que ir a nuestro <a class="inTxtLink" href="<?php echo htmlspecialchars($billingPortalSessionLink)?>">Panel de facturación</a>. Dentro de este panel vaya y haga clic en el botón azul que figura al lado derecho de su subscripción y que tiene el texto <b><i>Pagar importe adeudado</i></b>.</p>
            <div>
                <button id="exitModal">Cancelar</button>
                <button onclick="window.location='<?php echo htmlspecialchars($billingPortalSessionLink)?>';">Portal de facturación</button>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php if($_GET['changePlan'] == 1): ?>
        <div class="modal" style="display: block !important;">
            <form action="<?php echo htmlspecialchars('/dashboard/serviceManagement?subscriptionId=' . $subscriptionId); ?>" method="post" name="subscriptionStatusUpdateBtn">
                <h1>Mejorar plan</h1>
                <section class="pricingTable">
                    <?php foreach ($plans as $plan):?>
                        <div class="plan">
                            <p class="price">$<?php echo htmlspecialchars($plan['price']);?> MXN/mes (IVA incluido)</p>
                            <ul>
                                <?php foreach ($plan['features'] as $feature): ?>
                                    <li>
                                        <?php echo htmlspecialchars($feature['main']); ?> 
                                        <?php if (!empty($feature['note'])): ?>
                                            <span class="note">
                                                <?php echo htmlspecialchars($feature['note']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <label class="btna"><input type="radio" name="selectedPlan" class="selectedPlan" value="<?php echo htmlspecialchars($plan['planId']); ?>" required>Seleccionar</label>
                        </div>
                    <?php endforeach; ?>
                </section>
                <div>
                    <button onclick="window.location='<?php echo htmlspecialchars('/dashboard/serviceManagement?subscriptionId=' . $subscriptionId); ?>';">Cancelar</button>
                    <button type="submit" name="subscriptionPlanUpdateBtn" style="--btn: #c2ffbc; --btn-hover: #0c9100;">Mejorar plan</button>
                </div>
            </form>
        </div>
        <div class="overlay" style="display: block !important;" onclick="window.location='<?php echo htmlspecialchars('/dashboard/serviceManagement?subscriptionId=' . $subscriptionId); ?>';"></div>
    <?php endif; ?>
    <div class="overlay" id="overlay"></div>
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