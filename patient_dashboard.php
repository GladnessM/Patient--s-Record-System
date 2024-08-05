<?php
// Include the database connection file
require_once 'db_connect.php';

// Check if the patient is logged in
if (!isset($_SESSION['patient_id'])) {
    header('Location:login.php');
    exit;
}

// Get the patient's ID and data from the database
$patient_id = $_SESSION['patient_id'];
$query = "SELECT * FROM patients WHERE id = '$patient_id'";
$result = mysqli_query($mysqli, $query);
$patient_data = mysqli_fetch_assoc($result);

// Get the patient's records from the database
$query = "SELECT * FROM patient_records WHERE patient_id = '$patient_id'";
$result = mysqli_query($mysqli, $query);
$patient_records = array();
while ($row = mysqli_fetch_assoc($result)) {
    $patient_records[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome, <?php echo $patient_data['name'];?></h2>
        <div class="records-container">
            <h3>My Records</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Record Type</th>
                        <th>Description</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patient_records as $record) :?>
                        <tr>
                            <td><?php echo $record['date'];?></td>
                            <td><?php echo $record['record_type'];?></td>
                            <td><?php echo $record['description'];?></td>
                            <td><a href="download.php?id=<?php echo $record['id'];?>">Download PDF</a></td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
        <div class="personalization-container">
            <h3>Personalize My Page</h3>
            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                <label for="background_color">Background Color:</label>
                <input type="color" id="background_color" name="background_color" value="<?php echo $patient_data['background_color'];?>">
                <br>
                <label for="font_size">Font Size:</label>
                <input type="number" id="font_size" name="font_size" value="<?php echo $patient_data['font_size'];?>">
                <br>
                <input type="submit" value="Save Changes">
            </form>
        </div>
    </div>
</body>
</html>

<?php
// Handle the personalization form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $background_color = $_POST['background_color'];
    $font_size = $_POST['font_size'];

    // Update the patient's data in the database
    $query = "UPDATE patients SET background_color = '$background_color', font_size = '$font_size' WHERE id = '$patient_id'";
    mysqli_query($mysqli, $query);

    // Refresh the page to show the changes
    header('Location: patient_dashboard.php');
    exit;
}
?>