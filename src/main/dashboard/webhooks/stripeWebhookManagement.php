<?php
$endpointSecret = $_ENV['stripeWebhoookSecret'];
$stripeSecret = $_ENV['stripeSecret'];
$stripe = new \Stripe\StripeClient($stripeSecret);

$payload = @file_get_contents('php://input');
$event = null;

try {
    $event = \Stripe\Event::constructFrom(
        json_decode($payload, true)
    );
} catch(\UnexpectedValueException $e) {
    http_response_code(400);
    exit();
}

if ($endpointSecret) {
  $signHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
  try {
    $event = \Stripe\Webhook::constructEvent(
      $payload, $signHeader, $endpointSecret
    );
  } catch(\Stripe\Exception\SignatureVerificationException $e) {
    error_log('stripeWebhookManagement (a0): signature verification failed.');
    http_response_code(400);
    exit();
  } catch(\UnexpectedValueException $e) {
    error_log('stripeWebhookManagement (a1): payload verification failed.');
    http_response_code(400);
    exit();
  }
}

if ($event === null) {
    http_response_code(500);
    exit();
}
switch ($event->type) {
    case 'checkout.session.completed':
        $session = $event->data->object;

        if ($session->mode !== 'subscription') {
            break;
        }

        $subscriptionId = $session->subscription;
        try{
            $subscription = $stripe->subscriptions->retrieve($subscriptionId);
        } catch (Exception $e) {
            error_log("stripeWebhookManagement (0): Error while trying to create Stripe Client " . $e->getMessage());
            break;
        }

        $userId = $session->metadata->userId;
        $serviceName = $session->metadata->serviceName;
        $serviceId = $session->metadata->serviceId;
        $expirationTime = date('Y-m-d H:i:s', $subscription->current_period_end);
        $startTime = date('Y-m-d H:i:s', $subscription->current_period_start);
        try{
            $statement = $connection->prepare('INSERT INTO subscriptions (userId, subscriptionStripeId, subscriptionStatus, subscriptionExpirationTime, subscriptionStartTime, subscriptionName, serviceId) VALUES (:userId, :subscriptionStripeId, :subscriptionStatus, :subscriptionExpirationTime, :subscriptionStartTime, :subscriptionName, :serviceId)');
            $statement->execute([
            ':userId' => $userId,
            ':subscriptionStripeId' => $subscription->id,
            ':subscriptionStatus' => $subscription->status,
            ':subscriptionExpirationTime' => $expirationTime,
            ':subscriptionStartTime' => $startTime,
            ':subscriptionName' => $serviceName,
            ':serviceId' => $serviceId
            ]);
        } catch (PDOException $e) {
            error_log("stripeWebhookManagement (1): Error while trying to insert the stripe Subscription details in DB " . $e->getMessage());
        }
        break;
    case 'customer.subscription.updated':
        $subscription = $event->data->object;

        if($subscription->status === 'active'){
            $subscriptionStatus = 'active';

            // EJECUTAR CÓDIGO PARA REACTIVAR SERVICIO SI ESTABA SUSPENDIDO
        } else if($subscription->status === 'past_due' || $subscription->status === 'unpaid'){
            $subscriptionStatus = 'suspended';

            // EJECUTAR CÓDIGO PARA SUSPENDER SERVICIO (CANCELAR CON CRONJOB TRAS 7 DÍAS)
        } else if($subscription->status === 'canceled'){
            $subscriptionStatus = 'canceled';

            // EJECUTAR CÓDIGO PARA CANCELAR SERVICIO
        } else{
            error_log("stripeWebhookManagement (2): Stripe Subscription status invalid:" . $subscriptionStatus);
        }
        try {
            $statement = $connection->prepare('UPDATE subscriptions SET subscriptionStatus = :subscriptionStatus, subscriptionExpirationTime = :subscriptionExpirationTime WHERE subscriptionStripeId = :subscriptionStripeId');
            $statement->execute([
                ':subscriptionStatus' => $subscriptionStatus,
                ':subscriptionExpirationTime' => date('Y-m-d H:i:s', $subscription->current_period_end),
                ':subscriptionStripeId' => $subscription->id
            ]);
        } catch (PDOException $e) {
            error_log("stripeWebhookManagement (3): Error while trying to update status" . $e->getMessage());
        }
        break;
    case 'invoice.payment_succeeded':
        $invoice = $event->data->object;
        if (!$invoice->subscription) {
            break;
        }

        $subscription = $stripe->subscriptions->retrieve($invoice->subscription);
        try {
            $statement = $connection->prepare('UPDATE subscriptions SET subscriptionStatus = :subscriptionStatus, subscriptionExpirationTime = :subscriptionExpirationTime WHERE subscriptionStripeId = :subscriptionStripeId');
            $statement->execute([
                ':subscriptionStatus' => $subscription->status, // Must be 'active' after successful payment
                ':subscriptionExpirationTime' => date('Y-m-d H:i:s', $subscription->current_period_end),
                ':subscriptionStripeId' => $subscription->id
            ]);
        } catch (PDOException $e) {
            error_log("stripeWebhookManagement (4): Error while trying to renovate the subscription" . $e->getMessage());
        }
        break;
    case 'invoice.payment_failed':
        $invoice = $event->data->object;
        if (!$invoice->subscription) {
            break;
        }

        $subscription = $stripe->subscriptions->retrieve($invoice->subscription);
        try {
            $statement = $connection->prepare('UPDATE subscriptions SET subscriptionStatus = :subscriptionStatus, subscriptionExpirationTime = :subscriptionExpirationTime WHERE subscriptionStripeId = :subscriptionStripeId');
            $statement->execute([
                ':subscriptionStatus' => "suspended", // We receive 'past_due' or 'unpaid', that means suspended
                ':subscriptionExpirationTime' => date('Y-m-d H:i:s', $subscription->current_period_end),
                ':subscriptionStripeId' => $subscription->id
            ]);
        } catch (PDOException $e) {
            error_log("stripeWebhookManagement (5): Error while trying to renovate the subscription" . $e->getMessage());
        }
        break;
    case 'customer.subscription.deleted':
        $subscription = $event->data->object;

        try {
            $statement = $connection->prepare('UPDATE subscriptions SET subscriptionStatus = :subscriptionStatus WHERE subscriptionStripeId = :subscriptionStripeId');
            $statement->execute([
                ':subscriptionStatus' => $subscription->status, // Must be 'canceled'
                ':subscriptionStripeId' => $subscription->id
            ]);
        } catch (PDOException $e) {
            error_log("stripeWebhookManagement (5): Error while trying to renovate the subscription" . $e->getMessage());
        }
        break;
    case 'customer.created':
        $customer = $event->data->object;
        break;
    default:
        echo 'Received unknown event type ' . $event->type;
}

http_response_code(200);
exit();