<?php
session_start();
include "../db.php";

if(isset($_SESSION['user_id'])){

    $sql1 = "SELECT * FROM categories";
    $result1 = mysqli_query($conn, $sql1);

    if($_SESSION['user_role'] == "admin"){

        if(isset($_POST['submit'])){

            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $stock = $_POST['stock'];
            $category_id = $_POST['category_id'];

            // get category name from id
            $cat_sql = "SELECT name FROM categories WHERE id='$category_id'";
            $cat_result = mysqli_query($conn, $cat_sql);
            $cat_row = mysqli_fetch_assoc($cat_result);
            $category_name = $cat_row['name'];

            // image upload
            $image = $_FILES['image']['name'];
            $temp_location = $_FILES['image']['tmp_name'];
            $upload_location = "../image/";

            move_uploaded_file($temp_location, $upload_location.$image);

            // insert product
            $sql = "INSERT INTO products 
                    (name, description, price, stock, image, category_id, category_name)
                    VALUES
                    ('$name', '$description', '$price', '$stock', '$image', '$category_id', '$category_name')";

            $result = mysqli_query($conn, $sql);

            if($result){
                echo "Product added successfully";
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
    <title>Add Product</title>
    <link rel="stylesheet" href="dash.css">
</head>
<body>

<div class="dashboard_sidebar">
    <ul>
        <li><a href="addproduct.php">Add Product</a></li>
        <li><a href="#">View Orders</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>

<div class="dashboard_main">

<form action="addproduct.php" method="post" enctype="multipart/form-data">

    <input type="text" name="name" placeholder="Enter Product Name" required>

    <textarea name="description" placeholder="Enter Description"></textarea>

    <input type="number" name="price" placeholder="Enter Price" required>

    <input type="number" name="stock" placeholder="Enter Stock" required>

    <h3>Upload Image</h3>
    <input type="file" name="image" required>

    <br><br>

    <select name="category_id" required>
        <option value="">Select Category</option>
        <?php while($row = mysqli_fetch_assoc($result1)){ ?>
            <option value="<?php echo $row['id']; ?>">
                <?php echo $row['name']; ?>
            </option>
        <?php } ?>
    </select>

    <br><br>

    <input type="submit" name="submit" value="Add Product">

</form>

</div>

</body>
</html>