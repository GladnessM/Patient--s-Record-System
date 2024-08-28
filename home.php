<?php
session_start();

if (isset($_SESSION['patient_id']) && isset($_SESSION['username'])) {
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <a href="home.php" class="logo-link">
            <img src="/resources/logo-removebg-preview.png" alt="Logo">
            </a>
        </div>
        <div class="nav">
            <a href="appointments.php">Appointments</a>
            <a href="settings.php">Settings</a>
            <a href="Logout.php" class="logout-link">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="profile">
                <span><?php echo $_SESSION['username']; ?></span>
                <img src="/resources/profile.jpg" alt="Profile">
            </div>
        </div>

        <div class="current-appointment">
            <h2>Current Appointment</h2>
            <div class="appointment-info">
                <!-- <div class="patient-photo">
                    <img src="profile.jpg" alt="Patient Photo">
                    <h3><?php echo $_SESSION['username']; ?></h3>
                </div> -->
                <div class="patient-details">
                    <p><strong>Date:</strong> 12th February 2024</p>
                    <p><strong>Time:</strong> 10:00 AM</p>
                    <p><strong>Doctor:</strong> Dr. Alex Hess</p>
                </div>
            </div>
        </div>

        <div class="records-section">
            <h4>Your Records</h4>
            <ul>
                <?php
                require_once 'db_connect.php';
                $patient_id = $_SESSION['patient_id'];

                $sql = "SELECT * FROM patient_records WHERE patient_id=?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $patient_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($record = $result->fetch_assoc()) {
                    echo "<li class='record-item'>Record ID: " . htmlspecialchars($record['record_id']) . " - " . htmlspecialchars($record['record_details']) . "</li>";
                }
                ?>
            </ul>
            <a href="download.php" class="download-link">Download your Records as PDF</a>
        </div>

    </div>

</body>
</html>

<?php 
}else{
     header("Location: index.php");
     exit();
}
 ?>