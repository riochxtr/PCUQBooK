<?php
session_start();
include 'Connect.php';
error_reporting(0);

$loginError = false;
$signupError = false;
$signupSuccess = false;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendemail_confirm($UserName, $Email){
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
    $mail->addAddress($Email);

    $mail->isHTML(true); 
    $mail->Subject = 'PCUQBook Registration';

    $email_template = "
    <h1>Welcome to PCUQBook, {$UserName}!</h1>
    <h3>This is a confirmation that your registration was successful.</h3>
    <p>We're excited to have you on board. You can now log in and start reserving facilities!</p>
    <br><br>
    <p>Cheers,<br>PCUQBook Team</p>
    ";

    $mail->Body = $email_template;
    $mail->send();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCUQBook | Login</title>
    <link rel="stylesheet" href="land.css">
</head>
<body>

    <div id="loader">
        <div class="spinner"></div>
    </div>
    <div class="overlay"></div>

    <nav id="nav-bar">
        <div class="logo">PCUQBook</div>
        <div class="menu-icon" id="menu-icon">
            <i class="fa fa-bars"></i>
        </div>
        <div>
            <ul class="nav-ref" id="nav-links">
                <li><button class="nav-btn" onclick="openPopover('login')">Log In</button></li>
                <li><button class="nav-btn" onclick="openPopover('signup')">Sign Up</button></li>
            </ul>
        </div>
    </nav>

    <div class="content">
        <h1>Welcome to PCUQBook</h1>
        <h2>Philippine Christian University Facility Reservation</h2>
    </div>

    <div class="popover-wrapper" id="popover-form" style="display: none;">
        <div class="popover">
            <div>
                <form class="form" id="login-form" method="POST">
                    <span class="close-btn" onclick="closePopover()">&times;</span>
                    <p id="form-title" class="form-title">Log In</p>
                    <div class="input-container">
                        <input type="text" placeholder="Enter email" name="Uname" required>
                    </div>
                    <div class="input-container">
                        <input type="password" placeholder="Enter password" name="pass" required>
                    </div>
                    <button type="submit" class="submit" name="signIn">Sign in</button>

                    <?php
                   if (isset($_POST['signIn'])) {
                    $UserName = $_POST['Uname'];
                    $Password = $_POST['pass'];
                
                    // First get the user record including the hashed password
                    $sql = "SELECT * FROM users WHERE UserName='$UserName'";
                    $result = $conn->query($sql);
                
                    if ($result->num_rows > 0) {
                        $user = $result->fetch_assoc();
                        // Verify the password against the hash
                        if (password_verify($Password, $user['Password'])) {
                            session_start();
                            $_SESSION['Uname'] = $user['UserName'];
                
                            if ($user['role'] == 'admin') {
                                sleep(3);
                                header("Location: admin/Dashboard.php");
                                exit();
                            } else {
                                sleep(3);
                                header("Location: Homepage.php");
                                exit();
                            }
                        } else {
                            $loginError = true;
                            echo "<p class='error-message'>Incorrect Username or Password</p>";
                        }
                    } else {
                        $loginError = true;
                        echo "<p class='error-message'>Incorrect Username or Password</p>";
                    }
                }
                    ?>

                    <p class="signup-link">
                        No account? <a href="#" onclick="openPopover('signup')">Sign up</a>
                    </p>
                    <p class="signup-link">
                       <a href="forgot.php">Forgot Password?</a>
                    </p>
                </form>
            </div>

            <div>
                <form class="form" id="signup-form" style="display: none;" method="POST">
                    <p class="title">Register</p>
                    <span class="close-btn" onclick="closePopover()">&times;</span>
                    <p class="message">Signup now and get full access to our site.</p>
                    <label>
                        <input required type="text" class="input" name="Uname">
                        <span>Username</span>
                    </label>
                    <label>
                        <input required type="email" class="input" name="email">
                        <span>Email</span>
                    </label>
                    <label>
                        <input required type="password" class="input" name="pass">
                        <span>Password</span>
                    </label>
                    <button class="submit" name="signUp">Submit</button>

                    <?php
                    if (isset($_POST['signUp'])) {
                        $UserName = $_POST['Uname'];
                        $Email = $_POST['email'];
                        $Password = $_POST['pass'];
                    
                        // Hash the password before storing it
                        $hashedPassword = password_hash($Password, PASSWORD_DEFAULT);
                    
                        $checkQuery = "SELECT * FROM users WHERE Email = '$Email' OR UserName = '$UserName'";
                        $result = $conn->query($checkQuery);
                    
                        if ($result->num_rows > 0) {
                            $signupError = true;
                            echo "<p class='error-message'>This Username or email is already registered. Please use another.</p>";
                        } 
                        else {
                            // Use the hashed password in the insert query
                            $insertQuery = "INSERT INTO users(UserName, Email, Password, role) VALUES ('$UserName', '$Email', '$hashedPassword', 'user')";
                    
                            if ($conn->query($insertQuery) === TRUE) {
                                $signupSuccess = true;
                                sleep(3);
                                sendemail_confirm("$UserName", "$Email");
                            } else {
                                $signupError = true;
                                echo "<p class='error-message'>There was an error in signing up: " . $conn->error . "</p>";
                            }
                        }
                    }

                    if ($signupSuccess) {
                        echo "<p class='success-message'>Registration successful! Please Check your email for Confirmation.</p>";
                    }
                    ?>

                    <p class="signin">Already have an account? 
                        <a href="#" onclick="openPopover('login')">Sign in</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openPopover(mode) {
            const popover = document.getElementById('popover-form');
            const loginForm = document.getElementById('login-form');
            const signupForm = document.getElementById('signup-form');
            const title = document.getElementById('form-title');

            if (mode === 'login') {
                loginForm.style.display = 'block';
                signupForm.style.display = 'none';
                title.textContent = 'Log In';
            } else {
                loginForm.style.display = 'none';
                signupForm.style.display = 'block';
                title.textContent = 'Sign Up';
            }

            popover.style.display = 'flex';
        }

        function closePopover() {
            document.getElementById('popover-form').style.display = 'none';
        }
    </script>

    <?php if ($loginError): ?>
    <script>
        window.onload = function() {
            openPopover('login');
        };
    </script>
    <?php endif; ?>

    <?php if ($signupError || $signupSuccess): ?>
    <script>
        window.onload = function() {
            openPopover('signup');
        };
    </script>
    <?php endif; ?>

    <script>
        window.addEventListener("load", function () {
            const loader = document.getElementById("loader");
            loader.style.opacity = "0";
            loader.style.visibility = "hidden";
            loader.style.transition = "opacity 0.3s ease";
        });
    
        document.addEventListener("DOMContentLoaded", function () {
            const loginForm = document.getElementById("login-form");
            const signupForm = document.getElementById("signup-form");
    
            if (loginForm) {
                loginForm.addEventListener("submit", function () {
                    document.getElementById("loader").style.visibility = "visible";
                    document.getElementById("loader").style.opacity = "1";
                });
            }
    
            if (signupForm) {
                signupForm.addEventListener("submit", function () {
                    document.getElementById("loader").style.visibility = "visible";
                    document.getElementById("loader").style.opacity = "1";
                });
            }
        });
    </script>
    
</body>
</html>
