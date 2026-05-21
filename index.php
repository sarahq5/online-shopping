<?php
session_start();
include "db.php";

/* -------------------------
   CATEGORY FILTER + RANDOM PRODUCTS
--------------------------*/
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

if ($category_id > 0) {

    $sql_product = "SELECT * FROM products
                    WHERE category_id = $category_id
                    ORDER BY RAND()";

} else {

    $sql_product = "SELECT * FROM products
                    ORDER BY RAND()";
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
    <title>Nova Shop</title>

    <link rel="stylesheet" href="inde.css">
</head>

<body>

<!-- HEADER -->
<header class="header">

    <a href="index.php" class="logo">Nova Shop</a>
<section class="hero">

    <div class="hero-content">

        <h1>Luxury Fashion Collection</h1>

        <p>
            Discover premium styles curated for modern elegance.
        </p>
    </div>
</section>
    <!-- CATEGORY MENU -->
    <ul class="category_menu">

        <?php
        if(mysqli_num_rows($result_category) > 0){

            while($row_category = mysqli_fetch_assoc($result_category)){
        ?>

            <li>
                <a href="index.php?category_id=<?php echo $row_category['id']; ?>">
                    <?php echo $row_category['name']; ?>
                </a>
            </li>

        <?php
            }
        }
        ?>

    </ul>

    <!-- NAVIGATION -->
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

<?php
if(mysqli_num_rows($result_product) > 0){

    while($row_product = mysqli_fetch_assoc($result_product)){
?>

    <div class="product">

        <img
            src="image/<?php echo $row_product['image']; ?>"
            alt="<?php echo $row_product['name']; ?>"
        >

        <h3><?php echo $row_product['name']; ?></h3>

        <p><?php echo $row_product['description']; ?></p>

        <p>
            Stock:
            <?php echo $row_product['stock']; ?>
        </p>

        <p class="productprice">
            ETB <?php echo $row_product['price']; ?>
        </p>

        <?php if(isset($_SESSION['user_id'])) { ?>

            <a href="singleorder.php?product_id=<?php echo $row_product['id']; ?>">
                Buy Now
            </a>

        <?php } else { ?>

            <a href="login.php">
                Buy Now
            </a>

        <?php } ?>

    </div>

<?php
    }

} else {

    echo "<p>No products found</p>";
}
?>

</main>

<!-- FOOTER -->
<footer class="footer">
    <p>&copy;2026 Nova Shop</p>
</footer>

</body>
</html>