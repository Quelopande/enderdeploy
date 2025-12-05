<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', APP_ROOT . 'storage/logs/generalError.log');
$endpointSecret = $_ENV['stripeWebhoookSecret'];
$stripeSecret = $_ENV['stripeSecret'];
$stripe = new \Stripe\StripeClient($stripeSecret);

// ExpirationTime is in UTC/GMT and in sandbox mode the date is wrong

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
        $subscriptionName = $session->metadata->subscriptionName;
        $serviceId = $session->metadata->serviceId;
        $expirationTime = gmdate('Y-m-d H:i:s', $subscription->current_period_end);
        $startTime = date('Y-m-d H:i:s', $subscription->current_period_start);
        try{
            $statement = $connection->prepare('INSERT INTO subscriptions (userId, subscriptionStripeId, subscriptionStatus, subscriptionExpirationTime, subscriptionStartTime, subscriptionName, serviceId) VALUES (:userId, :subscriptionStripeId, :subscriptionStatus, :subscriptionExpirationTime, :subscriptionStartTime, :subscriptionName, :serviceId)');
            $statement->execute([
            ':userId' => $userId,
            ':subscriptionStripeId' => $subscription->id,
            ':subscriptionStatus' => $subscription->status,
            ':subscriptionExpirationTime' => $expirationTime,
            ':subscriptionStartTime' => $startTime,
            ':subscriptionName' => $subscriptionName,
            ':serviceId' => $serviceId
            ]);
        } catch (PDOException $e) {
            error_log("stripeWebhookManagement (1): Error while trying to insert the stripe Subscription details in DB " . $e->getMessage());
        }
        break;
    case 'customer.subscription.updated':
        $subscription = $event->data->object;

        $currentStripePriceId = $subscription->items->data[0]->price->id;
        
        $serviceStatement = $connection->prepare('SELECT serviceId FROM services WHERE priceId = :priceId LIMIT 1');
        $serviceStatement->execute(array(':priceId' => $currentStripePriceId));
        $serviceResult = $serviceStatement->fetch(PDO::FETCH_ASSOC);

        if($subscription->status === 'active'){
            $subscriptionStatus = 'active';
        } else if($subscription->status === 'past_due' || $subscription->status === 'unpaid'){
            $subscriptionStatus = 'suspended';
        } else if($subscription->status === 'canceled'){
            $subscriptionStatus = 'canceled';
        } else{
            error_log("stripeWebhookManagement (2): Stripe Subscription status invalid:" . $subscriptionStatus);
        }

        $expirationTime = gmdate('Y-m-d H:i:s', $subscription->current_period_end);
        try {
            $statement = $connection->prepare('UPDATE subscriptions SET subscriptionStatus = :subscriptionStatus, subscriptionExpirationTime = :subscriptionExpirationTime, serviceId = :serviceId WHERE subscriptionStripeId = :subscriptionStripeId');
            $statement->execute([
                ':subscriptionStatus' => $subscriptionStatus,
                ':subscriptionExpirationTime' => $expirationTime,
                ':subscriptionStripeId' => $subscription->id,
                ':serviceId' => $serviceResult['serviceId']
            ]);
        } catch (PDOException $e) {
            error_log("stripeWebhookManagement (3): Error while trying to update status" . $e->getMessage());
        }
        break;
    case 'invoice.payment_succeeded':
        $invoice = $event->data->object;
        if (!isset($invoice->subscription) || empty($invoice->subscription)) {
            break;
        }

        $subscription = $stripe->subscriptions->retrieve($invoice->subscription);

        $expirationTime = gmdate('Y-m-d H:i:s', $subscription->current_period_end);
        try {
            $statement = $connection->prepare('UPDATE subscriptions SET subscriptionStatus = :subscriptionStatus, subscriptionExpirationTime = :subscriptionExpirationTime WHERE subscriptionStripeId = :subscriptionStripeId');
            $statement->execute([
                ':subscriptionStatus' => $subscription->status, // Must be 'active' after successful payment
                ':subscriptionExpirationTime' => $expirationTime,
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

        $expirationTime = gmdate('Y-m-d H:i:s', $subscription->current_period_end);
        try {
            $statement = $connection->prepare('UPDATE subscriptions SET subscriptionStatus = :subscriptionStatus, subscriptionExpirationTime = :subscriptionExpirationTime WHERE subscriptionStripeId = :subscriptionStripeId');
            $statement->execute([
                ':subscriptionStatus' => "suspended", // We receive 'past_due' or 'unpaid', that means suspended
                ':subscriptionExpirationTime' => $expirationTime,
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