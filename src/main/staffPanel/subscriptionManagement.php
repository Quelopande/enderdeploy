<?php
$statement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
$statement->execute(array(':id' => $id));
$result = $statement->fetch(PDO::FETCH_ASSOC);

$roleStatement = $connection->prepare('SELECT * FROM roles WHERE roleId = :roleId LIMIT 1');
$roleStatement->execute(array(':roleId' => $result['role']));
$roleResult = $roleStatement->fetch(PDO::FETCH_ASSOC);

if ($result['role'] != -1 && ($roleResult['viewSubscriptionData'] == 1 OR $roleResult['manageSubscription'] == '1')) {
    $subscriptionId = (int)$_GET['subscriptionId'];

    $subscriptionStatement = $connection->prepare('SELECT * FROM subscriptions WHERE subscriptionId = :subscriptionId');
    $subscriptionStatement->execute(array(':subscriptionId' => $subscriptionId));
    $subscriptionResult = $subscriptionStatement->fetch(PDO::FETCH_ASSOC);
    
    $stripeSecret = $_ENV['stripeSecret'];
    $stripe = new \Stripe\StripeClient($stripeSecret);

    try {
        $stripeSubscription = $stripe->subscriptions->retrieve($subscriptionResult['subscriptionStripeId']);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        error_log("staffPanel/subscriptionManagement (1): Error when trying to get subscription data directly from Stripe" . $e->getMessage());
        exit; 
    }

    function getServiceData($serviceIdToObtain, $dataToObtain, $connection){
        $serviceStatement = $connection->prepare('SELECT * FROM services WHERE serviceId = :serviceId LIMIT 1');
        $serviceStatement->execute(array(':serviceId' => $serviceIdToObtain));
        $serviceResult = $serviceStatement->fetch(PDO::FETCH_ASSOC);

        return $serviceResult[$dataToObtain];
    }

    if ($stripeSubscription->status === 'active') {
        if ($stripeSubscription->cancel_at_period_end === true) {
            $btnTxt = "Reactivar";
            $notifyTxt = 'Cancelación Programada';
            $active = true;
            $showBtn = true; 
        } else {
            $btnTxt = "Cancelar";
            $notifyTxt = 'Activo';
            $active = true;
            $showBtn = true;
        }
    } else if ($stripeSubscription->status === 'past_due' || $stripeSubscription->status === 'unpaid') {
        $btnTxt = "Renovar";
        $notifyTxt = 'Suspendido';
        $suspended = true;
        $showBtn = true;
    } else if ($stripeSubscription->status === 'canceled') {
        $notifyTxt = 'Expirado / Finalizado';
        $btnTxt = "Comprar de Nuevo";
        $canceled = true;
        $showBtn = false;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['subscriptionStatusUpdateBtn']) && $stripeSubscription->status === 'active') {
        if ($stripeSubscription->cancel_at_period_end === true) {
            try {
                $stripe->subscriptions->update($subscriptionResult['subscriptionStripeId'], [
                    'cancel_at_period_end' => false,
                ]);
                header('Location: /staffPanel/subscriptionManagement?subscriptionId=' . $subscriptionId . '&success=reactivated');
                exit;

            } catch (\Stripe\Exception\ApiErrorException $e) {
                error_log("staffPanel/subscriptionManagement (2): Error al intentar reactivar la cancelación programada: " . $e->getMessage());
                header('Location: /checkout/new-subscription');
                exit;
            }
        } else {
            try {
                $stripe->subscriptions->update($subscriptionResult['subscriptionStripeId'], [
                    'cancel_at_period_end' => true,
                ]);
                header('Location: /staffPanel/subscriptionManagement?subscriptionId=' . $subscriptionId . '&status=cancel_scheduled');
                exit;
            } catch (\Stripe\Exception\ApiErrorException $e) {
                error_log("staffPanel/subscriptionManagement (3): Error al programar la cancelación en Stripe: " . $e->getMessage());
                header('Location: /staffPanel/subscriptionManagement?subscriptionId=' . $subscriptionId . '&error=stripe_api_cancel_fail');
                exit;
            }
        }
    } else if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['subscriptionPlanUpdateBtn'])) {
        $newServiceId = (int)$_POST['selectedPlan'];
        if($subscriptionResult['serviceId'] < $newServiceId && $stripeSubscription->status !== 'canceled'){
            $priceId = getServiceData($newServiceId, 'priceId', $connection); 
            try {
                $stripe->subscriptions->update($subscriptionResult['subscriptionStripeId'], [
                    'items' => [
                        [
                            'id' => $stripeSubscription->items->data[0]->id,
                            'price' => $priceId,
                        ],
                    ],
                    'proration_behavior' => 'always_invoice',
                ]);
                header('Location: /staffPanel/subscriptionManagement?subscriptionId=' . $subscriptionId . '&status=plan_updated');
                exit;

            } catch (\Stripe\Exception\ApiErrorException $e) {
                error_log("staffPanel/subscriptionManagement (4): Error al cambiar el plan en Stripe: " . $e->getMessage());
                header('Location: /staffPanel/subscriptionManagement?subscriptionId=' . $subscriptionId . '&error=plan_update_fail');
                exit;
            }
        }
        header('Location: /staffPanel/subscriptionManagement?subscriptionId=' . $subscriptionId);
        exit;
    }

    if($subscriptionResult['serviceId'] !== 3){
        $plans = [
            // When modifying this ensure also to modify it in subscriptions, there's no need to set planId 1 because user cannot downgrade
            [
                'planId' => '2',
                'price' => 799,
            ],
            [
                'planId' => '3',
                'price' => 1399,
            ],
        ];
    }
    if($subscriptionResult['serviceId'] === 2){
        unset($plans[0]);
    }

    require_once APP_ROOT . 'src/views/staffPanel/subscriptionManagement.view.php';
} else {
    header("HTTP/1.0 403 Forbidden");
    require_once APP_ROOT . 'src/main/staffPanel/noAccess.php';
    exit();
}