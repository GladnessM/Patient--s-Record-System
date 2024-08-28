<?php
session_start();

if (isset($_SESSION['patient_id']) && isset($_SESSION['username'])) {

    require_once 'db_connect.php';
    $patient_id = $_SESSION['patient_id'];



    // Handle booking an appointment
    if (isset($_POST['book'])) {
        $date = $_POST['date'];
        $time = $_POST['time'];
        $doctor = $_POST['doctor'];
        
        $sql = "INSERT INTO appointments (patient_id, date, time, doctor) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("isss", $patient_id, $date, $time, $doctor);
        $stmt->execute();
    }

    // Handle deleting an appointment
    if (isset($_POST['delete'])) {
        $appointment_id = $_POST['appointment_id'];
        
        $sql = "DELETE FROM appointments WHERE appointment_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $appointment_id);
        if(
        $stmt->execute()){
            echo "Appointment deleted successfully";
        } else {
            echo "Error deleting appointment: " . $stmt->error;
        }
        header("Location: appointments.php");
        exit();
    }

    $sql = "SELECT * FROM appointments WHERE patient_id=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Appointments</title>
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
    <body class="appointments-page">
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
                    <img src="/resources/profile.jpg" alt="Profile">
                    <span><?php echo $_SESSION['username']; ?></span>
                </div>
            </div>

            <h2>Book an Appointment</h2>
            <form method="post">
                <label>Date: <input type="date" name="date" required></label>
                <label>Time: <input type="time" name="time" required></label>
                <label>Doctor: <input type="text" name="doctor" required></label>
                <button type="submit" name="book">Book Appointment</button>
            </form>

            <h2>Your Appointments</h2>
            <ul class="appointment-list">
                <?php
                while ($appointment = $result->fetch_assoc()) {
    
                    echo "<li class='appointment-item'>";
                    echo "<div class='appointment-details'>";
                    echo "<p>Date: " . htmlspecialchars($appointment['date']) . "</p>";
                    echo "<p>Time: " . htmlspecialchars($appointment['time']) . "</p> ";
                    echo "<p>Doctor: " . htmlspecialchars($appointment['doctor']). "</p>";
                    echo "</div>";
                    echo "<form method='post' style='display:inline;'>";
                    echo "<input type='hidden' name='appointment_id' value='" . htmlspecialchars($appointment['patient_id']) . "'>";
                    echo "<button type='submit' name='delete' class='delete-button'>Delete</button>";
                    echo "</form>";
                    echo "</li>";
                }
                ?>
            </ul>
        </div>
    </body>
    </html>

    <?php 
}else{
    header("Location: index.php");
    exit();
}
?>
