<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "farmer") {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";

if (isset($_POST['submit'])) {

    $farmer_id = $_SESSION['user_id'];
    $farm_name = $_POST['farm_name'];
    $location = $_POST['location'];
    $crop_type = $_POST['crop_type'];
    $description = $_POST['description'];
    $target_amount = $_POST['target_amount'];
    $roi = $_POST['roi'];
    $duration = $_POST['duration'];

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    $upload_folder = "../assets/images/farms/";

    if (!file_exists($upload_folder)) {
        mkdir($upload_folder, 0777, true);
    }

    move_uploaded_file($tmp, $upload_folder . $image);

    $stmt = $conn->prepare("
        INSERT INTO farms
        (farmer_id, farm_name, location, crop_type, description,
         target_amount, roi, duration, image)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issssdiss",
        $farmer_id,
        $farm_name,
        $location,
        $crop_type,
        $description,
        $target_amount,
        $roi,
        $duration,
        $image
    );

    if ($stmt->execute()) {
        $message = "Farm added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Farm</title>
    <link rel="stylesheet" href="../assets/css/add_farm.css">
</head>
<body>

<div class="container">

    <h1>Add New Farm</h1>

    <?php if($message!=""){ ?>
        <p class="message"><?php echo $message; ?></p>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">

        <input type="text" name="farm_name" placeholder="Farm Name" required>

        <input type="text" name="location" placeholder="Location" required>

        <input type="text" name="crop_type" placeholder="Crop Type" required>

        <textarea name="description" placeholder="Farm Description" required></textarea>

        <input type="number" name="target_amount" placeholder="Target Amount (₦)" required>

        <input type="number" name="roi" placeholder="ROI (%)" required>

        <input type="text" name="duration" placeholder="Duration (e.g. 6 Months)" required>

        <input type="file" name="image" required>

        <button type="submit" name="submit">
            Add Farm
        </button>

    </form>

</div>

</body>
</html>