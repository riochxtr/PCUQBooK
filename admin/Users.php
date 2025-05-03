<?php
include '../Connect.php';
session_start();

if (!isset($_SESSION['Uname'])) {
    header("Location: ../landing.php");
    exit();
}

if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE Id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "User deleted successfully!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$sql = "SELECT * FROM users WHERE role = 'user'";
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
        <div><h1>Registered Users</h1></div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="popup" id="successPopup">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Password</th>
                <th>Action</th>
            </tr>

            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['Id']}</td>
                            <td>{$row['UserName']}</td>
                            <td>{$row['Email']}</td>
                            <td>{$row['Password']}</td>
                            <td>
                                <form method='POST' onsubmit=\"return confirm('Are you sure you want to delete this user?');\">
                                    <input type='hidden' name='delete_id' value='{$row['Id']}'>
                                    <button type='submit' class='btn btn-danger btn-sm'>
                                        <i class='bi bi-trash'></i> Delete
                                    </button>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No users found</td></tr>";
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
