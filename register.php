<?php
include "db.php";
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $email =$_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role= "user";

    $sql= "insert into users(name,email,password,phone,address,role) 
            values('$name','$email', '$password','$phone','$address','$role')";
    $result= mysqli_query($conn,$sql);
    if(!$result){
        echo "Error!: {$conn->error}";
        
    }
    else{
        echo"Registered Successfully";
    }
    
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="registe.css">
</head>
<body>
 <a class="shoplink" href="index.php">Shop</a> 
<div class="registerdiv">
    <div class="a1">
    <a href="login.php">Login</a>
   <a href="register.php">Sign up</a>
    </div>
            <form action="register.php" method="post">

            <input type="text" name="name" placeholder="Enter your name" required>
            <input type="email" name="email" placeholder="@gmail.com" required>
            <input type="password" name="password" placeholder="******" required>
            <input type="text " name="phone" placeholder="+251-***-***" required>
            <textarea name="address" id="" placeholder="Adress"></textarea>
            <input class="button" type="submit" name="submit" value="sign up">
    </form>


</div>
<div>


</div>
    
 
</body>
</html>


