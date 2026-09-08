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

/* Get the farm */
$stmt = $conn->prepare("
    SELECT * FROM farms
    WHERE id = ? AND farmer_id = ?
");

$stmt->bind_param("ii", $farm_id, $farmer_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Farm not found.");
}

$farm = $result->fetch_assoc();

$message = "";

/* Update farm */
if (isset($_POST['update'])) {

    $farm_name = $_POST['farm_name'];
    $location = $_POST['location'];
    $crop_type = $_POST['crop_type'];
    $description = $_POST['description'];
    $target_amount = $_POST['target_amount'];
    $roi = $_POST['roi'];
    $duration = $_POST['duration'];

    $stmt = $conn->prepare("
        UPDATE farms
        SET farm_name=?,
            location=?,
            crop_type=?,
            description=?,
            target_amount=?,
            roi=?,
            duration=?
        WHERE id=? AND farmer_id=?
    ");

    $stmt->bind_param(
        "ssssdisii",
        $farm_name,
        $location,
        $crop_type,
        $description,
        $target_amount,
        $roi,
        $duration,
        $farm_id,
        $farmer_id
    );

    if ($stmt->execute()) {
        $message = "Farm updated successfully.";

        header("Refresh:2; url=my_farms.php");
    } else {
        $message = "Error updating farm.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Farm</title>
    <link rel="stylesheet" href="../assets/css/add_farm.css">
</head>

<body>

<div class="container">

    <h1>Edit Farm</h1>

    <?php if($message!=""){ ?>
        <p class="message"><?php echo $message; ?></p>
    <?php } ?>

    <form method="POST">

        <input type="text"
               name="farm_name"
               value="<?php echo htmlspecialchars($farm['farm_name']); ?>"
               required>

        <input type="text"
               name="location"
               value="<?php echo htmlspecialchars($farm['location']); ?>"
               required>

        <input type="text"
               name="crop_type"
               value="<?php echo htmlspecialchars($farm['crop_type']); ?>"
               required>

        <textarea name="description" required><?php
            echo htmlspecialchars($farm['description']);
        ?></textarea>

        <input type="number"
               name="target_amount"
               value="<?php echo $farm['target_amount']; ?>"
               required>

        <input type="number"
               name="roi"
               value="<?php echo $farm['roi']; ?>"
               required>

        <input type="text"
               name="duration"
               value="<?php echo htmlspecialchars($farm['duration']); ?>"
               required>

        <button type="submit" name="update">
            Update Farm
        </button>

    </form>

</div>

</body>
</html>