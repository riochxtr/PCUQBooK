<?php
include 'Connect.php';
session_start();

$errors = array();

if (isset($_POST['verify'])) {
    $otp = mysqli_real_escape_string($conn, $_POST['otp']);

    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];

        $query = "SELECT * FROM users WHERE email = '$email' AND code = '$otp'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $_SESSION['verified_email'] = $email;
            header("Location: change_password.php");
            exit();
        } else {
            $errors[] = "Invalid OTP code.";
        }
    } else {
        $errors[] = "Session expired. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Verify OTP</title>
  <link rel="stylesheet" href="forgot.css">
</head>
<body>
  <div class="container">
    <form class="forgot-password-form" method="POST">
      <h2>Verify OTP</h2>
      <p>We have sent a One-Time PIN to your email</p>
      <hr>
      <div class="input-container">
        <input type="text" name="otp" placeholder="Enter your OTP" required>
      </div>

      <?php if (!empty($errors)): ?>
        <div style="color:red;">
          <?php foreach ($errors as $error): ?>
            <p><?php echo $error; ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      
      <button type="submit" name="verify" class="reset-btn">Continue</button>
      <a href="landing.php" class="back-btn">Back to login</a>
    </form>
  </div>
</body>
</html>
