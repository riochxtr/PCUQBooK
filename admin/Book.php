<?php
include '../Connect.php';
session_start();

require '../vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


function cancel_Email($email, $facility, $building, $roomNo, $date, $start_time, $end_time) {
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
    $mail->Subject = 'Booking Cancelled';

    $email_template = "
    <h1>Facility Booking Cancelled</h1>
    <p>Dear {$email},</p>
    <p>We regret to inform you that your booking has been canceled. Here are the details:</p>
    
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
            <th align='left'>Start Time</th><td>{$start_time}</td>
        </tr>
        <tr>
            <th align='left'>End Time</th><td>{$end_time}</td>
        </tr>
    </table>

    <br><br>
    <p> If you have any questions, please contact the administration.</p>
    
    <br>
    <p>Thank you,<br><strong>PCUQBook Team</strong></p>
    ";


    $mail->Body = $email_template;
    $mail->send();
}

if (!isset($_SESSION['Uname'])) {
    header("Location: ../landing.php");
    exit();
}

// Handle deletion and move to history
if (isset($_POST['delete_booking_id'])) {
    $delete_booking_id = $_POST['delete_booking_id'];

    // Fetch the booking details
    $stmt = $conn->prepare("SELECT * FROM booking WHERE bookingID = ?");
    $stmt->bind_param("i", $delete_booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    if ($booking) {
        $stmt = $conn->prepare("INSERT INTO history (bookingID, email, facility, building, roomNo, date, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "isssssss",
            $booking['bookingID'],
            $booking['email'],
            $booking['facility'],
            $booking['building'],
            $booking['roomNo'],
            $booking['date'],
            $booking['start_time'],
            $booking['end_time']
        );
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM booking WHERE bookingID = ?");
        $stmt->bind_param("i", $delete_booking_id);
        $stmt->execute();
        $stmt->close();

        cancel_Email(
            $booking['email'],
            $booking['facility'],
            $booking['building'],
            $booking['roomNo'],
            $booking['date'],
            $booking['start_time'],
            $booking['end_time']
        );

        $_SESSION['success'] = "Booking deleted successfully, moved to history, and email sent!";
    } else {
        $_SESSION['success'] = "Booking not found.";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}



$sql = "SELECT * FROM booking";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="adm.css">
</head>
<body>
    
    <nav>
        <ul>
            <li class="logo">PCUQBook</li>
            <li><a href="Dashboard.php"><i class="bi bi-window-desktop"></i> &nbsp; Home</a></li>
            <li><a href="Users.php"><i class="bi bi-people-fill"></i> &nbsp; Users</a></li>
            <li><a href="Facility.php"><i class="bi bi-clipboard"></i> &nbsp; Facilities</a></li>
            <li><a href="Book.php"><i class="bi bi-book"></i> &nbsp; Booking</a></li>
            <li><a href="History.php"><i class="bi bi-journals"></i> &nbsp; History</a></li>
            <li><a class="out" href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="wrapper">
        <div class="section">
            <div><h1>Booking</h1></div>

            <?php if (isset($_SESSION['success'])): ?>
            <div class="popup" id="successPopup">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
            <?php endif; ?>
    
            <table>
                <tr>
                    <th>Booking ID</th>
                    <th>Email</th>
                    <th>Facility</th>
                    <th>Building</th>
                    <th>Room No</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Action</th>
                </tr>
            
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['bookingID']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['facility']}</td>
                                <td>{$row['building']}</td>
                                <td>{$row['roomNo']}</td>
                                <td>{$row['date']}</td>
                                <td>{$row['start_time']}</td>
                                <td>{$row['end_time']}</td>
                                <td>
                                    <form method='POST' onsubmit=\"return confirm('Are you sure you want to delete this booking?');\">
                                        <input type='hidden' name='delete_booking_id' value='{$row['bookingID']}'>
                                        <button type='submit' class='btn btn-danger btn-sm'>
                                            Cancel
                                        </button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No bookings found</td></tr>";
                }
                ?>
            </table>
            
        </div>
    </div>
    
    <script>
        const popup = document.getElementById("successPopup");
        if (popup) {
            popup.style.display = "block";
            setTimeout(() => {
                popup.style.display = "none";
            }, 5000);
        }
    </script>

</body>
</html>
