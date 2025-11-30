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
            [
                'main' => '10 usuarios',
                'note' => '$49.9MXN por Usuario al mes adicional'
            ],
            [
                'main' => '1 GB',
                'note' => 'En Base de Datos'
            ],
            [
                'main' => '25 GB En Almacenamiento',
                'note' => '(expansion gratuita)'
            ],
            [
                'main' => 'Backups',
                'note' => 'Manuales'
            ],
            [
                'main' => 'Certificado SSL',
                'note' => 'Incluido'
            ],
            [
                'main' => 'Guia rapida y Documentacion',
                'note' => '1 horas por mes'
            ],
        ],
    ],
    [
        'planId' => '2',
        'price' => 799,
        'features' => [
            [
                'main' => '20 usuarios',
                'note' => '$39.9MXN por Usuario al mes adicional'
            ],
            [
                'main' => '2 GB',
                'note' => 'En Base de Datos'
            ],
            [
                'main' => '50 GB En Almacenamiento',
                'note' => '(expansion gratuita)'
            ],
            [
                'main' => 'Backups',
                'note' => 'Automaticos'
            ],
            [
                'main' => 'Certificado SSL',
                'note' => 'Incluido'
            ],
            [
                'main' => 'Gestion 24/7',
                'note' => 'Avanzada'
            ],
            [
                'main' => 'Garantia',
                'note' => 'lo hacemos funcionar por ti'
            ],
            [
                'main' => 'Guia y Capacitacion',
                'note' => '2 horas por mes'
            ],
        ],
    ],
    [
        'planId' => '3',
        'price' => 1399,
        'features' => [
            [
                'main' => '35 usuarios',
                'note' => '$39.9MXN por Usuario al mes adicional'
            ],
            [
                'main' => '5 GB',
                'note' => 'En Base de Datos'
            ],
            [
                'main' => '150 GB En Almacenamiento',
                'note' => '(expansion gratuita)'
            ],
            [
                'main' => 'Backups',
                'note' => 'Automaticos y Diarios'
            ],
            [
                'main' => 'Certificado SSL',
                'note' => 'Incluido'
            ],
            [
                'main' => 'Gestion 24/7',
                'note' => 'Avanzada y Prioritaria'
            ],
            [
                'main' => 'Garantia',
                'note' => 'Lo hacemos funcionar por ti'
            ],
            [
                'main' => 'Acceso',
                'note' => 'a la base de datos'
            ],
            [
                'main' => 'Capacitacion y Guia Personalizada',
                'note' => 'te explicamos de 0 a 100 (4 horas al mes)'
            ],
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
    $serviceId = htmlspecialchars(trim($_POST['selectedPlan']), ENT_QUOTES, 'UTF-8');
    $serviceName = htmlspecialchars(trim($_POST['serviceName']), ENT_QUOTES, 'UTF-8');
    $serviceVersion = htmlspecialchars(trim($_POST['serviceVersion']), ENT_QUOTES, 'UTF-8');
    $acceptLaws = htmlspecialchars(trim($_POST['acceptLaws']), ENT_QUOTES, 'UTF-8');
    $shareData = htmlspecialchars(trim($_POST['shareData']), ENT_QUOTES, 'UTF-8');

    $statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $statement->execute(array(':id' => $id));
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $istatement = $connection->prepare('SELECT * FROM subscriptions WHERE subscriptionName = :subscriptionName LIMIT 1');
    $istatement->execute(array(':subscriptionName' => $serviceName));
    $iresult = $istatement->fetch(PDO::FETCH_ASSOC);
    if(empty($serviceName) || empty($serviceId) || empty($serviceVersion)){
        $serviceErrors = "Debes de rellenar todos los datos";
    }elseif ($iresult != false) {
        $serviceErrors = "Este nombre ya está en uso";
    } elseif(!isset($acceptLaws) || !isset($shareData)){
        $serviceErrors = "Tienes que aceptar que se apliquen las leyes de la region. También debes aceptar que los datos se compartan de forma privada y segura con el socio local.";
    }else{
        $serviceStatement = $connection->prepare('SELECT * FROM services WHERE serviceId = :serviceId LIMIT 1');
        $serviceStatement->execute(array(':serviceId' => $serviceId));
        $serviceResult = $serviceStatement->fetch(PDO::FETCH_ASSOC);
        $checkout_session = \Stripe\Checkout\Session::create([
            'customer' => $result['stripeCustomerId'],
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $serviceResult['priceId'],
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => 'https://www.rendercores.com/dashboard/payments/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'https://rendercores.com/dashboard/services',
            'metadata' => [
                'userId' => $id,
                'serviceName' => $serviceName,
                'serviceVersion' => $serviceVersion,
                'serviceId' => $serviceId,
            ],
        ]);
        
        header("Location: " . $checkout_session->url);
        // $output = shell_exec("python3 ../erpCreate.py $serviceName");
    }
}

$subscriptionsStatement = $connection->prepare('SELECT * FROM subscriptions WHERE userId = :userId');
$subscriptionsStatement->execute(array(':userId' => $id));
$subscriptions = $subscriptionsStatement->fetchAll(PDO::FETCH_ASSOC);

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