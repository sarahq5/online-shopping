<?php
session_start();
include "db.php";
 
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
 
if (!isset($_GET['product_id'])) {
    header("Location: ../index.php");
    exit();
}
 
$product_id = intval($_GET['product_id']);
$user_id    = $_SESSION['user_id'];
 
/* ── Fetch product ── */
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result  = $stmt->get_result();
$product = $result->fetch_assoc();
 
if (!$product) {
    echo "<p>Product not found.</p>";
    exit();
}
 
/* ── Handle quantity form submission → go to checkout ── */
if (isset($_POST['proceed'])) {
    $qty = max(1, intval($_POST['quantity']));
    if ($qty > $product['stock']) {
        $error = "Only {$product['stock']} items in stock.";
    } else {
        /* Store pending order in session */
        $_SESSION['pending_order'] = [
            'product_id' => $product_id,
            'quantity'   => $qty,
            'unit_price' => $product['price'],
            'total'      => $product['price'] * $qty,
        ];
        header("Location: checkout.php");
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
    <link rel="stylesheet" href="inde.css">
    <link rel="stylesheet" href="shop.css">
</head>
<body>
 
<!-- HEADER -->
<header class="header header--slim">
    <a href="index.php" class="logo">Nova Shop</a>
    <nav>
        <ul>
            <li><a href="view_orders.php">My Orders</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>
 
<!-- PRODUCT DETAIL -->
<main class="single-main">
 
    <a href="index.php" class="back-link">&#8592; Back to shop</a>
 
    <div class="single-grid">
 
        <!-- IMAGE -->
        <div class="single-image-wrap">
            <img
                src="../image/<?php echo htmlspecialchars($product['image']); ?>"
                alt="<?php echo htmlspecialchars($product['name']); ?>"
                class="single-image"
            >
        </div>
 
        <!-- INFO + ORDER FORM -->
        <div class="single-info">
 
            <span class="single-category">Nova Shop</span>
            <h1 class="single-title"><?php echo htmlspecialchars($product['name']); ?></h1>
 
            <p class="single-desc"><?php echo htmlspecialchars($product['description']); ?></p>
 
            <div class="single-meta">
                <span class="single-price">ETB <?php echo number_format($product['price'], 2); ?></span>
                <span class="single-stock <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-stock'; ?>">
                    <?php echo $product['stock'] > 0 ? "In stock ({$product['stock']})" : "Out of stock"; ?>
                </span>
            </div>
 
            <?php if (!empty($error)): ?>
                <p class="form-error"><?php echo $error; ?></p>
            <?php endif; ?>
 
            <?php if ($product['stock'] > 0): ?>
            <form action="singleorder.php?product_id=<?php echo $product_id; ?>" method="POST" class="order-form">
 
                <div class="qty-row">
                    <label for="quantity">Quantity</label>
                    <div class="qty-control">
                        <button type="button" class="qty-btn" onclick="adjustQty(-1)">&#8722;</button>
                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            value="1"
                            min="1"
                            max="<?php echo $product['stock']; ?>"
                            class="qty-input"
                            onchange="updateTotal()"
                        >
                        <button type="button" class="qty-btn" onclick="adjustQty(1)">&#43;</button>
                    </div>
                </div>
 
                <div class="order-total">
                    <span>Total</span>
                    <span id="total-display">ETB <?php echo number_format($product['price'], 2); ?></span>
                </div>
 
                <button type="submit" name="proceed" class="btn-buy">
                    Proceed to Checkout
                </button>
 
            </form>
            <?php else: ?>
                <p class="out-msg">This product is currently out of stock.</p>
            <?php endif; ?>
 
        </div>
    </div>
</main>
 
<footer class="footer">
    <p>&copy;2026 Nova Shop</p>
</footer>
 
<script>
const unitPrice = <?php echo floatval($product['price']); ?>;
 
function adjustQty(delta) {
    const input = document.getElementById('quantity');
    const max   = parseInt(input.max);
    let val     = parseInt(input.value) + delta;
    val = Math.max(1, Math.min(max, val));
    input.value = val;
    updateTotal();
}
 
function updateTotal() {
    const qty   = parseInt(document.getElementById('quantity').value) || 1;
    const total = (unitPrice * qty).toFixed(2);
    document.getElementById('total-display').textContent = 'ETB ' + parseFloat(total).toLocaleString('en-ET', {minimumFractionDigits: 2});
}
</script>
 
</body>
</html>
 