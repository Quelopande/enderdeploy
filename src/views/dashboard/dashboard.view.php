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
        <?php
            function getFullName() {
                global $connection, $id;
                $stmt = $connection->prepare('SELECT `user`, `secondName`, `lastName`, `secondLastName` FROM users WHERE id = :id LIMIT 1');
                $stmt->execute([':id' => $id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$row) return 'Usuario';
                $parts = [];
                if (!empty($row['user'])) $parts[] = $row['user'];
                if (!empty($row['secondName'])) $parts[] = $row['secondName'];
                if (!empty($row['lastName'])) $parts[] = $row['lastName'];
                if (!empty($row['secondLastName'])) $parts[] = $row['secondLastName'];
                $name = trim(implode(' ', $parts));
                return htmlspecialchars(ucwords($name), ENT_QUOTES, 'UTF-8');
            }

            function getFullData(){
                global $connection, $id;
                $stmt = $connection->prepare('SELECT su.subscriptionName, su.subscriptionStatus, su.subscriptionStartTime, su.subscriptionExpirationTime, se.serviceName, se.durationDays
                FROM subscriptions su 
                INNER JOIN services se ON su.serviceId = se.serviceId 
                WHERE su.userId = :id');
                $stmt->execute([':id' => $id]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $rows;
            }
        ?>
        <section class = "dashboard">
            <div class="dashboard__content">
                <div class="welcome">
                    ¡Bienvenido de vuelta <?php echo getFullName(); ?>!
                </div>
                <div class ="dashboard__actions">
                    <a href="/dashboard/support" class="action-btn">
                        <i class="fa-regular fa-life-ring"></i>
                        <span>Ir al soporte</span>
                    </a>

                    <a href="https://uptime.rendercores.online/" class="action-btn">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Ir a estado servicio</span>
                    </a>

                    <a href="/dashboard/settings" class="action-btn">
                        <i class="fa-solid fa-gear"></i>
                        <span>Ir a opciones</span>
                    </a>
                    
                </div>
                <!-- Data Table Section -->
                <div> 
                    <div class="card">
                        <h1 class="containers">Contenedores</h1>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Contenedor</th>
                                    <th>Estado Actual</th>
                                    <th>Fecha de Inicio</th>
                                    <th>Fecha de Expiración</th>
                                    <th>Nombre servicio</th>
                                    <th>Duracion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get data from database
                                $containers = getFullData();
                                
                                if (empty($containers)) {
                                    echo '<tr><td colspan="6" class="empty-state">No tienes contenedores activos</td></tr>';
                                } else {
                                    foreach ($containers as $container) {
                                        $statusClass = strtolower($container['subscriptionStatus']) === 'activo' ? 'status-active' : 'status-inactive';
                                        echo '<tr>';
                                        echo '<td class="container-name">' . htmlspecialchars($container['subscriptionName']) . '</td>';
                                        echo '<td><span class="status-badge ' . $statusClass . '">' . htmlspecialchars($container['subscriptionStatus']) . '</span></td>';
                                        echo '<td>' . htmlspecialchars($container['subscriptionStartTime']) . '</td>';
                                        echo '<td>' . htmlspecialchars($container['subscriptionExpirationTime']) . '</td>';
                                        echo '<td>' . htmlspecialchars($container['serviceName']) . '</td>';
                                        echo '<td>' . htmlspecialchars($container['durationDays']) . '</td>';
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>