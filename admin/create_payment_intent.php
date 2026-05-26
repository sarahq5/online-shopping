<?php
/**
 * create_payment_intent.php
 * Called via fetch() from checkout.php to create a Stripe PaymentIntent.
 * Place this file in the same /admin/ directory as checkout.php.
 *
 * Requires: composer require stripe/stripe-php
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['pending_order'])) {
    echo json_encode(['error' => 'Session expired. Please go back and try again.']);
    exit();
}

// ── Load Stripe via Composer autoload ──
require_once __DIR__ . '/../vendor/autoload.php';

// ── Your Stripe TEST secret key ──
require_once __DIR__ . '/../config.php';
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$order = $_SESSION['pending_order'];

// Stripe amounts are in the smallest currency unit.
// ETB (Ethiopian Birr) is a zero-decimal currency — use whole numbers.
// If Stripe does NOT support ETB, fall back to USD (multiply by ~0.018 or hard-code).
// Stripe currently accepts ETB; confirm at: https://stripe.com/docs/currencies
$amount_in_cents = intval(round($order['total'] * 100)); // e.g. ETB 250.00 → 25000

try {
    $intent = \Stripe\PaymentIntent::create([
        'amount'               => $amount_in_cents,
        'currency'             => 'etb',          // Change to 'usd' if ETB not supported
        'automatic_payment_methods' => ['enabled' => true],
        'metadata'             => [
            'user_id'    => $_SESSION['user_id'],
            'product_id' => $order['product_id'],
            'quantity'   => $order['quantity'],
        ],
    ]);

    echo json_encode(['clientSecret' => $intent->client_secret]);

} catch (\Stripe\Exception\ApiErrorException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
