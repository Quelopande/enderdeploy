<?php
// INACTIVA
// \Stripe\Stripe::setApiKey('' . $_ENV['stripeSecret'] . '');

// Obtener todas las suscripciones
$subscriptions = \Stripe\Subscription::all(['limit' => 100]);

echo "<h2>Estado de Pagos</h2>";
echo "<table border='1'>";
echo "<tr><th>Email</th><th>Estado</th><th>Próximo pago</th><th>Monto</th></tr>";

foreach ($subscriptions->autoPagingIterator() as $sub) {
    $customer = \Stripe\Customer::retrieve($sub->customer);
    
    $next_payment = date('d/m/Y', $sub->current_period_end);
    
    $amount = ($sub->plan->amount / 100) . ' ' . strtoupper($sub->plan->currency);
    
    $status = ($sub->status == 'active') ? '✅ Activo' : '❌ Inactivo';
    
    echo "<tr>";
    echo "<td>{$customer->email}</td>";
    echo "<td>{$status}</td>";
    echo "<td>{$next_payment}</td>";
    echo "<td>{$amount}</td>";
    echo "</tr>";
}

echo "</table>";

// Nota: Agregar verificacion staff