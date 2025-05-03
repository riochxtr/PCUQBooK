<?php
session_start();
include("Connect.php");

if (!isset($_SESSION['Uname']) ) {
    header("Location: landing.php");
    exit();
}

$UserName = $_SESSION['Uname'];

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_mess($email, $subj, $mess){
  $mail = new PHPMailer(true);
  // $mail->SMTPDebug = 2;

  $mail->isSMTP();
  $mail->SMTPAuth   = true;

  $mail->Host       = 'smtp.gmail.com'; 
  $mail->Username   = 'pcuqbook@gmail.com';  
  $mail->Password   = 'hdmk srpw wefl fhvj';

  $mail->SMTPSecure = "tls";
  $mail->Port       = 587;    

  $mail->setFrom('pcuqbook@gmail.com', $email);
  $mail->addAddress('pcuqbook@gmail.com');

  $mail->isHTML(true); 
  $mail->Subject = $subj;

  $email_template = "
  <h1> {$subj} </h1>
  <h3>You recieved an email from {$email} :
  <h4> {$mess} </h4>
  </div>
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
    <title>PCUQBook | Homepage</title>
    <link rel="stylesheet" href="Homepage.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://unpkg.com/scrollreveal"></script>
</head>
<body>
    <nav id="nav-bar">
        <div class="logo">PCUQBook</div>
        <div class="hamburger" id="hamburger">
          <i class="bi bi-list"></i>
        </div>
        <div>
            <ul class="nav-ref" id="nav-links">
                <li><a href="#main">Home</a></li>
                <li><a href="#fac">Facilities</a></li>
                <li><a href="#booking">Guidelines</a></li>
                <li><a href="#contact">Contacts</a></li>
                <li><button class="nav-btn" onclick="window.location.href='booking.php'">BOOK NOW!!</button></li>
                <div class="user">
                  <div id="user-icon" class="user-icon">
                    <i class="bi bi-person-circle"></i>
                  </div>
                
                  <div class="dropdown-menu" id="dropdown-menu">
                    <ul>
                      <p class="dropdown-header">Signed in as: <?php echo ($UserName); ?></p>
                      <li><a href="account.php">Profile</a></li>
                      <li><a href="logout.php">Logout</a></li>
                    </ul>
                  </div>
                </div>
            </ul>

        </div>
    </nav>

    <div class="hero-content">
        <h1 class="reveal" >Book facilities with ease, security, and flexibility</h1>
        <p class="reveal">PCUQBook empowers students and staff to reserve university spaces for events, classes, or creative projects—anytime, anywhere.</p>
    </div>

    <section class="message reveal" id="main">
       <div class="welc reveal">WELCOME TO </div> 
       <div class="welc1 reveal">PCUQBook</div>
        <br><br>
        <p class="reveal">
         "A well-managed facility is more than just a space—it’s a foundation for learning, collaboration, and growth. 
         When access is seamless and organized, students and educators can focus on what truly matters: building ideas, 
         sharing knowledge, and creating meaningful experiences. Systems like PCUQBook turn everyday spaces into opportunities 
         for connection and excellence."
        </p>
        <br><br><br><br>
      </section>

      <section id="home" class="reveal">
        <div class="image1">
            <img src="6.jpg" alt="Pcu-pic">
        </div>
        <div>
        <h1 class="reveal">PCUQBook is a web-based booking system designed for Philippine Christian University to allow booking of facilities through an online platform.
            It aims to revolutionize the management of facility scheduling and resource
            allocation within the University.</h1>
            <div>
                <button class="btn1" onclick="window.location.href='booking.php'"></a>BOOK NOW!!</button>
            </div>
        </div>        
    </section>

    <section class="destination reveal" id="fac">
        <h2 class="reveal" >Available Facilities at PCU</h2>
        <p class="reveal" >Explore a variety of well-equipped spaces tailored to meet the academic, physical, and cultural needs of the PCU community.</p>
      
        <div class="facility-grid">
          <div class="facility-card reveal">
            <img src="2.jpg" alt="Classroom">
            <h3>CLASSROOMS</h3>
            <p>Structured learning spaces equipped with desks, whiteboards, and essential teaching tools to foster a productive academic environment.</p>
          </div>
      
          <div class="facility-card reveal">
            <img src="9.png" alt="Smart Classroom">
            <h3>SMART CLASSROOMS</h3>
            <p>Technologically enhanced rooms that integrate advanced tools to support interactive and dynamic teaching methods.</p>
          </div>
      
          <div class="facility-card reveal">
            <img src="3.jpg" alt="Laboratory">
            <h3>LABORATORIES</h3>
            <p>Specialized spaces for experiments and practical applications in science, engineering, and technology disciplines.</p>
          </div>
      
          <div class="facility-card reveal">
            <img src="8.jpg" alt="Auditorium">
            <h3>AUDITORIUM</h3>
            <p>A large venue designed for lectures, performances, seminars, and other campus events that bring the community together.</p>
          </div>
      
          <div class="facility-card reveal">
            <img src="5.jpg" alt="Gymnasium">
            <h3>GYMNASIUM</h3>
            <p>A space dedicated to physical fitness, sports activities, and wellness programs for students and staff.</p>
          </div>
      
          <div class="facility-card reveal">
            <img src="7.jpg" alt="Library">
            <h3>LIBRARY</h3>
            <p>Resource center offering books, digital materials, and research tools to support academic growth and independent learning.</p>
          </div>
        </div>
      </section>
      
      <section id="booking" class="reveal">
        <h1 class="section-title reveal">Facility Booking Process & Guidelines</h1>
        <p class="section-intro reveal">
          Learn how to easily book facilities through PCUQBook and make the most of the resources available at Philippine Christian University. 
          Follow the steps and guidelines to ensure a smooth and successful booking experience.
        </p>
      
        <div class="booking-wrapper">
          <div class="booking-steps reveal">
            <h2>How to Book a Facility</h2>
            <div class="step reveal">
              <h3>Step 1: Login</h3>
              <p>Log in to your account using your registered Username and password. If you don't have an account, please <a href="landing.php" class="reg">register here</a>.</p>
            </div>
            <div class="step reveal">
              <h3>Step 2: Choose a Facility</h3>
              <p>Navigate to the facilities section and browse the available spaces. Select the facility you wish to book.</p>
            </div>
            <div class="step reveal">
              <h3>Step 3: Check Availability</h3>
              <p>Check the availability calendar to ensure the facility is free on your desired date and time.</p>
            </div>
            <div class="step reveal">
              <h3>Step 4: Make a Booking</h3>
              <p>Fill out the booking form with the required details, including the purpose of booking, date, and time.</p>
            </div>
            <div class="step reveal">
              <h3>Step 5: Confirmation</h3>
              <p>Once submitted, you will receive a confirmation email. Present this confirmation when using the facility.</p>
            </div>
          </div>
      
          <div class="booking-divider"></div>
      
          <div class="booking-guidelines reveal">
            <h2>Booking Guidelines</h2>
            <ul>
              <li class="reveal">Bookings must be made at least 24 hours in advance.</li>
              <li class="reveal">Cancellation of bookings should be done at least 12 hours before the reserved time to avoid penalties.</li>
              <li class="reveal">Ensure all personal and booking details are accurate to avoid complications.</li>
              <li class="reveal">Facilities must be used for their intended purpose and handled with care.</li>
              <li class="reveal">Report any damages or issues immediately to the facility management team.</li>
              <li class="reveal">Failure to show up without prior cancellation may result in a penalty or suspension of booking privileges.</li>
            </ul>
          </div>
        </div>
      </section>

      <section id="contact" class="reveal">
        <h1 class="reveal">REACH US</h1>
        <div class="contact-info reveal">
            <form class="message-form" method="POST">
                <label>Email</label>
                <input type="email" placeholder="Email" name="email" required>
                <label>Subject</label>
                <input type="text" placeholder="Subject" name="subj" required>
                <label>Message</label>
                <textarea name="message" placeholder="Your Message" rows="4" required></textarea>
                <button class="send-btn" name="send">Send</button>
            </form>

            <?php 
                if(isset($_POST['send'])){
                  $email = $_POST['email'];
                  $subj = $_POST['subj'];
                  $mess = $_POST['message'];

                  send_mess("$email", "$subj", "$mess");
                  echo "<script>showPopup();</script>";
                }
            ?>     
        </div>
            <div id="popup" class="popup">
              <p>Email has been sent successfully!</p>
            </div>  
    </section>

      

      <script>
        ScrollReveal().reveal('.reveal', {
          distance: '50px',
          duration: 1000,
          easing: 'ease-out',
          origin: 'bottom',
          interval: 200,
          reset: false
        });
      </script>      

      <script>
        const userIcon = document.getElementById("user-icon");
        const dropdownMenu = document.getElementById("dropdown-menu");

        userIcon.addEventListener("click", () => {
          dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
        });

        document.addEventListener("click", function (e) {
          if (!userIcon.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.style.display = "none";
          }
        });
      </script>

      <script>
        const hamburger = document.getElementById('hamburger');
        const navLinks = document.getElementById('nav-links');

        hamburger.addEventListener('click', () => {
          navLinks.classList.toggle('show-nav');
        });
      </script>

      <script>
        function showPopup() {
            const popup = document.getElementById("popup");
            popup.classList.add("show");
            setTimeout(() => {
                popup.classList.remove("show");
            }, 5000);
        }
      </script>

</body>
</html>