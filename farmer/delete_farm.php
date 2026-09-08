<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "farmer") {
    header("Location: ../auth/login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header("Location: my_farms.php");
    exit();
}

$farm_id = $_GET['id'];

/* Get image before deleting */
$stmt = $conn->prepare("
    SELECT image
    FROM farms
    WHERE id = ? AND farmer_id = ?
");

$stmt->bind_param("ii", $farm_id, $farmer_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Farm not found.");
}

$farm = $result->fetch_assoc();

/* Delete image from folder */
$image_path = "../assets/images/farms/" . $farm['image'];

if (!empty($farm['image']) && file_exists($image_path)) {
    unlink($image_path);
}

/* Delete farm */
$stmt = $conn->prepare("
    DELETE FROM farms
    WHERE id = ? AND farmer_id = ?
");

$stmt->bind_param("ii", $farm_id, $farmer_id);

if ($stmt->execute()) {
    header("Location: my_farms.php");
    exit();
} else {
    echo "Error deleting farm.";
}
?>