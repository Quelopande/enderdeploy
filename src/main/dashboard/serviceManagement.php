<?php
$statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
$statement->execute(array(':id' => $id));
$result = $statement->fetch(PDO::FETCH_ASSOC);

$stripeSecret = $_ENV['stripeSecret'];
$stripe = new \Stripe\StripeClient($stripeSecret);

try {
    $billingPortalSession = $stripe->billingPortal->sessions->create([
        'customer' => $result['stripeCustomerId'],
        'return_url' => 'https://www.rendercores.com/dashboard/serviceManagement?' . $_SERVER['QUERY_STRING'],
    ]);
    $billingPortalSessionLink = $billingPortalSession->url;
} catch (\Stripe\Exception\ApiErrorException $e) {
    error_log("serviceManagement (1): Error while creating the billing portal" . $e->getMessage());
    $billingPortalSessionLink = '/dashboard/services'; 
}

if(!$_GET['subscriptionId']){
    header('Location: /dashboard/services');
} else{
    $subscriptionId = htmlspecialchars(trim($_GET['subscriptionId']), ENT_QUOTES, 'UTF-8');
}

$subscriptionStatement = $connection->prepare('SELECT * FROM subscriptions WHERE subscriptionId = :subscriptionId LIMIT 1');
$subscriptionStatement->execute(array(':subscriptionId' => $subscriptionId));
$subscriptionResult = $subscriptionStatement->fetch(PDO::FETCH_ASSOC);

if(!$subscriptionResult){
    header('Location: /dashboard/services');
}

$dateString = $subscriptionResult['subscriptionExpirationTime'];
$dateTime = new DateTime($dateString);
$timestamp = $dateTime->getTimestamp();

$formatter = new IntlDateFormatter(
    'es_ES',
    IntlDateFormatter::NONE,
    IntlDateFormatter::NONE,
    'America/Mazatlan', // Timezone that has the server established
    IntlDateFormatter::GREGORIAN,
    'd MMM, yyyy'
);

$formattedDate = $formatter->format($timestamp);


$btnColor = '';
$btnSecColor = '';
$notifyColor = '';
$notifySecColor = '';
$btnTxt = '';
$notifyTxt = '';
$active = false;
$canceled = false;
$suspended = false;
$showBtn = false;


try {
    $stripeSubscription = $stripe->subscriptions->retrieve($subscriptionResult['subscriptionStripeId']);
} catch (\Stripe\Exception\ApiErrorException $e) {
    error_log("serviceManagement (2): Error when trying to get subscription data directly from Stripe" . $e->getMessage());
    $stripeStatus = 'error'; 
    exit; 
}
$stripeStatus = $stripeSubscription->status;

$btnColor = '';
$btnSecColor = '';
$notifyColor = '';
$notifySecColor = '';
$btnTxt = '';
$notifyTxt = '';
$active = false;
$canceled = false;
$suspended = false;
$showBtn = false;
if ($stripeStatus === 'active') {
    if ($stripeSubscription->cancel_at_period_end === true) {
        $btnColor = '#bbe8c8';
        $btnSecColor = '#188038';
        $notifyColor = '#ff9a9a';
        $notifySecColor = '#a10000';
        $btnTxt = "Reactivar";
        $notifyTxt = 'Cancelación Programada';
        $active = true;
        $showBtn = true; 
    } else {
        $btnColor = '#ff9a9a';
        $btnSecColor = '#a10000';
        $notifyColor = '#bbe8c8';
        $notifySecColor = '#188038';
        $btnTxt = "Cancelar";
        $notifyTxt = 'Activo';
        $active = true;
        $showBtn = true;
    }
} else if ($stripeStatus === 'past_due' || $stripeStatus === 'unpaid') {
    $btnColor = '#bbe8c8';
    $btnSecColor = '#188038';
    $notifyColor = '#ffc897ff'; 
    $notifySecColor = '#b85600ff';
    $btnTxt = "Renovar";
    $notifyTxt = 'Suspendido';
    $suspended = true;
    $showBtn = true;
} else if ($stripeStatus === 'canceled') {
    $canceled = true;
    $notifyColor = '#ff9a9a';
    $notifySecColor = '#a10000';
    $notifyTxt = 'Expirado / Finalizado';
    $btnTxt = "Comprar de Nuevo";
    $showBtn = false;
} 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['subscriptionStatusUpdateBtn']) && $stripeStatus === 'active') {
    if ($stripeSubscription->cancel_at_period_end === true) {
        try {
            $stripe->subscriptions->update($subscriptionResult['subscriptionStripeId'], [
                'cancel_at_period_end' => false,
            ]);
            header('Location: /dashboard/serviceManagement?subscriptionId=' . $subscriptionId . '&success=reactivated');
            exit;

        } catch (\Stripe\Exception\ApiErrorException $e) {
            error_log("serviceManagement (4): Error al intentar reactivar la cancelación programada: " . $e->getMessage());
            header('Location: /checkout/new-subscription');
            exit;
        }
    } else {
        try {
            $stripe->subscriptions->update($subscriptionResult['subscriptionStripeId'], [
                'cancel_at_period_end' => true,
            ]);
            header('Location: /dashboard/serviceManagement?subscriptionId=' . $subscriptionId . '&status=cancel_scheduled');
            exit;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            error_log("serviceManagement (3): Error al programar la cancelación en Stripe: " . $e->getMessage());
            header('Location: /dashboard/serviceManagement?subscriptionId=' . $subscriptionId . '&error=stripe_api_cancel_fail');
            exit;
        }
    }
}

$serviceStatement = $connection->prepare('SELECT * FROM services WHERE serviceId = :serviceId LIMIT 1');
$serviceStatement->execute(array(':serviceId' => $subscriptionResult['serviceId']));
$serviceResult = $serviceStatement->fetch(PDO::FETCH_ASSOC);

if (isset($_SESSION['id'])){
    if($result['status'] === 'verified'){
        require_once APP_ROOT . 'src/views/dashboard/serviceManagement.view.php';
    } else{
        require_once APP_ROOT . 'src/views/dashboard/notVerified.view.php';
    }
} else if (!isset($_SESSION['id'])){
    header('Location: ../auth/signin');
} else {
    require_once APP_ROOT . 'src/main/auth/ban.php';
}