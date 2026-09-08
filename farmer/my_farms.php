<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "farmer") {
    header("Location: ../auth/login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT * FROM farms
    WHERE farmer_id = ?
    ORDER BY created_at DESC
");

$stmt->bind_param("i", $farmer_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Farms</title>
    <link rel="stylesheet" href="../assets/css/my_farms.css">
</head>

<body>

<div class="container">

    <h1>🌾 My Farms</h1>

    <?php if($result->num_rows > 0){ ?>

        <div class="farm-grid">

            <?php while($farm = $result->fetch_assoc()){ ?>

                <div class="farm-card">

                    <img src="../assets/images/farms/<?php echo $farm['image']; ?>" alt="Farm Image">

                    <h2>
                        <?php echo htmlspecialchars($farm['farm_name']); ?>
                    </h2>

                    <p>
                        📍 <?php echo htmlspecialchars($farm['location']); ?>
                    </p>

                    <p>
                        🌱 Crop:
                        <?php echo htmlspecialchars($farm['crop_type']); ?>
                    </p>

                    <p>
                        🎯 Target:
                        ₦<?php echo number_format($farm['target_amount']); ?>
                    </p>

                    <p>
                        💵 Funded:
                        ₦<?php echo number_format($farm['funded_amount']); ?>
                    </p>

                    <p>
                        📈 ROI:
                        <?php echo $farm['roi']; ?>%
                    </p>

                    <p>
                        ⏳ Duration:
                        <?php echo htmlspecialchars($farm['duration']); ?>
                    </p>

                    <p>
                        Status:
                        <span class="status">
                            <?php echo ucfirst($farm['status']); ?>
                        </span>
                    </p>

                    <div class="actions">

                        <a href="edit_farm.php?id=<?php echo $farm['id']; ?>" class="edit">
                            Edit
                        </a>

                        <a href="delete_farm.php?id=<?php echo $farm['id']; ?>"
                           class="delete"
                           onclick="return confirm('Delete this farm?');">
                            Delete
                        </a>

                    </div>

                </div>

            <?php } ?>

        </div>

    <?php } else { ?>

        <p class="empty">
            You haven't added any farms yet.
        </p>

    <?php } ?>

</div>

</body>
</html>