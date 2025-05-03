<?php
include 'Connect.php';

$email = "";
$errors = array();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_otp($email, $otp){
    $mail = new PHPMailer(true);
    // $mail->SMTPDebug = 2;

    $mail->isSMTP();
    $mail->SMTPAuth   = true;

    $mail->Host       = 'smtp.gmail.com'; 
    $mail->Username   = 'pcuqbook@gmail.com';  
    $mail->Password   = 'hdmk srpw wefl fhvj';

    $mail->SMTPSecure = "tls";
    $mail->Port       = 587;    

    $mail->setFrom('pcuqbook@gmail.com', 'PCUQBook');
    $mail->addAddress($email);

    $mail->isHTML(true); 
    $mail->Subject = 'Password Reset Request - PCUQBook';

    $email_template = "
    <p>We received a request to reset your PCUQBook account password.</p>

    <p>Your verification code: <strong> {$otp} </strong></p>

    <p>If you didn\'t request this, please ignore this email or contact support if you have concerns.</p>
    <p>Thank you,<br>The PCUQBook Team</p>
    ";

    $mail->Body = $email_template;
    $mail->send();
}

if (isset($_POST['check'])) {
  $email = mysqli_real_escape_string($conn, $_POST['email']);

  $checkEmail = "SELECT * FROM users WHERE email = '$email'";
  $result = mysqli_query($conn, $checkEmail);

  if (mysqli_num_rows($result) > 0) {
      $otp = rand(100000, 999999);

      $updateCode = "UPDATE users SET code = '$otp' WHERE email = '$email'";

      if (mysqli_query($conn, $updateCode)) {
          session_start();
          $_SESSION['email'] = $email;

          send_otp($email, $otp);
          header('Location: forgot_verify.php');
          exit();
      } else {
          $errors[] = "Failed to update OTP. Please try again.";
      }
  } else {
      $errors[] = "Email is not found in our system";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Forgot Password</title>
<link rel="stylesheet" href="forgot.css">
</head>
<body>
<div class="container">
  <form class="forgot-password-form" method="POST">
    <h2>Forgot Password</h2>
    <p>Enter Your Email Address</p>
    <hr>
    <div class="input-container">
      <input type="text" name="email" id="username" placeholder="Enter your email" required>
    </div>

    <?php if(!empty($errors)): ?>
      <div style="color:red;">
          <?php foreach($errors as $error): ?>
              <p><?php echo $error; ?></p>
          <?php endforeach; ?>
      </div>
    <?php endif; ?>
    
    <button type="submit" name="check" class="reset-btn">Continue</button>
    <a href="landing.php" class="back-btn">Back to login</a>
  </form>
</div>
</body>
</html>