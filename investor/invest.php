<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "investor") {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: browse_farms.php");
    exit();
}

$farm_id = $_GET['id'];
$investor_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT *
    FROM farms
    WHERE id = ? AND status = 'active'
");

$stmt->bind_param("i", $farm_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Farm not found.");
}

$farm = $result->fetch_assoc();

$message = "";

if (isset($_POST['invest'])) {

    $amount = $_POST['amount'];

    if ($amount <= 0) {

        $message = "Enter a valid amount.";

    } else {

        /* Calculate expected return */
        $expected_return = $amount + (($amount * $farm['roi']) / 100);

        /* Save investment */
        $stmt = $conn->prepare("
            INSERT INTO investments
            (investor_id, farm_id, amount, expected_return)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iidd",
            $investor_id,
            $farm_id,
            $amount,
            $expected_return
        );

        if ($stmt->execute()) {

            /* Update funded amount */
            $stmt = $conn->prepare("
                UPDATE farms
                SET funded_amount = funded_amount + ?
                WHERE id = ?
            ");

            $stmt->bind_param("di", $amount, $farm_id);
            $stmt->execute();

            /* Mark farm completed if target reached */
            $stmt = $conn->prepare("
                UPDATE farms
                SET status = 'completed'
                WHERE id = ?
                AND funded_amount >= target_amount
            ");

            $stmt->bind_param("i", $farm_id);
            $stmt->execute();

            $message = "Investment successful!";

        } else {

            $message = "Investment failed.";

        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Invest</title>
    <link rel="stylesheet" href="../assets/css/invest.css">
</head>

<body>

<div class="container">

    <h1>Invest in <?php echo htmlspecialchars($farm['farm_name']); ?></h1>

    <?php if($message!=""){ ?>
        <p class="message"><?php echo $message; ?></p>
    <?php } ?>

    <div class="details">

        <p><strong>Location:</strong> <?php echo htmlspecialchars($farm['location']); ?></p>

        <p><strong>ROI:</strong> <?php echo $farm['roi']; ?>%</p>

        <p><strong>Target:</strong> ₦<?php echo number_format($farm['target_amount']); ?></p>

        <p><strong>Funded:</strong> ₦<?php echo number_format($farm['funded_amount']); ?></p>

    </div>

    <form method="POST">

        <input
            type="number"
            name="amount"
            placeholder="Enter investment amount"
            required
        >

        <button type="submit" name="invest">
            Confirm Investment
        </button>

    </form>

</div>

</body>
</html>