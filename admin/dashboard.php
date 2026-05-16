<?php
session_start();
if(isset($_SESSION['user_id'])){
        if($_SESSION['user_role']== "admin"){

        }
        else{
            header("Location: ../dashboard.php");
           exit();
        }
}
else{
    header("Location: ../index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
     <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    
<div class="dashboard_sidebar">
    <ul>
             <li><a href="addproduct.php">Add Product</a></li>
         <li><a href="daisplayproduct.php">View Order</a></li>
             <li><a href="../logout.php">logout</a></li>
         
    </ul>
</div>
<div class="main">
    <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Ab maiores totam, necessitatibus corporis hic optio ea cupiditate esse possimus ad nisi, quo vero repudiandae dolorum enim molestiae non. Recusandae, numquam?</p>
</div>
   
<!-- timestamp:-2:30 -->
</body>
</html>