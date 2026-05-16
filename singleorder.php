<?php
session_start();
include 'db.php';

if(isset($_SESSION['user_id'])){
    header("Location: index.php");
}
else{


if(isset($_SESSION['user_id'])){
if(isset($_GET['user_id'], $_GET['product_id'],$_GET['product_price'])){
    $user_id = $_GET['user_id'];
    $product_id = $_GET['product_id'];
    $total_amount = $_GET['product_price'];
    $sql = "insert into single_order(user_id, product_id,total_amount) values('$user_id' ,'$product_id','$total_amount')";
    $result= mysqli_query($conn,$sql);
    if(!$result){
        echo "Error: {$conn->error}";
    }
    else{
        echo "order added succesfully!! <a href = 'index.php'>Buy More</a>";
    }
}
}
else
    header("Location: index.php");
}
?>