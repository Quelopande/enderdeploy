<?php
define('APP_ROOT', __DIR__ . '/../');
ini_set('error_log', APP_ROOT . 'storage/logs/generalError.log');

require APP_ROOT . 'vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(APP_ROOT);
$dotenv->safeLoad();

$stripe = new \Stripe\StripeClient($_ENV['stripeSecret']);

try {
    $subscriptions = $stripe->subscriptions->all(['status' => ['past_due', 'unpaid'], 'limit' => 100]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    error_log("cronJobCheckService (1): Error when acquiring all the suspended subscription: " . $e->getMessage());
    exit();
}

foreach ($subscriptions->autoPagingIterator() as $subscription) {
    if ($subscription->status === 'unpaid') {
        try {
            $stripe->subscriptions->cancel($subscription->id);
            continue;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            error_log("cronJobCheckService (3): Error when canceling UNPAID " . $subscription->id . " : " . $e->getMessage());
            continue;
        }
    }
    
    try {
        $invoices = $stripe->invoices->all([
            'subscription' => $subscription->id,
            'paid' => false,
            'status' => 'open',
            'limit' => 1,
        ]);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        error_log("cronJobCheckService (2): Error while listing invoices of the subcription " . $subscription->id . ": " . $e->getMessage());
        continue;
    }

    if (!empty($invoices->data)) {
        $failedInvoice = $invoices->data[0];
        $invoiceCreationTimestamp = $failedInvoice->created; 
        $sevenDaysInSeconds = 7 * 24 * 60 * 60;
        $deadlineTimestamp = $invoiceCreationTimestamp + $sevenDaysInSeconds;

        if (time() >= $deadlineTimestamp) {
            try{
                $stripe->subscriptions->cancel($subscription->id);
            } catch (\Stripe\Exception\ApiErrorException $e){
                error_log("cronJobCheckService (3): Error while canceling the subscription " . $subscription->id . " : " . $e->getMessage());
            }
        }
    }
}