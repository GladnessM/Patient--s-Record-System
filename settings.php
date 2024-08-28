<?php
session_start();

if (isset($_SESSION['patient_id']) && isset($_SESSION['username'])) {

    require_once 'db_connect.php';
    $patient_id = $_SESSION['patient_id'];

    // Handle updating appearance
    if (isset($_POST['update_appearance'])) {
        $background_color = $_POST['background_color'];
        
        $sql = "UPDATE user_settings SET background_color=? WHERE patient_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $background_color, $patient_id);
        $stmt->execute();
    }

    // Fetch current settings
    $sql = "SELECT background_color FROM user_settings WHERE patient_id=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $settings = $result->fetch_assoc();
    $current_background_color = $settings['background_color'] ?? '#ffffff'; // Default to white

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Settings</title>
        <link rel="stylesheet" type="text/css" href="styles.css">
        <style>
            /* Apply the user's selected background color to the body */
            body {
                background-color: <?php echo htmlspecialchars($current_background_color); ?>;
            }
        </style>
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

            <h2>Change Appearance</h2>
            <form method="post">
                <label>Background Color: <input type="color" name="background_color" value="<?php echo htmlspecialchars($current_background_color); ?>" required></label><br>
                <button type="submit" name="update_appearance">Update Appearance</button>
            </form>
        </div>
    </body>
    </html>

    <?php 
}else{
    header("Location: index.php");
    exit();
}
?>
