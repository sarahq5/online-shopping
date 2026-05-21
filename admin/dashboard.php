<?php
session_start();
 
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] != "admin") {
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Nova Shop Admin</title>
    <link rel="stylesheet" href="d.css">
</head>
<body>
 
<!-- ═══════════════════════════════════════
     SIDEBAR
════════════════════════════════════════ -->
<aside class="sidebar">
 
    <div class="sidebar__brand">Nova Shop</div>
    <span class="sidebar__label">Admin Panel</span>
 
    <nav class="sidebar__nav">
        <ul>
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="addproduct.php">Add Product</a></li>
            <li><a href="vieworders.php">View Orders</a></li>
            <li><a href="../logout.php" class="logout">Logout</a></li>
        </ul>
    </nav>
 
</aside>
 
<!-- ═══════════════════════════════════════
     MAIN CONTENT
════════════════════════════════════════ -->
<main class="admin-main">
 
    <div class="admin-main__header">
        <h1>Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></p>
    </div>
 
    <div class="dashboard-cards">
        <div class="dash-card">
            <span class="dash-card__label">Products</span>
            <span class="dash-card__hint">Manage your inventory</span>
            <a href="addproduct.php" class="dash-card__link">Add Product &rarr;</a>
        </div>
        <div class="dash-card">
            <span class="dash-card__label">Orders</span>
            <span class="dash-card__hint">View all customer orders</span>
            <a href="vieworders.php" class="dash-card__link">View Orders &rarr;</a>
        </div>
    </div>
 
</main>
 
</body>
</html>
