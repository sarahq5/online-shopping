<?php
include "db.php";
session_start();

if(isset($_POST['submit'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Proper prepared statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();

        // Verify hashed password
        if( $row['password'] === $password){
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['username'];
            $_SESSION['user_role'] = $row['role'];
            if($_SESSION['role'] == "user"){
                header("Location: admin/dashboard.php");
                }
            else{
                header("Location: index.php");
                exit();

            }
            echo "Login successful";
        } else {
            echo "Wrong password";
        }
    } else {
        echo "User not found";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Document</title>
</head>
<body>
    
<form action="login.php" method="post">
<div class="login"> 
    <div class="a1">
    <a href="login.php">Login</a>
   <a href="register.php">Sign up</a>
    </div>
        <input type="email" name="email" placeholder="Enter your email" required>
        <input type="password" name="password" placeholder="Enter password " required>
        <a class="a2" href="#">Forget your password?</a>
        <input type="submit" name="submit" value="login">
       
    </div>
</form>
</body>
</html>
