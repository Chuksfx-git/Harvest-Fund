<?php
session_start();
include("../config/database.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$message = "";

if(isset($_POST['reset'])){

$email = trim($_POST['email']);

$stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 1){

$code = rand(100000,999999);

$_SESSION['reset_code'] = $code;
$_SESSION['reset_email'] = $email;

$mail = new PHPMailer(true);

try{

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'harvestfundbusiness@gmail.com';
$mail->Password = 'llrw xoxj tjbz vtqn';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

$mail->setFrom('harvestfundbusiness@gmail.com','Harvest Fund');
$mail->addAddress($email);

$mail->isHTML(true);
$mail->Subject = "Harvest Fund Password Reset Code";
$mail->Body = "Your Harvest Fund verification code is: <b>$code</b>";

$mail->send();

header("Location: verify_code.php");
exit();

}catch(Exception $e){

$message = "Email could not be sent.";

}

}else{

$message = "Email not found";

}

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
<link rel="stylesheet" href="../assets/css/forgot_password.css">
</head>

<body>

<form class="forgot-password" action="forgot_password.php" method="POST">

<h1>Forgot Password</h1>

<?php if($message != ""){ ?>
<p><?php echo $message; ?></p>
<?php } ?>

<div class="input-container">
<label>Email</label>
<input type="email" name="email" required>
</div>

<button type="submit" name="reset_request">Send Reset Code</button>

<div class="links">
<a href="./login.php">Back to Log In</a>
</div>

<div class="footer">
<p>&copy; 2025 Harvest Fund. All rights reserved.</p>
</div>

</form>

</body>
</html>