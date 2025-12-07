<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enderdeploy - Staff Panel [Confidencial]</title>
    <link rel="stylesheet" href="/assets/styles/staffPanel/subscriptionManagement.css">
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
    <div class="subscriptionData">
        <div class="noEditableSection">
            <div>
                <h2>Información NO editable</h2>
                <p>Se obtiene desde la DB, hay posibilidades que se haya sincronizado mal. En caso de detectar un dato erroneo deberás de informar al departamento correspondiente.</p>
            </div>
            <div class="noEditableData">
                <div>
                    <h3>ID interna</h3>
                    <p><?php echo htmlspecialchars($subscriptionId); ?></p>
                </div>
                <div>
                    <h3>ID Stripe</h3>
                    <p><?php echo htmlspecialchars($subscriptionResult['subscriptionStripeId']); ?></p>
                </div>
                <div>
                    <h3>Fecha de inicio</h3>
                    <p><?php echo htmlspecialchars($subscriptionResult['subscriptionStartTime']); ?></p>
                </div>
                <div>
                    <h3>Próximo periodo de facturación</h3>
                    <p><?php echo htmlspecialchars($subscriptionResult['subscriptionExpirationTime']); ?></p>
                </div>
                <div>
                    <h3>Estado (Interno)</h3>
                    <p><?php echo htmlspecialchars($subscriptionResult['subscriptionStatus']); ?></p>
                </div>
            </div>
        </div>
        <div class="editableSection">
        </div>
    </div>
</body>
</html>