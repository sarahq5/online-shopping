<?php
session_start();
include "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION['pending_order'])) {
    header("Location: ../index.php");
    exit();
}

$order   = $_SESSION['pending_order'];
$user_id = $_SESSION['user_id'];

/* ── Fetch product details for display ── */
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $order['product_id']);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: ../index.php");
    exit();
}

/* ── Fetch user info ── */
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — Nova Shop</title>
    <link rel="stylesheet" href="../inde.css">
    <link rel="stylesheet" href="../shop.css">
    <link rel="stylesheet" href="checkout.css">
    <!-- Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>

<!-- HEADER -->
<header class="header header--slim">
    <a href="../index.php" class="logo">Nova Shop</a>
    <nav>
        <ul>
            <li><a href="../view_orders.php">My Orders</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<main class="checkout-main">

    <div class="checkout-wrapper">

        <!-- LEFT: ORDER SUMMARY -->
        <div class="checkout-summary">
            <h2 class="section-title">Order Summary</h2>

            <div class="summary-product">
                <img
                    src="../image/<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                    class="summary-thumb"
                >
                <div class="summary-details">
                    <p class="summary-name"><?php echo htmlspecialchars($product['name']); ?></p>
                    <p class="summary-qty">Qty: <?php echo $order['quantity']; ?></p>
                    <p class="summary-unit">ETB <?php echo number_format($order['unit_price'], 2); ?> each</p>
                </div>
            </div>

            <div class="summary-line">
                <span>Subtotal</span>
                <span>ETB <?php echo number_format($order['total'], 2); ?></span>
            </div>
            <div class="summary-line">
                <span>Shipping</span>
                <span class="free-tag">Free</span>
            </div>
            <div class="summary-line summary-total">
                <span>Total</span>
                <span>ETB <?php echo number_format($order['total'], 2); ?></span>
            </div>

            <div class="test-badge">
                🧪 Stripe Test Mode — use card <strong>4242 4242 4242 4242</strong>
            </div>
        </div>

        <!-- RIGHT: PAYMENT FORM -->
        <div class="checkout-payment">
            <h2 class="section-title">Payment Details</h2>

            <div id="payment-message" class="payment-message hidden"></div>

            <div class="field-group">
                <label class="field-label">Name on card</label>
                <input
                    type="text"
                    id="cardholder-name"
                    class="field-input"
                    placeholder="Abebe Bikila"
                    value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>"
                >
            </div>

            <div class="field-group">
                <label class="field-label">Card details</label>
                <div id="card-element" class="stripe-element"></div>
                <div id="card-errors" class="card-errors" role="alert"></div>
            </div>

            <button id="submit-btn" class="btn-pay">
                <span id="btn-text">Pay ETB <?php echo number_format($order['total'], 2); ?></span>
                <span id="btn-spinner" class="spinner hidden"></span>
            </button>

            <p class="secure-note">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="14" height="14">
                    <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd"/>
                </svg>
                Secured by Stripe. Your card info is never stored.
            </p>
        </div>

    </div>
</main>

<footer class="footer">
    <p>&copy;2026 Nova Shop</p>
</footer>

<script>
(async () => {
    // ── 1. Initialize Stripe with your TEST publishable key ──
   const stripe = Stripe('<?php echo STRIPE_PUBLIC_KEY; ?>');
    const elements = stripe.elements();

    // ── 2. Mount the card element ──
    const cardElement = elements.create('card', {
        style: {
            base: {
                color:           '#1a1a2e',
                fontFamily:      '"DM Sans", sans-serif',
                fontSize:        '16px',
                fontSmoothing:   'antialiased',
                '::placeholder': { color: '#9ca3af' },
            },
            invalid: { color: '#e53e3e', iconColor: '#e53e3e' },
        },
        hidePostalCode: false,
    });
    cardElement.mount('#card-element');

    cardElement.on('change', ({ error }) => {
        const display = document.getElementById('card-errors');
        display.textContent = error ? error.message : '';
    });

    // ── 3. On submit: create PaymentIntent server-side, then confirm ──
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.addEventListener('click', async () => {
        setLoading(true);

        // 3a. Ask our server to create a PaymentIntent
        const res = await fetch('create_payment_intent.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({}),
        });

        const { clientSecret, error: serverError } = await res.json();

        if (serverError) {
            showMessage(serverError);
            setLoading(false);
            return;
        }

        // 3b. Confirm payment with the card element
        const { error: stripeError } = await stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: cardElement,
                billing_details: {
                    name: document.getElementById('cardholder-name').value,
                },
            },
        });

        if (stripeError) {
            showMessage(stripeError.message);
            setLoading(false);
        } else {
            // Payment succeeded — redirect to success page
            window.location.href = 'payment_success.php';
        }
    });

    function setLoading(on) {
        submitBtn.disabled = on;
        document.getElementById('btn-text').classList.toggle('hidden', on);
        document.getElementById('btn-spinner').classList.toggle('hidden', !on);
    }

    function showMessage(msg) {
        const el = document.getElementById('payment-message');
        el.textContent = msg;
        el.classList.remove('hidden');
        setTimeout(() => el.classList.add('hidden'), 6000);
    }
})();
</script>

</body>
</html>
