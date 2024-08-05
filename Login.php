<?php
session_start();

// Include the database connection file
require_once 'db_connect.php';

// Check if the user is already logged in
if (isset($_SESSION['patient_id'])) {
    header('Location: patient_dashboard.php');
    exit;
}

// Define the login form
?>
<!DOCTYPE html>
<html>
<head>
    <title>Patient Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Patient Login</h2>
        <form action="patient_dashboard.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Username" required><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Login">
        </form>
        <?php if (isset($_GET['error'])) : ?>
            <p class="error-message"><?php echo $_GET['error']; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Handle the login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //$id= $_POST['id'];
    $username = mysqli_real_escape_string($mysqli, $_POST['username']);
    $password = $_POST['password'];
   

    // Query the database to retrieve the stored hash for the given username
    $query = "SELECT password, patient_id FROM patients WHERE username = '$username'";
    $result = mysqli_query($mysqli, $query);

    if (mysqli_num_rows($result) == 1) {
        $user_data = mysqli_fetch_assoc($result);
        $stored_hash = $user_data['password'];
        $patient_id = $user_data['id'];

        // Hash the entered password and compare it with the stored hash
        if (password_verify($password, $stored_hash)) {
            // Login successful, set the session variable and redirect to the dashboard
            $_SESSION['patient_id'] = $patient_id;
            header('Location: patient_dashboard.php');
            exit;
        } else {
            // Login failed, display an error message
            header('Location: login.php?error=Invalid username or password');
            exit;
        }
    } else {
        // User not found, display an error message
        header('Location: login.php?error=Invalid username or password');
        exit;
    }
}
?>
