<?php
include '../Connect.php';
session_start();

if (!isset($_SESSION['Uname'])) {
    header("Location: ../landing.php");
    exit();
}

$error = "";
$successMessage = "";
$errorMessage = "";

if (isset($_POST['submit'])) {
    $facility = $_POST['fac'];
    $building = $_POST['building'];
    $room = $_POST['room'];

    $checkFacility = "SELECT * FROM facilities WHERE roomNo = '$room'";
    $result = $conn->query($checkFacility);

    if($result->num_rows > 0){
        $error = "Room number Already Exist!";
    }
    else{
        $insertQuery = "INSERT INTO facilities (facility, building, roomNo)
        VALUES ('$facility', '$building', '$room')";

        if ($conn->query($insertQuery) === TRUE) {
            $successMessage = "Facility added successfully!";
        } else {
            $errorMessage = "Error: " . $conn->error;
        }
    }
}

if (isset($_POST['delete_roomNo'])) {
    $roomNo = $_POST['delete_roomNo'];

    $deleteQuery = "DELETE FROM facilities WHERE roomNo = '$roomNo'";
    if ($conn->query($deleteQuery) === TRUE) {
        $successMessage = "Facility deleted successfully!";
    } else {
        $errorMessage = "Error deleting facility: " . $conn->error;
    }
}
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
            <div><h1>Facility Manager</h1></div>
            <button class="btn1" id="show-form">Add Facility</button>

            <table>
                <?php
                $facilityQuery = "SELECT DISTINCT facility FROM facilities";
                $facilityResult = $conn->query($facilityQuery);

                if ($facilityResult->num_rows > 0) {
                    while ($facilityRow = $facilityResult->fetch_assoc()) {
                        $currentFacility = $facilityRow['facility'];
                        echo "<tr><td colspan='4' style='background-color:rgb(8, 37, 58);'><strong>$currentFacility</strong></td></tr>";
                        echo "<tr>
                                <th>Room No</th>
                                <th>Building</th>
                                <th>Action</th>
                            </tr>";

                        $roomQuery = "SELECT * FROM facilities WHERE facility = '$currentFacility'";
                        $roomResult = $conn->query($roomQuery);

                        while ($row = $roomResult->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['roomNo']}</td>
                                    <td>{$row['building']}</td>
                                    <td>
                                        <form method='POST' onsubmit=\"return confirm('Are you sure you want to delete this facility?');\">
                                            <input type='hidden' name='delete_roomNo' value='{$row['roomNo']}'>
                                            <button type='submit' class='btn btn-danger btn-sm'>
                                                <i class='bi bi-trash'></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>";
                        }
                    }
                } else {
                    echo "<tr><td colspan='4'>No facilities available.</td></tr>";
                }
                ?>
            </table>

        </div>
    </div>

    <div class="modal" id="form-modal">
        <div class="modal-content">
            <form method="POST">
                <h2>Add Facility <span class="close-btn">&times;</span></h2>

                <label for="facility">Facility</label>
                <input type="text" name="fac" id="facility" placeholder="Enter a Facility" required>

                <label for="building">Building</label>
                <select name="building" id="building" required>
                    <option value="" disabled selected>-Select a Building-</option>
                    <option value="Old Building">Old Building</option>
                    <option value="Law Building">Law Building</option>
                    <option value="Science-Technology Building">Science Technology Building</option>
                </select>

                <label for="room">Room #</label>
                <input name="room" type="text" id="room" placeholder="Enter a Room number" required>

                <button class="btn1" name="submit">Submit</button>

                <?php if (!empty($successMessage)) : ?>
                    <p id="form-feedback" class="success-message"><?= $successMessage ?></p>
                <?php elseif (!empty($errorMessage)) : ?>
                    <p id="form-feedback" class="error-message"><?= $errorMessage ?></p>
                <?php elseif (!empty($error)) : ?>
                    <p id="form-feedback" class="error-message"><?= $error ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script>
        const showFormBtn = document.getElementById('show-form');
        const modal = document.getElementById('form-modal');
        const closeBtn = document.querySelector('.close-btn');

        showFormBtn.addEventListener('click', () => {
            modal.style.display = 'block';
        });

        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });

        window.addEventListener('DOMContentLoaded', () => {
            const feedback = document.getElementById('form-feedback');
            if (feedback) {
                modal.style.display = 'block';
                feedback.style.opacity = '1';
                setTimeout(() => {
                    feedback.style.opacity = '0';
                }, 3000);
            }
        });
    </script>
</body>
</html>
