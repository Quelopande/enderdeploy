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
    ?>
    <div class = "dashboard">
        <div class="dashboard welcome">
            ¡Bienvenido de vuelta <?php echo getFullName(); ?>!
        </div>
        <div class ="dashboard dashboard__actions">
            <a href="/dashboard/support" class="dashboard action-btn"><li id="2"><i class="fa-regular fa-life-ring"></i> <p class="overTxt">Ir al soporte</p></li></a>
            <a href="https://uptime.rendercores.online/" class="dashboard action-btn"><li id="3"><i class="fa-solid fa-chart-line"></i> <p>Ir a estado servicio</p></li></a>
            <a href="/dashboard/settings" class="dashboard action-btn"><li id="5"><i class="fa-solid fa-gear"></i> <p class="overTxt">Ir a opciones</p></li></a>
            
        </div>
        <!-- Data Table Section -->
        <div>
            <div>
                <h2 class="containers">Contenedores</h2>
            </div>
            <div class="card-content">
                <div class="card">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Container</th>
                                <th>Estado Actual</th>
                                <th>Fecha de Expiración</th>
                                <th>Versión</th>
                                <th>Software</th>
                                <th>Máquina</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // example
                            $containers = [
                                ['name' => 'minecraft-server-01', 'status' => 'Activo', 'expiration' => '2025-06-15', 'version' => '1.20.4', 'software' => 'Paper', 'machine' => 'Node-01'],
                                ['name' => 'minecraft-server-02', 'status' => 'Activo', 'expiration' => '2025-07-20', 'version' => '1.19.4', 'software' => 'Spigot', 'machine' => 'Node-02'],
                            ];
                            
                            if (empty($containers)) {
                                echo '<tr><td colspan="6" class="empty-state">No tienes contenedores activos</td></tr>';
                            } else {
                                foreach ($containers as $container) {
                                    $statusClass = strtolower($container['status']) === 'activo' ? 'status-active' : 'status-inactive';
                                    echo '<tr>';
                                    echo '<td class="container-name">' . htmlspecialchars($container['name']) . '</td>';
                                    echo '<td><span class="status-badge ' . $statusClass . '">' . htmlspecialchars($container['status']) . '</span></td>';
                                    echo '<td>' . htmlspecialchars($container['expiration']) . '</td>';
                                    echo '<td>' . htmlspecialchars($container['version']) . '</td>';
                                    echo '<td>' . htmlspecialchars($container['software']) . '</td>';
                                    echo '<td>' . htmlspecialchars($container['machine']) . '</td>';
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>