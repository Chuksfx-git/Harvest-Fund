<?php
session_start();
include("../config/database.php");

if(isset($_POST['update'])){

$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$email = $_SESSION['reset_email'];

$stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
$stmt->bind_param("ss",$password,$email);

$stmt->execute();

echo "Password updated successfully";

}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../auth/css/reset_password.css">
    <title>Document</title>
</head>
<body>
    
</body>
</html>
<form class="reset-password" method="POST">

<h1>New Password</h2>
<div>
    <label for="new-password">New Password</label>
    <input type="password" name="password" required>
</div>
<br></br>

<button type="submit" name="update">Update Password</button>

</form>