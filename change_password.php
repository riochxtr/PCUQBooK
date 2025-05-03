<?php
include 'Connect.php';
session_start();

$errors = "";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_success($email){
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
    $mail->Subject = 'Password Reset Successfully';

    $email_template = "
    <p>Hello User,</p>

    <p>This is to inform you that your PCUQBook account password has been successfully changed.</p>

    <p>If you did not make this change, please contact our support team immediately.</p>

    <p>Thank you,<br>The PCUQBook Team</p>
    ";

    $mail->Body = $email_template;
    $mail->send();
}

if (!isset($_SESSION['verified_email'])) {
    header("Location: forgot.php");
    exit();
}

if (isset($_POST['change'])) {
    $newPass = mysqli_real_escape_string($conn, $_POST['Npass']);
    $confirmPass = mysqli_real_escape_string($conn, $_POST['Cpass']);
    $email = $_SESSION['verified_email'];

    if (empty($newPass) || empty($confirmPass)) {
        $errors = "<p style='color: red; text-align: center;'>All password fields are required.</p>";
    } elseif ($newPass !== $confirmPass) {
        $errors = "<p style='color: red; text-align: center;'>New password and confirmation don't match.</p>";
    } else {
        $hashedPassword = password_hash($newPass, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE users SET password = '$hashedPassword', code = NULL WHERE email = '$email'";
        if (mysqli_query($conn, $updateQuery)) {
            unset($_SESSION['verified_email']);

            send_success($email);
            header("Location: landing.php");
            exit();
        } else {
            $errors = "<p style='color: red; text-align: center;'>Something went wrong. Please try again.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Set New Password</title>
  <link rel="stylesheet" href="forgot.css">
</head>
<body>
  <div class="container">
    <form class="forgot-password-form" method="POST">
      <h2>Set New Password</h2>
      <p>You can now change your password</p>
      <hr>
      <div class="input-container">
        <input type="password" name="Npass" placeholder="New Password" required>
        <input type="password" name="Cpass" placeholder="Confirm Password" required>
      </div>

      <?php if (!empty($errors)) echo $errors; ?>
      
      <button type="submit" name="change" class="reset-btn">Continue</button>
      <a href="landing.php" class="back-btn">Back to login</a>
    </form>
  </div>
</body>
</html>
