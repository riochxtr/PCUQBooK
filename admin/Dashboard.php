<?php
include '../Connect.php';

session_start();
if (!isset($_SESSION['Uname']) ) {
    header("Location: ../landing.php");
    exit();
}

$sql = "SELECT COUNT(*) AS total_users FROM users WHERE role = 'user'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$totalUsers = $row['total_users'];

$facilties = "SELECT COUNT(*) AS total_facilities FROM facilities";
$facRes = $conn->query($facilties);
$count = $facRes->fetch_assoc();
$totalFacilities = $count['total_facilities'];

$bookings = "SELECT COUNT(*) AS total_books FROM booking";
$book = $conn->query($bookings);
$cnt = $book->fetch_assoc();
$totalbooks = $cnt['total_books'];

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
            <div><h1>Welcome Admin</h1></div>

            <div class="content">
                <div class="box">
                    <div class="total"><?php echo $totalUsers; ?></div>
                    <h3>USERS</h3>
                </div>
                
                <div class="box">
                    <div class="total"><?php echo $totalFacilities; ?></div>
                    <h3>FACILITIES</h3>
                </div>

                <div class="box">
                    <div class="total"><?php echo $totalbooks; ?></div>
                    <h3>BOOKING</h3>
                </div>
            </div>
    </div>

</body>
</html>