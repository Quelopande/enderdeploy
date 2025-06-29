<?php 
$statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
$statement->execute([':id' => $id]);
$result = $statement->fetch(PDO::FETCH_ASSOC);
$serviceErrors = [];

$planes = [
    [
        'planId' => '1',
        'price' => 499,
        'features' => [
            '10 usuarios <span>$49.9MXN por Usuario al mes adicional</span>',
            '1 GB <span>En Base de Datos</span>',
            '25 GB <span>En Almacenamiento (expansion gratuita)</span>',
            'Backups <span>Manuales</span>',
            'Certificado SSL <span>Incluido</span>',
            'Guia rapida y Documentacion <span>1 horas por mes</span>',
        ],
    ],
    [
        'planId' => '2',
        'price' => 799,
        'features' => [
            '20 usuarios <span>$39.9MXN por Usuario al mes adicional</span>',
            '2 GB <span>En Base de Datos</span>',
            '50 GB En Almacenamiento <span>(expansion gratuita)</span>',
            'Backups <span>Automaticos</span>',
            'Certificado SSL <span>Incluido</span>',
            'Gestion 24/7 <span>Avanzada</span>',
            'Garantia <span>lo hacemos funcionar por ti</span>',
            'Guia y Capacitacion <span>2 horas por mes</span>',
        ],
    ],
    [
        'planId' => '3',
        'price' => 1399,
        'features' => [
            '35 usuarios <span>$39.9MXN por Usuario al mes adicional</span>',
            '5 GB <span>En Base de Datos</span>',
            '150 GB En Almacenamiento <span>(expansion gratuita)</span>',
            'Backups <span>Automaticos y Diarios</span>',
            'Certificado SSL <span>Incluido</span>',
            'Gestion 24/7 <span>Avanzada y Prioritaria</span>',
            'Garantia <span>Lo hacemos funcionar por ti</span>',
            'Acceso <span>a la base de datos</span>',
            'Capacitacion y Guia Personalizada <span>te explicamos de 0 a 100 (4 horas al mes)</span>',
        ],
    ],
];

$stripeSecret = $_ENV['stripeSecret'];
\Stripe\Stripe::setApiKey($stripeSecret);
$billingPortalSessionLink = \Stripe\BillingPortal\Session::create([
    'customer' => $result['stripeCustomerId'],
    'return_url' => 'https://www.rendercores.com/dashboard/services',
]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $servicePlan = htmlspecialchars(trim($_POST['selectedPlan']), ENT_QUOTES, 'UTF-8');
    $serviceName = htmlspecialchars(trim($_POST['serviceName']), ENT_QUOTES, 'UTF-8');
    $serviceVersion = htmlspecialchars(trim($_POST['serviceVersion']), ENT_QUOTES, 'UTF-8');
    $acceptLaws = htmlspecialchars(trim($_POST['acceptLaws']), ENT_QUOTES, 'UTF-8');
    $shareData = htmlspecialchars(trim($_POST['shareData']), ENT_QUOTES, 'UTF-8');

    $statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $statement->execute(array(':id' => $id));
    $result = $statement->fetch();
    $istatement = $connection->prepare('SELECT * FROM subscriptions WHERE subscriptionName = :subscriptionName LIMIT 1');
    $istatement->execute(array(':subscriptionName' => $serviceName));
    $iresult = $istatement->fetch();
    if(empty($serviceName) || empty($servicePlan) || empty($serviceVersion)){
        $serviceErrors = "Debes de rellenar todos los datos";
    }elseif ($iresult != false) {
        $serviceErrors = "Este nombre ya está en uso";
    } elseif(!isset($acceptLaws) || !isset($shareData)){
        $serviceErrors = "Tienes que aceptar que se apliquen las leyes de la region. También debes aceptar que los datos se compartan de forma privada y segura con el socio local.";
    }else{
        $serviceStatement = $connection->prepare('SELECT * FROM services WHERE serviceId = :serviceId LIMIT 1');
        $serviceStatement->execute(array(':serviceId' => $servicePlan));
        $serviceResult = $serviceStatement->fetch();
        $checkout_session = \Stripe\Checkout\Session::create([
            'customer' => $result['stripeCustomerId'],
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $serviceResult['priceId'],
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => 'http://rendercores.com/dashboard/payments/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'https://rendercores.com/dashboard/services',
            'metadata' => [
                'userId' => $id,
                'serviceName' => $serviceName,
                'serviceVersion' => $serviceVersion,
            ],
        ]);
        
        header("Location: " . $checkout_session->url);
        // $output = shell_exec("python3 ../erpCreate.py $serviceName");
    }
}

$subscriptionsStatement = $connection->prepare('SELECT * FROM subscriptions WHERE userId = :userId');
$subscriptionsStatement->execute(array(':userId' => $id));
$subscriptions = $subscriptionsStatement->fetchAll();

if (isset($_SESSION['id'])){
    if($result['status'] === 'verified'){
        require_once APP_ROOT . 'src/views/dashboard/services.view.php';
    } else{
        require_once APP_ROOT . 'src/views/dashboard/notVerified.view.php';
    }
} else if (!isset($_SESSION['id'])){
    header('Location: ../auth/signin');
} else {
    require_once APP_ROOT . 'src/main/auth/ban.php';
}