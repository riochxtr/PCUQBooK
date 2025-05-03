<?php
include '../Connect.php';
session_start();

if (!isset($_SESSION['Uname'])) {
    header("Location: ../landing.php");
    exit();
}

$sql = "SELECT * FROM history";
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
        <div><h1>Booking History</h1></div>

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
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No bookings found</td></tr>";
            }
            ?>
        </table>
    </div>
</div>

<script>
        const popup = document.getElementById("successPopup");
        popup.style.display = "block";
        setTimeout(() => {
            popup.style.display = "none";
        }, 5000);
</script>

</body>
</html>
