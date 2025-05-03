<?php
    error_reporting(0);
    session_start();
    include("Connect.php");

    $message = '';

    if (!isset($_SESSION['Uname'])) {
        header("Location: landing.php");
        exit();
    }

    $UserName = $_SESSION['Uname'];

    $UserEmail = "Email not found";
    $add = "";
    $dob = "";
    $ph = "";

    $emailQuery = "SELECT email FROM users WHERE username = ?";
    $emailStmt = $conn->prepare($emailQuery);
    $emailStmt->bind_param("s", $UserName);
    $emailStmt->execute();
    $emailResult = $emailStmt->get_result();

    if ($emailResult->num_rows > 0) {
        $row = $emailResult->fetch_assoc();
        $UserEmail = $row['email'];
    }

    $Iinfo = "SELECT address, dob, phone FROM account WHERE email = ?";
    $IinfoStmt = $conn->prepare($Iinfo);
    $IinfoStmt->bind_param("s", $UserEmail);
    $IinfoStmt->execute();
    $IinfoResult = $IinfoStmt->get_result();

    if ($IinfoResult->num_rows > 0) {
        $row = $IinfoResult->fetch_assoc();
        $add = $row['address'];
        $dob = $row['dob'];
        $ph = $row['phone'];
    }

    $bookingQuery = "SELECT bookingID, facility, roomNo, date, start_time, end_time FROM booking WHERE email = ?";
    $bookingStmt = $conn->prepare($bookingQuery);
    $bookingStmt->bind_param("s", $UserEmail);
    $bookingStmt->execute();
    $bookingResult = $bookingStmt->get_result();

    if (isset($_POST['cancel_booking'])) {
        $bookingID = $_POST['bookingID'];
        
        $getBookingQuery = "SELECT * FROM booking WHERE bookingID = ?";
        $getBookingStmt = $conn->prepare($getBookingQuery);
        $getBookingStmt->bind_param("i", $bookingID);
        $getBookingStmt->execute();
        $bookingDetails = $getBookingStmt->get_result()->fetch_assoc();
        
        if ($bookingDetails) {
            $historyQuery = "INSERT INTO history (bookingID, email, facility, building, roomNo, date, start_time, end_time) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $historyStmt = $conn->prepare($historyQuery);
            $historyStmt->bind_param("isssisss", 
                $bookingDetails['bookingID'],
                $UserEmail, 
                $bookingDetails['facility'],
                $bookingDetails['building'],
                $bookingDetails['roomNo'],
                $bookingDetails['date'],
                $bookingDetails['start_time'],
                $bookingDetails['end_time']
            );
            
            if ($historyStmt->execute()) {
                $cancelQuery = "DELETE FROM booking WHERE bookingID = ?";
                $cancelStmt = $conn->prepare($cancelQuery);
                $cancelStmt->bind_param("i", $bookingID);
    
                if ($cancelStmt->execute()) {
                    $message = "<p style='color: green; text-align: center;'>Booking canceled and archived successfully.</p>";
                    $bookingStmt->execute();
                    $bookingResult = $bookingStmt->get_result();
                } else {
                    $message = "<p style='color: red; text-align: center;'>Error canceling the booking: " . $conn->error . "</p>";
                }
            } else {
                $message = "<p style='color: red; text-align: center;'>Error archiving booking: " . $conn->error . "</p>";
            }
        } else {
            $message = "<p style='color: red; text-align: center;'>Booking not found.</p>";
        }
    }
 
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
    }

    if (isset($_POST['save'])) {
        $Phone = $_POST['phone'];
        $Address = $_POST['address'];
        $DateOfBirth = $_POST['dob'];

        $checkEmailQuery = "SELECT email FROM account WHERE email = ?";
        $checkEmailStmt = $conn->prepare($checkEmailQuery);
        $checkEmailStmt->bind_param("s", $UserEmail);
        $checkEmailStmt->execute();
        $checkEmailResult = $checkEmailStmt->get_result();

        if ($checkEmailResult->num_rows > 0) {
            $updateQuery = "UPDATE account SET address = ?, dob = ?, phone = ? WHERE email = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ssss", $Address, $DateOfBirth, $Phone, $UserEmail);

            if ($updateStmt->execute()) {
                $message = "<p style='color: green; text-align: center;'>Profile updated successfully.</p>";
                $add = $Address;
                $dob = $DateOfBirth;
                $ph = $Phone;
            } else {
                $message = "<p style='color: red; text-align: center;'>There was an error updating the profile: " . $conn->error . "</p>";
            }
        } else {
            $insertQuery = "INSERT INTO account (email, address, dob, phone) VALUES (?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ssss", $UserEmail, $Address, $DateOfBirth, $Phone);

            if ($insertStmt->execute()) {
                $message = "<p style='color: green; text-align: center;'>Profile saved successfully.</p>";
                $add = $Address;
                $dob = $DateOfBirth;
                $ph = $Phone;
            } else {
                $message = "<p style='color: red; text-align: center;'>There was an error saving the profile: " . $conn->error . "</p>";
            }
        }
    }

    if(isset($_POST['change'])){
        $curPass = $_POST['Cpass'];
        $newPass = $_POST['Npass'];
        $confirmPass = $_POST['CNpass'];
        
        if (empty($curPass) || empty($newPass) || empty($confirmPass)) {
            $message = "<p style='color: red; text-align: center;'>All password fields are required.</p>";
        } 
        elseif ($newPass !== $confirmPass) {
            $message = "<p style='color: red; text-align: center;'>New password and confirmation don't match.</p>";
        } 
        elseif ($newPass === $curPass) {
            $message = "<p style='color: red; text-align: center;'>New password must be different from current password.</p>";
        }
        else {

            $sql = "SELECT Password FROM users WHERE UserName = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $UserName);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                if (password_verify($curPass, $user['Password'])) {
                    $newHashedPassword = password_hash($newPass, PASSWORD_DEFAULT);
                    
                    $updateSql = "UPDATE users SET Password = ? WHERE UserName = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("ss", $newHashedPassword, $UserName);
                    
                    if ($updateStmt->execute()) {
                        session_regenerate_id(true);
                        $message = "<p style='color: green; text-align: center;'>Password changed successfully!</p>";
                    } else {
                        $message = "<p style='color: red; text-align: center;'>Error updating password: " . $conn->error . "</p>";
                    }
                } else {
                    $message = "<p style='color: red; text-align: center;'>Current password is incorrect.</p>";
                }
            } else {
                $message = "<p style='color: red; text-align: center;'>User not found.</p>";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="acnt.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

        <?php if ($message != ''): ?>
                    <div class="message fade-in" style="display: block; color: #FFF;"><?php echo $message; ?></div>
                    <script>
                        setTimeout(function() {
                            document.querySelector('.message').style.display = 'none';
                        }, 5000);
                    </script>
        <?php endif; ?>

    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-pic-container">
                <h3><a class="exit" href="Homepage.php">&times;</a></h3>
                <div class="profile-pic" id="profilePic">
                    <i class="bi bi-person-circle"></i>
                </div>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($UserName); ?></h1>
            </div>
        </div>

        <form method="POST" action="">
            <div class="profile-section">
                <h2>
                    Personal Information
                    <div>
                        <button class="edit-btn" type="button" onclick="toggleEdit()">Edit Profile</button>
                        <button class="edit-btn save-btn" type="submit" name="save" style="display: none;">Save</button>
                        <button class="edit-btn cancel-btn" type="button" onclick="cancelEdit()" style="display: none;">Cancel</button>
                    </div>
                </h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Email</label>
                        <span><?php echo htmlspecialchars($UserEmail); ?></span>
                        <input type="email" value="<?php echo htmlspecialchars($UserEmail); ?>" disabled>
                    </div>
                    <div class="info-item">
                        <label>Phone</label>
                        <span><?php echo htmlspecialchars($ph); ?></span>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($ph); ?>">
                    </div>
                    <div class="info-item">
                        <label>Address</label>
                        <span><?php echo htmlspecialchars($add); ?></span>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($add); ?>">
                    </div>
                    <div class="info-item">
                        <label>Date of Birth</label>
                        <span><?php echo htmlspecialchars($dob); ?></span>
                        <input type="date" name="dob" value="<?php echo htmlspecialchars($dob); ?>">
                    </div>
                </div>
            </div>
        </form>

        <hr>
        <form method="POST">
            <div class="profile-section">
                <h2>
                    Account Settings
                </h2>
                <div class="info-grid">
                    <label>Current Password</label>
                    <span></span>
                    <input class="pass" type="password" name="Cpass">

                    <label>New Password</label>
                    <span></span>
                    <input class="pass" type="password" name="Npass">

                    <label>Confirm Password</label>
                    <span></span>
                    <input class="pass" type="password" name="CNpass">

                    <button class="btn-up" name="change">Save</button>
                </div>
            </div>
        </form>

        <hr>
        <div class="profile-section">
            <h2>Booking History</h2>
            <table class="booking-history">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Facility</th>
                        <th>Room Number</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($bookingResult->num_rows > 0) {
                        while ($row = $bookingResult->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['bookingID']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['facility']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['roomNo']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['end_time']) . "</td>";
                            echo "<td>
                                    <form method='POST' action='' onsubmit=\"return confirm('Are you sure you want to cancel this booking?');\">
                                        <input type='hidden' name='bookingID' value='" . $row['bookingID'] . "'>
                                        <button type='submit' name='cancel_booking' class='cancel-btn'>
                                            <i class='bi bi-x-circle'></i> Cancel
                                        </button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No bookings found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        let originalData = {};

        function toggleEdit() {
            const infoItems = document.querySelectorAll('.info-item');
            const editBtn = document.querySelector('.edit-btn');
            const saveBtn = document.querySelector('.save-btn');
            const cancelBtn = document.querySelector('.cancel-btn');

            infoItems.forEach(item => {
                const span = item.querySelector('span');
                const input = item.querySelector('input');
                originalData[input.name] = span.textContent;
            });

            infoItems.forEach(item => item.classList.add('editing'));
            editBtn.style.display = 'none';
            saveBtn.style.display = 'inline-block';
            cancelBtn.style.display = 'inline-block';
        }

        function cancelEdit() {
            const infoItems = document.querySelectorAll('.info-item');
            const editBtn = document.querySelector('.edit-btn');
            const saveBtn = document.querySelector('.save-btn');
            const cancelBtn = document.querySelector('.cancel-btn');

            infoItems.forEach(item => {
                const span = item.querySelector('span');
                const input = item.querySelector('input');
                span.textContent = originalData[input.name];
                input.value = originalData[input.name];
            });

            infoItems.forEach(item => item.classList.remove('editing'));
            editBtn.style.display = 'inline-block';
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
        }
    </script>
</body>
</html>