<?php
session_start();
include "db.php";

/* -------------------------
   CATEGORY FILTER (FIXED)
--------------------------*/
if(isset($_GET['category_id'])){

    $category_id = $_GET['category_id'];

    $sql_product = "SELECT * FROM products WHERE category_id = '$category_id'";

} else {

    $sql_product = "SELECT * FROM products";
}

$result_product = mysqli_query($conn, $sql_product);

/* -------------------------
   GET CATEGORIES
--------------------------*/
$sql_category = "SELECT * FROM categories";
$result_category = mysqli_query($conn, $sql_category);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="inde.css">
    <title>Nova Shop</title>
</head>

<body>

<!-- HEADER -->
<header class="header">

    <a href="index.php">Nova Shop</a>

    <ul>
        <?php while($row_category = mysqli_fetch_assoc($result_category)){ ?>
            <li>
                <a href="index.php?category_id=<?php echo $row_category['id']; ?>">
                    <?php echo $row_category['name']; ?>
                </a>
            </li>
        <?php } ?>
    </ul>

    <nav>
        <ul>
            <?php if(!isset($_SESSION['user_id'])) { ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Signup</a></li>
            <?php } else { ?>
                <li><a href="admin/dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php } ?>
        </ul>
    </nav>

</header>

<!-- PRODUCTS -->
<main class="main">

<?php while($row_product = mysqli_fetch_assoc($result_product)){ ?>

<div class="product">

    <img src="image/<?php echo $row_product['image']; ?>" alt="product image">

    <p><?php echo $row_product['name']; ?></p>

    <p><?php echo $row_product['description']; ?></p>

    <p>Stock: <?php echo $row_product['stock']; ?></p>

    <p class="productprice"><?php echo $row_product['price']; ?></p>

    <?php if(isset($_SESSION['user_id'])) {?>
    <a href="singleorder.php?user_id = <?php
     echo $_SESSION['user_id']; ?> &product_id=<?
     php echo $row_product_category['id']; ?>&product_price = <?php echo $row_product_category['price']; ?>">Buy Now</a>
<?php } ?>
  <?php if(!isset($_SESSION['user_id'])) {?>
    <a href="login.php">Buy Now</a>
<?php } ?>
</div>

<?php } ?>

</main>

<!-- FOOTER -->
<footer class="footer">
    <p>&copy; Nova Shop</p>
</footer>

</body>
</html>