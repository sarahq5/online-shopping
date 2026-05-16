<?php 
session_start();
include "../db.php";
if(isset($_SESSION['user_id'])){
    if($_SESSION['user_role'] == "admin"){
        if(isset($_GET['product_id'])){
            $product_id = $_GET['product_id'];
            $sql = "delete from product where id = '$product_id' ";
            $result = mysqli_query($conn,$sql) ;

            if(!$result){
                echo "Error!: {$conn->error}";
            }

            else{
                echo "Deleted successfuly <a href = 'diaspalyproduct.php'>Go Back</a>";
            }
        }
}
else{
    echo " go for user dashboard";
}}
else{
    header("Location: ../index.php");

}       
?>