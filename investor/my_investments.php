<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "investor") {
    header("Location: ../auth/login.php");
    exit();
}

$investor_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT
        investments.*,
        farms.farm_name,
        farms.crop_type,
        farms.location
    FROM investments
    JOIN farms
        ON investments.farm_id = farms.id
    WHERE investments.investor_id = ?
    ORDER BY investments.invested_at DESC
");

$stmt->bind_param("i", $investor_id);
$stmt->execute();

$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Investments</title>
    <link rel="stylesheet" href="../assets/css/my_investments.css">
</head>

<body>

<div class="container">

    <h1>💰 My Investments</h1>

    <?php if($result->num_rows > 0){ ?>

        <table>

            <tr>
                <th>Farm</th>
                <th>Crop</th>
                <th>Location</th>
                <th>Amount</th>
                <th>Expected Return</th>
                <th>Status</th>
                <th>Date</th>
            </tr>

            <?php while($investment = $result->fetch_assoc()){ ?>

            <tr>

                <td>
                    <?php echo htmlspecialchars($investment['farm_name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($investment['crop_type']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($investment['location']); ?>
                </td>

                <td>
                    ₦<?php echo number_format($investment['amount']); ?>
                </td>

                <td>
                    ₦<?php echo number_format($investment['expected_return']); ?>
                </td>

                <td>
                    <?php echo ucfirst($investment['status']); ?>
                </td>

                <td>
                    <?php echo $investment['invested_at']; ?>
                </td>

            </tr>

            <?php } ?>

        </table>

    <?php } else { ?>

        <p class="empty">
            You haven't made any investments yet.
        </p>

    <?php } ?>

</div>

</body>
</html>