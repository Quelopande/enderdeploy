<?php
require_once APP_ROOT . 'vendor/autoload.php';

$stripeSecret = $_ENV['stripeSecret'];
\Stripe\Stripe::setApiKey($stripeSecret);

if (!isset($_GET['session_id'])) {
  die("⚠️ Falta session_id en la URL.");
}

try {
  $session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);

  if ($session->payment_status === 'paid') {
    $subscriptionId = $session->subscription;
    
    $subscription = \Stripe\Subscription::retrieve($subscriptionId);
      $userId = $session->metadata->userId;
      $serviceName = $session->metadata->serviceName;

      $priceId = $subscription->items->data[0]->price->id;
      $istatement = $connection->prepare('SELECT * FROM services WHERE priceId = :priceId LIMIT 1');
      $istatement->execute(array(':priceId' => $priceId));
      $iresult = $istatement->fetch();
      echo $iresult['serviceId'];
      $estatement = $connection->prepare('INSERT INTO subscriptions (userId, subscriptionStripeId, subscriptionServiceId, subscriptionStatus, subscriptionExpirationTime, subscriptionName) VALUES (:userId, :subscriptionStripeId, :subscriptionServiceId, :subscriptionStatus, :subscriptionExpirationTime, :subscriptionName)');
      $estatement->execute([
        ':userId' => $userId,
        ':subscriptionStripeId' => $iresult['serviceId'],
        ':subscriptionServiceId' => $subscription->id,
        ':subscriptionStatus' => $session->status,
        ':subscriptionExpirationTime' => date('Y-m-d H:i:s', $subscription->current_period_end),
        ':subscriptionName' => $serviceName
      ]);
      require "../../../views/dashboard/payments/success.view.php";
  } else {
    require "../../../views/dashboard/payments/fail.view.php";
  }
} catch (\Stripe\Exception\ApiErrorException $e) {
  error_log("Error Stripe: " . $e->getMessage());
  echo "Hubo un error al procesar tu pago.";
}