<?php
require '../../vendor/autoload.php';
require '../../connection.php';

$stripeSecret = getenv('stripeSecret');
\Stripe\Stripe::setApiKey("$stripeSecret");
$logFile = 'cronJobCheckService.log';
$todayDate = date('Y-m-d');


file_put_contents($logFile, "Inicio verificación {$todayDate}\n", FILE_APPEND);

try {
    $subs = \Stripe\Subscription::all([
        'limit' => 100,
        'created' => [
            'gte' => strtotime('today midnight'),
            'lte' => strtotime('now')
        ]
    ]);

    $changes = 0;

    foreach ($subs as $sub) {
        $action = null;
        if ($sub->status == 'canceled') {
            $action = 'canceled';
            $sub->current_period_end = $todayDate;
        } elseif (date('Y-m-d', $sub->current_period_end) == $todayDate) {
            $action = 'active';
        }

        if ($action) {
            $estatement = $connection->prepare('UPDATE users SET subscriptionstatus = :subscriptionstatus, subscriptionExpirationTime = :subscriptionExpirationTime WHERE subscriptionServiceId = :subscriptionServiceId');
            $estatement->execute([
                ':subscriptionServiceId' => $sub->id,
                ':subscriptionstatus' => $sub->status,
                ':subscriptionExpirationTime' => date('Y-m-d H:i:s', $sub->current_period_end),
            ]);
            $changes++;
        }
    }

      file_put_contents($logFile, "Total cambios procesados: {$changes}\n", FILE_APPEND);

} catch (\Exception $e) {
    $db->rollback();
    file_put_contents($logFile, "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
} finally {
    $db->close();
}