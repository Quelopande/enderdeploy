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
    <link rel="stylesheet" href="/assets/styles/dashboard/dashboard.css">
    <link rel="stylesheet" href="/assets/styles/modal.css">
    <script src="/assets/js/menu.js" defer></script>
    <link href="https://pro.fontawesome.com/releases/v6.0.0-beta1/css/all.css" rel="stylesheet"/>
</head>
<body data-page-id="1">
    <?php require APP_ROOT . 'src/views/dashboard/menutemplate.view.php';?>
    <main class="separate">
        
        <section class = "dashboard">
            <div class="dashboardContent">
                <div class="welcome">
                    ¡Bienvenido de vuelta <?php echo ucfirst(htmlspecialchars($result['user'], ENT_QUOTES, 'UTF-8')) ?>!
                </div>
                <div class ="dashboardActions">
                    <a href="/dashboard/support" class="actionBtn">
                        <i class="fa-regular fa-life-ring"></i>
                        <span>Ir al soporte</span>
                    </a>

                    <a href="https://uptime.rendercores.online/" class="actionBtn">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Ir a estado servicio</span>
                    </a>

                    <a href="/dashboard/settings" class="actionBtn">
                        <i class="fa-solid fa-gear"></i>
                        <span>Ir a opciones</span>
                    </a>
                    
                </div>
                <!-- Data Table Section -->
                <div>   
                    <h1 class="containers">Contenedores</h1>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Contenedor</th>
                                <th>Estado Actual</th>
                                <th>Fecha de Inicio</th>
                                <th>Fecha de Expiración</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Get data from index.php
                            $containers = $subscriptions;
                            
                            if (empty($containers)) {
                                echo '<tr><td colspan="6" class="empty-state">No tienes contenedores activos</td></tr>';
                            } else {
                                foreach ($containers as $container) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($container['subscriptionName'], ENT_QUOTES, 'UTF-8') . '</td>';
                                    echo '<td>' . htmlspecialchars($container['subscriptionStatus'], ENT_QUOTES, 'UTF-8') . '</td>';
                                    echo '<td>' . htmlspecialchars($container['subscriptionStartTime'], ENT_QUOTES, 'UTF-8') . '</td>';
                                    echo '<td>' . htmlspecialchars($container['subscriptionExpirationTime'], ENT_QUOTES, 'UTF-8') . '</td>';
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</body>
</html>