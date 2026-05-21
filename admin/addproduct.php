<?php
session_start();
include "../db.php";
 
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
 
if ($_SESSION['user_role'] != "admin") {
    header("Location: ../index.php");
    exit();
}
 
$sql1    = "SELECT * FROM categories";
$result1 = mysqli_query($conn, $sql1);
 
if (isset($_POST['submit'])) {
    $name        = $_POST['name'];
    $description = $_POST['description'];
    $price       = $_POST['price'];
    $stock       = $_POST['stock'];
    $category_id = $_POST['category_id'];
 
    // get category name
    $cat_sql    = "SELECT name FROM categories WHERE id='$category_id'";
    $cat_result = mysqli_query($conn, $cat_sql);
    $cat_row    = mysqli_fetch_assoc($cat_result);
    $category_name = $cat_row['name'];
 
    // image upload
    $image           = $_FILES['image']['name'];
    $temp_location   = $_FILES['image']['tmp_name'];
    $upload_location = "../image/";
    move_uploaded_file($temp_location, $upload_location . $image);
 
    // insert product
    $sql = "INSERT INTO products (name, description, price, stock, image, category_id, category_name)
            VALUES ('$name', '$description', '$price', '$stock', '$image', '$category_id', '$category_name')";
    $result = mysqli_query($conn, $sql);
 
    if ($result) {
        $success = "Product added successfully.";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product — Nova Shop Admin</title>
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
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="addproduct.php" class="active">Add Product</a></li>
            <li><a href="displayproduct.php">View Orders</a></li>
            <li><a href="../logout.php" class="logout">Logout</a></li>
        </ul>
    </nav>
 
</aside>
 
<!-- ═══════════════════════════════════════
     MAIN CONTENT
════════════════════════════════════════ -->
<main class="admin-main">
 
    <div class="admin-main__header">
        <h1>Add Product</h1>
        <p>Fill in the details below to add a new product.</p>
    </div>
 
    <!-- Messages -->
    <?php if (!empty($success)) : ?><p class="form-success"><?php echo $success; ?></p><?php endif; ?>
    <?php if (!empty($error))   : ?><p class="form-error"><?php echo $error; ?></p><?php endif; ?>
 
    <!-- Form -->
    <form class="product-form" action="addproduct.php" method="post" enctype="multipart/form-data">
 
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name" placeholder="Enter product name" required>
        </div>
 
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" placeholder="Enter description"></textarea>
        </div>
 
        <div class="form-row">
            <div class="form-group">
                <label>Price (ETB)</label>
                <input type="number" name="price" placeholder="0.00" required>
            </div>
            <div class="form-group">
                <label>Stock</label>
                <input type="number" name="stock" placeholder="0" required>
            </div>
        </div>
 
        <div class="form-group">
            <label>Category</label>
            <select name="category_id" required>
                <option value="">Select category</option>
                <?php while ($row = mysqli_fetch_assoc($result1)) : ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
 
        <div class="form-group">
            <label>Product Image</label>
            <input type="file" name="image" required>
        </div>
 
        <input type="submit" name="submit" value="Add Product">
 
    </form>
 
</main>
 
</body>
</html>