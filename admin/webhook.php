<?php
require_once '../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
$secret = STRIPE_WEBHOOK_SECRET;

$payload = file_get_contents('php://input');
$sig     = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$secret  = 'whsec_YOUR_WEBHOOK_SIGNING_SECRET'; // from Stripe dashboard

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig, $secret);
} catch (\Exception $e) {
    http_response_code(400);
    exit();
}

if ($event->type === 'payment_intent.succeeded') {
    $intent  = $event->data->object;
    $user_id    = $intent->metadata->user_id;
    $product_id = $intent->metadata->product_id;
    $quantity   = $intent->metadata->quantity;
    $total      = $intent->amount / 100;
    $unit_price = $total / $quantity;

    include '../db.php';
    $stmt = $conn->prepare(
        "INSERT INTO orders (user_id, product_id, quantity, unit_price, total, status, created_at)
         VALUES (?, ?, ?, ?, ?, 'paid', NOW())"
    );
    $stmt->bind_param("iiddd", $user_id, $product_id, $quantity, $unit_price, $total);
    $stmt->execute();

    // Decrease stock
    $stmt = $conn->prepare(
        "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?"
    );
    $stmt->bind_param("iii", $quantity, $product_id, $quantity);
    $stmt->execute();
}

http_response_code(200);