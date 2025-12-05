<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enderdeploy - Staff Panel [Confidencial]</title>
    <link rel="stylesheet" href="/assets/styles/staffPanel/subscriptions.css">
    <link rel="website icon" type="ico" href="/assets/img/logo.ico">
</head>
<body>
    <div class="menu">
        <h2><a href="/staffPanel" style="text-decoration: none;">Staff Panel | <b style="background:red; padding: 2px 5px;">SENSIBLE</b></a></h2>
        <div>
            <a href="/staffPanel/tickets">Tickets</a>
            <a href="/staffPanel/subscriptions">Servicios</a>
            <a href="/staffPanel/users">Usuarios</a>
        </div>
    </div>
    <?php if(isset($_GET['userId'])): ?>
        <h1>Servicios del usuario <?php htmlspecialchars($_GET['userId']); ?></h1>
        <?php foreach($subscriptionsResult as $subscription): ?>
            <a href="/staffPanel/subscriptionManagement?<?php echo htmlspecialchars();?>" class="subscriptionCard">

            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <h1>Buscar servicios de un usuario</h1>
        <form action="<?php echo htmlspecialchars('/staffPanel/subscriptions'); ?>" method="get">
            <input type="text" name="userId" placeholder="ID del usuario" required>
            <button type="submit">Buscar</button>
        </form>
    <?php endif; ?>
</body>
</html>