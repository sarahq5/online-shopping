<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['product_id'])) {
    header("Location: index.php");
    exit();
}

$product_id = intval($_GET['product_id']);
$user_id    = $_SESSION['user_id'];

/* ── Fetch product ── */
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit();
}

/* ── Handle quantity form → go to checkout ── */
if (isset($_POST['proceed'])) {
    $qty = max(1, intval($_POST['quantity']));
    if ($qty > $product['stock']) {
        $error = "Only {$product['stock']} item" . ($product['stock'] !== 1 ? 's' : '') . " left in stock.";
    } else {
        $_SESSION['pending_order'] = [
            'product_id' => $product_id,
            'quantity'   => $qty,
            'unit_price' => $product['price'],
            'total'      => $product['price'] * $qty,
        ];
        header("Location: admin/checkout.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> — Nova Shop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="single.css">
</head>
<body>

<!-- HEADER -->
<header class="header">
    <a href="index.php" class="logo">Nova<em>Shop</em></a>
    <nav class="header-nav">
        <a href="view_orders.php">My Orders</a>
        <a href="logout.php" class="nav-logout">Logout</a>
    </nav>
</header>

<!-- BREADCRUMB -->
<div class="breadcrumb">
    <a href="index.php">Shop</a>
    <span>/</span>
    <span><?php echo htmlspecialchars($product['category_name'] ?? 'Product'); ?></span>
    <span>/</span>
    <span class="bc-current"><?php echo htmlspecialchars($product['name']); ?></span>
</div>

<!-- PRODUCT -->
<main class="product-main">
    <div class="product-grid">

        <!-- IMAGE COLUMN -->
        <div class="image-col">
            <div class="image-frame">
                <img
                    src="image/<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                    class="product-img"
                    onerror="this.src='image/placeholder.png'"
                >
                <?php if ($product['stock'] > 0 && $product['stock'] < 5): ?>
                <div class="urgency-tag">Only <?php echo $product['stock']; ?> left</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- INFO COLUMN -->
        <div class="info-col">

            <p class="product-category">
                <?php echo htmlspecialchars($product['category_name'] ?? 'Nova Shop'); ?>
            </p>

            <h1 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h1>

            <p class="product-price">
                ETB <strong><?php echo number_format($product['price'], 2); ?></strong>
            </p>

            <div class="divider"></div>

            <p class="product-desc"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <div class="divider"></div>

            <!-- STOCK STATUS -->
            <div class="stock-row">
                <?php if ($product['stock'] > 0): ?>
                <span class="stock-dot stock-dot--in"></span>
                <span class="stock-label">In stock
                    <em>(<?php echo $product['stock']; ?> available)</em>
                </span>
                <?php else: ?>
                <span class="stock-dot stock-dot--out"></span>
                <span class="stock-label stock-label--out">Out of stock</span>
                <?php endif; ?>
            </div>

            <!-- ERROR -->
            <?php if (!empty($error)): ?>
            <p class="form-error">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>
                <?php echo htmlspecialchars($error); ?>
            </p>
            <?php endif; ?>

            <!-- ORDER FORM -->
            <?php if ($product['stock'] > 0): ?>
            <form
                action="singleorder.php?product_id=<?php echo $product_id; ?>"
                method="POST"
                class="order-form"
            >
                <div class="qty-block">
                    <label class="qty-label">Quantity</label>
                    <div class="qty-control">
                        <button type="button" class="qty-btn" onclick="adjustQty(-1)" aria-label="Decrease">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/></svg>
                        </button>
                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            value="1"
                            min="1"
                            max="<?php echo $product['stock']; ?>"
                            class="qty-input"
                            onchange="updateTotal()"
                            readonly
                        >
                        <button type="button" class="qty-btn" onclick="adjustQty(1)" aria-label="Increase">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                    </div>
                </div>

                <div class="order-total">
                    <span class="total-label">Total</span>
                    <span class="total-value" id="total-display">
                        ETB <?php echo number_format($product['price'], 2); ?>
                    </span>
                </div>

                <button type="submit" name="proceed" class="btn-checkout">
                    Proceed to Checkout
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>

            </form>

            <?php else: ?>
            <div class="out-of-stock-msg">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4M12 17h.01"/></svg>
                This product is currently out of stock.
            </div>
            <?php endif; ?>

            <!-- TRUST STRIP -->
            <div class="trust-strip">
                <div class="trust-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Secure checkout
                </div>
                <div class="trust-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Stripe payments
                </div>
                <div class="trust-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM1 10h22"/></svg>
                    Free delivery
                </div>
            </div>

        </div><!-- /info-col -->
    </div><!-- /product-grid -->
</main>

<footer class="footer">
    <p>&copy; 2026 Nova Shop. All rights reserved.</p>
</footer>

<script>
const unitPrice = <?php echo floatval($product['price']); ?>;
const maxStock  = <?php echo intval($product['stock']); ?>;

function adjustQty(delta) {
    const input = document.getElementById('quantity');
    let val = parseInt(input.value) + delta;
    val = Math.max(1, Math.min(maxStock, val));
    input.value = val;
    updateTotal();
}

function updateTotal() {
    const qty   = parseInt(document.getElementById('quantity').value) || 1;
    const total = unitPrice * qty;
    document.getElementById('total-display').textContent =
        'ETB ' + total.toLocaleString('en', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
</script>

</body>
</html>