<?php
session_start();
include "../db.php";

if(isset($_SESSION['user_id'])){

    if($_SESSION['user_role'] == "admin"){

        // Get categories
        $sql1 = "SELECT * FROM categories";
        $result1 = mysqli_query($conn, $sql1);

        // Get product data
        if(isset($_GET['product_id'])){
            $product_id = $_GET['product_id'];

            $sql2 = "SELECT * FROM products WHERE id='$product_id'";
            $result2 = mysqli_query($conn, $sql2);
            $row2 = mysqli_fetch_assoc($result2);
        }

        // UPDATE PRODUCT
        if(isset($_POST['submit'])){

            $product_id = $_GET['product_id'];

            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $stock = $_POST['stock'];
            $category_name = $_POST['category_name'];

            $image = $_FILES['image']['name'];

            // If new image uploaded
            if(!empty($image)){

                $temp_location = $_FILES['image']['tmp_name'];
                $upload_location = "../image/";

                move_uploaded_file($temp_location, $upload_location.$image);

                $sql = "UPDATE products 
                        SET name='$name',
                            description='$description',
                            price='$price',
                            stock='$stock',
                            image='$image',
                            category_name='$category_name'
                        WHERE id='$product_id'";

            } else {

                $sql = "UPDATE products 
                        SET name='$name',
                            description='$description',
                            price='$price',
                            stock='$stock',
                            category_name='$category_name'
                        WHERE id='$product_id'";
            }

            $result = mysqli_query($conn, $sql);

            if($result){
                header("Location: daisplayproduct.php");
                exit();
            } else {
                echo "Error: " . $conn->error;
            }
        }

    } else {
        echo "Go for user dashboard";
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
    <title>Update Product</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<div class="dashboard_sidebar">
    <ul>
        <li><a href="addproduct.php">Add Product</a></li>
        <li><a href="daisplayproduct.php">View Products</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>

<div class="dashboard_main">

<form action="upadateproduct.php?product_id=<?php echo $product_id; ?>" method="post" enctype="multipart/form-data">

    <input type="text" name="name" value="<?php echo $row2['name']; ?>" required>

    <textarea name="description"><?php echo $row2['description']; ?></textarea>

    <input type="number" name="price" value="<?php echo $row2['price']; ?>" required>

    <input type="number" name="stock" value="<?php echo $row2['stock']; ?>" required>

    <br><br>

    <img src="../image/<?php echo $row2['image']; ?>" width="100">

    <input type="file" name="image">

    <h3>Current Category: <?php echo $row2['category_name']; ?></h3>

    <select name="category_name" required>
        <?php while($row = mysqli_fetch_assoc($result1)){ ?>
            <option value="<?php echo $row['name']; ?>"
                <?php if($row['name'] == $row2['category_name']) echo "selected"; ?>>
                <?php echo $row['name']; ?>
            </option>
        <?php } ?>
    </select>

    <br><br>

    <input class="button" type="submit" name="submit" value="Update Product">

</form>

</div>

</body>
</html>