<?php
/**
 * payment_success.php
 * Shown after Stripe confirms the payment.
 * Here we also save the order to the DB and clear the session.
 */

session_start();
include "../db.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['pending_order'])) {
    header("Location: ../index.php");
    exit();
}

$order   = $_SESSION['pending_order'];
$user_id = $_SESSION['user_id'];

/* ── Save order to database ── */
$stmt = $conn->prepare(
    "INSERT INTO orders (user_id, product_id, quantity, unit_price, total, status, created_at)
     VALUES (?, ?, ?, ?, ?, 'paid', NOW())"
);
$stmt->bind_param(
    "iidd",            // adjust types to your schema
    $user_id,
    $order['product_id'],
    $order['quantity'],
    $order['unit_price'],
    $order['total']
);
$stmt->execute();
$order_id = $conn->insert_id;

/* ── Decrease stock ── */
$stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
$stmt->bind_param("iii", $order['quantity'], $order['product_id'], $order['quantity']);
$stmt->execute();

/* ── Clear pending order from session ── */
unset($_SESSION['pending_order']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful — Nova Shop</title>
    <link rel="stylesheet" href="../inde.css">
    <link rel="stylesheet" href="../shop.css">
    <link rel="stylesheet" href="checkout.css">
</head>
<body>

<header class="header header--slim">
    <a href="../index.php" class="logo">Nova Shop</a>
    <nav>
        <ul>
            <li><a href="../view_orders.php">My Orders</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<main class="success-main">
    <div class="success-card">

        <div class="success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
        </div>

        <h1 class="success-title">Payment Successful!</h1>
        <p class="success-sub">Your order <strong>#<?php echo $order_id; ?></strong> has been placed.</p>

        <div class="success-summary">
            <div class="success-row">
                <span>Amount paid</span>
                <span>ETB <?php echo number_format($order['total'], 2); ?></span>
            </div>
            <div class="success-row">
                <span>Items</span>
                <span><?php echo $order['quantity']; ?></span>
            </div>
            <div class="success-row">
                <span>Status</span>
                <span class="paid-pill">Paid</span>
            </div>
        </div>

        <div class="success-actions">
            <a href="../view_orders.php" class="btn-orders">View My Orders</a>
            <a href="../index.php" class="btn-continue">Continue Shopping</a>
        </div>

    </div>
</main>

<footer class="footer">
    <p>&copy;2026 Nova Shop</p>
</footer>

</body>
</html>
