<?php
session_start();

if (isset($_SESSION['patient_id']) && isset($_SESSION['username'])) {

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOME</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Hello, <?php echo $_SESSION['username']; ?> Welcome! </h1> 
    

    <p>Your records:</p>
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
           echo "<li> Record ID: " . htmlspecialchars($record['record_id']) . "-" . htmlspecialchars($record['record_details']) . "</li>";
        }
        ?>
    </ul>
    <a href="download.php">Download your Records as PDF</a><br>
    <br><a href="Logout.php">Logout</a> 
</body>
</html>

<?php 
}else{
     header("Location: index.php");
     exit();
}
 ?>