<?php
session_start();

if(isset($_POST['verify'])){

$user_code = $_POST['code'];

if($user_code == $_SESSION['reset_code']){

header("Location: reset_password.php");
exit();

}else{

echo "Invalid verification code";

}

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/verify_code.css">
    <title>Document</title>
</head>
<body>
   <form class="verify-code" method="POST">

<h1>Enter Verification Code</h2>

<div class="input-container">
<label>VERIFY</label>
<input type="text" name="code" required>
</div>
<br></br>

<button type="submit" name="verify">Verify</button>

</form> 
</body>
</html>
