<?php
session_start();
include 'Connect.php';

if (!isset($_SESSION['Uname'])) {
    header("Location: landing.php");
    exit();
}

$UserName = $_SESSION['Uname'];

$emailQuery = "SELECT email FROM users WHERE username = '$UserName'";
$emailResult = $conn->query($emailQuery);

if ($emailResult->num_rows > 0) {
    $row = $emailResult->fetch_assoc();
    $UserEmail = $row['email'];
} else {
    $UserEmail = "Email not found";
}

$facilitiesQuery = "SELECT DISTINCT facility FROM facilities";
$facilitiesResult = $conn->query($facilitiesQuery);

$checkFacility = "SELECT * FROM facilities";

$bookingMessage = "";

// Send booking to user email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_booking($facility, $building, $roomNo, $date, $startTime, $endTime, $Iemail, $UserName){
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
    $mail->addAddress($Iemail);

    $mail->isHTML(true); 
    $mail->Subject = 'Booking Confirmation';

    $email_template = "
    <h1>Facility Booking Confirmed</h1>
    <p>Dear {$UserName},</p>
    <p>Thank you for booking with <strong>PCUQBook</strong>! Here are your booking details:</p>
    
    <table cellpadding='10' cellspacing='0' border='1' style='border-collapse: collapse;'>
        <tr>
            <th align='left'>Facility</th><td>{$facility}</td>
        </tr>
        <tr>
            <th align='left'>Building</th><td>{$building}</td>
        </tr>
        <tr>
            <th align='left'>Room No</th><td>{$roomNo}</td>
        </tr>
        <tr>
            <th align='left'>Date</th><td>{$date}</td>
        </tr>
        <tr>
            <th align='left'>Start Time</th><td>{$startTime}</td>
        </tr>
        <tr>
            <th align='left'>End Time</th><td>{$endTime}</td>
        </tr>
    </table>

    <br><br>
    <p>Please make sure to arrive at the facility on time.</p>
    <p>If you need to cancel please log in to your account.</p>
    
    <br>
    <p>Thank you,<br><strong>PCUQBook Team</strong></p>
    ";


    $mail->Body = $email_template;
    $mail->send();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PCUQBook Facility Booking</title>
  <link rel="stylesheet" href="book.css">
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
                <li><a href="Homepage.php">Home</a></li>
                <li><a href="Homepage.php#fac">Facilities</a></li>
                <li><a href="Homepage.php#booking">Guidelines</a></li>
                <li><a href="Homepage.php#contact">Contacts</a></li>
                <div class="user">
                  <div id="user-icon" class="user-icon">
                    <i class="bi bi-person-circle"></i>
                  </div>
                  <div class="dropdown-menu" id="dropdown-menu">
                    <ul>
                      <p class="dropdown-header">Signed in as: <?php echo htmlspecialchars($UserName); ?></p>
                      <li><a href="account.php">Profile</a></li>
                      <li><a href="logout.php">Logout</a></li>
                    </ul>
                  </div>
                </div>
            </ul>
        </div>
    </nav>

    <div class="book reveal"></div>

    <div class="booking-container reveal">
        <div class="booking-header reveal">
          <h1>Book a Facility</h1>
        </div>

        <form method="POST">
            <label for="facility">Facility:</label>
            <select name="facility" id="facility" required>
                <option disabled selected>Select a Facility</option>
                <?php
                if ($facilitiesResult->num_rows > 0) {
                    while ($row = $facilitiesResult->fetch_assoc()) {
                        echo "<option value='{$row['facility']}'>" . htmlspecialchars($row['facility']) . "</option>";
                    }
                }
                ?>
            </select>
            
            <label class="reveal" for="checkin">Date:</label>
            <input class="reveal" type="date" name="date" id="date" required>

            <label class="reveal" for="clockin">Clock-in:</label>
            <input class="reveal" type="time" name="clockin" id="clockin" required>

            <label class="reveal" for="clockout">Clock-out:</label>
            <input class="reveal" type="time" name="clockout" id="clockout" required>

            <button type="submit" name="search" class="btn btn-primary custom-btn reveal">
                <i class="bi bi-search"></i> Search
            </button>
        </form>
    </div>
    
        <?php
        if (isset($_POST['search'])) {
            $selectedFacility = $_POST['facility'];
            $selectedDate = $_POST['date'];
            $selectedStartTime = $_POST['clockin'];
            $selectedEndTime = $_POST['clockout'];

            $currentDate = date('Y-m-d');
            if ($selectedDate < $currentDate) {
                $bookingMessage = "<div class='error-message'>Error: You cannot book a facility for a past date.</div>";
                $facilitiesQuery = "SELECT DISTINCT facility FROM facilities"; 
                $facilitiesResult = $conn->query($facilitiesQuery);
            } 
            else {

                $validFacilityQuery = "SELECT * FROM facilities WHERE facility = '$selectedFacility'";
                $validFacilityResult = $conn->query($validFacilityQuery);

                if ($validFacilityResult->num_rows == 0) {
                    $bookingMessage = "<div class='error-message'>Error: Facility not found.</div>";
                } 
                else {
                    $availableRoomsQuery = "
                        SELECT * FROM facilities 
                        WHERE facility = '$selectedFacility' 
                        AND roomNo NOT IN (
                            SELECT roomNo FROM booking 
                            WHERE facility = '$selectedFacility' 
                            AND date = '$selectedDate'
                            AND (
                                (start_time < '$selectedEndTime' AND end_time > '$selectedStartTime') OR
                                (start_time < '$selectedStartTime' AND end_time > '$selectedEndTime')
                            )
                        )
                    ";

                    $facilitiesResult = $conn->query($availableRoomsQuery);

                    if ($facilitiesResult->num_rows > 0) {
                        $bookingMessage = "<div class='success-message'>Available rooms found! Please proceed below</div>";
                    } 
                    else {
                        $bookingMessage = "<div class='error-message'>Sorry, no available rooms in this facility for the selected time.</div>";
                    }
                }
            }
        }
        ?>

    <div class="fac-head">
        <h1 class="reveal">Facilities</h1>
        <div>
        <?php
        if ($bookingMessage) {
            echo $bookingMessage;
        }
        ?>
        </div>
    </div>

    <div class="facilities reveal">    
        <div class="facility-list">
        <?php
        if (isset($facilitiesResult) && $facilitiesResult->num_rows > 0) {
            while ($facility = $facilitiesResult->fetch_assoc()) {
                echo "
                <div class='facility-card'>
                    <h3>" . htmlspecialchars($facility['facility']) . "</h3>
                    <h1>" . htmlspecialchars($facility['roomNo']) . "</h1>
                    <h4>" . htmlspecialchars($facility['building']) . "</h4>
                    <button class='book-btn' onclick='openPopover(\"" . htmlspecialchars($facility['facility']) . "\", \"" . htmlspecialchars($facility['building']) . "\", \"" . htmlspecialchars($facility['roomNo']) . "\")'>Book Now</button>
                </div>";
            }
        } else {
            echo "<p>No facilities found for the selected criteria.</p>";
        }
        ?>
        </div>
    </div>

    <div id="popover" class="popover">
        <div class="popover-content">
            <h2>Booking details</h2><span class="close-btn" onclick="closePopover()">&times;</span>
            <form class="modal-form" method="POST">
                <label for="facility">Facility:</label>
                <select name="fac" id="facility">
                    <?php
                    if (isset($selectedFacility)) {
                        echo "<option value='" . htmlspecialchars($selectedFacility) . "' selected>" . htmlspecialchars($selectedFacility) . "</option>";
                    } else {
                        echo "<option disabled selected>Select a Facility</option>";
                    }
                    ?>
                    
                </select>

                <label for="building">Building:</label>
                <input type="text" name="building" id="building" readonly>

                <label for="roomNo">Room No:</label>
                <input type="text" name="rn" id="roomNo" readonly>

                <label for="checkin">Date:</label>
                <input type="date" name="petsa" id="popoverDate" 
                <?php if (isset($selectedDate)) echo "value='" . htmlspecialchars($selectedDate) . "'"; ?> readonly>

                <label for="clockin">Clock-in:</label>
                <input type="time" name="Ti" id="popoverClockIn" 
                <?php if (isset($selectedStartTime)) echo "value='" . htmlspecialchars($selectedStartTime) . "'"; ?> readonly>

                <label for="clockout">Clock-out:</label>
                <input type="time" name="To" id="popoverClockOut"
                <?php if (isset($selectedEndTime)) echo "value='" . htmlspecialchars($selectedEndTime) . "'"; ?> readonly>

                <button type="submit" name="book">Submit</button>
            </form>
        </div>
    </div>

    <?php
        if (isset($_POST['book'])) {
            $facility = $_POST['fac'];
            $building = $_POST['building'];
            $roomNo = $_POST['rn'];
            $date = $_POST['petsa'];
            $startTime = $_POST['Ti'];
            $endTime = $_POST['To'];
            $Iemail = $UserEmail;

            $validFacilityQuery = "SELECT * FROM facilities WHERE facility = '$facility' AND building = '$building' AND roomNo = '$roomNo'";
            $validFacilityResult = $conn->query($validFacilityQuery);

            if ($validFacilityResult->num_rows == 0) {
                echo "<p class='error-message'>Error: The selected facility, room, or building does not exist.</p>";
            } 
            
            else {   
                $CheckBooking = "SELECT * FROM booking WHERE facility = '$facility' AND roomNo = '$roomNo' AND date = '$date' 
                                AND start_time = '$startTime' AND end_time = '$endTime'";

                $CheckBookingResult = $conn->query($CheckBooking);

                if ($CheckBookingResult->num_rows > 0) {
                    echo "<p class='error-message'>The selected facility is already booked at this time.</p>";
                } else {
                    $insertQuery = "INSERT INTO booking (facility, building, roomNo, date, start_time, end_time, email) 
                                    VALUES ('$facility', '$building', '$roomNo', '$date', '$startTime', '$endTime', '$Iemail')";

                    if ($conn->query($insertQuery) === TRUE) {
                        echo "<p class='success-message'>Booking successfully made!</p>";

                        send_booking("$facility", "$building", "$roomNo", "$date", "$startTime", "$endTime", "$Iemail", "$UserName");

                    } else {
                        echo "<p class='error-message'>Error: " . $conn->error . "</p>";
                    }
                }
            }
        }
    ?>


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

    function openPopover(facility, building, roomNo) {
        const popover = document.getElementById('popover');
        const facilitySelect = document.querySelector('#popover select[name="fac"]');
        const buildingInput = document.querySelector('#popover input[name="building"]');
        const roomNoInput = document.querySelector('#popover input[name="rn"]');
        const dateInput = document.querySelector('#popover input[name="petsa"]');
        const clockInInput = document.querySelector('#popover input[name="Ti"]');
        const clockOutInput = document.querySelector('#popover input[name="To"]');
        
        // Set the values from the search form
        const searchDate = document.getElementById('date').value;
        const searchClockIn = document.getElementById('clockin').value;
        const searchClockOut = document.getElementById('clockout').value;
        
        // Set the values in the popover
        if (facilitySelect) {
            facilitySelect.value = facility;
        }
        if (buildingInput) {
            buildingInput.value = building;
        }
        if (roomNoInput) {
            roomNoInput.value = roomNo;
        }
        if (dateInput && searchDate) {
            dateInput.value = searchDate;
        }
        if (clockInInput && searchClockIn) {
            clockInInput.value = searchClockIn;
        }
        if (clockOutInput && searchClockOut) {
            clockOutInput.value = searchClockOut;
        }
        
        popover.style.display = 'flex';
    }

    function closePopover() {
        const popover = document.getElementById('popover');
        popover.style.display = 'none'; 
    }
</script>

</body>
</html>