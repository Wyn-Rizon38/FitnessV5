<?php
session_start();
include '../db/connection.php';

// Require member login
if (!isset($_SESSION['member_logged_in']) || !isset($_SESSION['member_username'])) {
    header("Location: Login/member_login.php");
    exit();
}

$username = $_SESSION['member_username'];

// Fetch member's height from database
$stmt = $conn->prepare("SELECT user_id, fullname, height FROM members WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id, $fullname, $height);
$stmt->fetch();
$stmt->close();

// Handle ESP32 POST (weight in kg)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['weight'])) {
    $weight = floatval($_POST['weight']);
    // Update member's weight in database
    $stmt = $conn->prepare("UPDATE members SET weight=? WHERE user_id=?");
    $stmt->bind_param("di", $weight, $user_id);
    $stmt->execute();
    $stmt->close();
    $message = "Weight updated successfully!";
} else {
    // Get latest weight from database
    $stmt = $conn->prepare("SELECT weight FROM members WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($weight);
    $stmt->fetch();
    $stmt->close();
}

// Calculate BMI and suggestion
$bmi = null;
$suggestion = '';
if (!empty($weight) && !empty($height) && $weight > 0 && $height > 0) {
    $bmi = round($weight / pow($height / 100, 2), 2); // height in cm to m
    if ($bmi < 18.5) {
        $suggestion = "You are underweight. Consider a balanced diet with more calories and strength training.";
    } elseif ($bmi < 24.9) {
        $suggestion = "You have a normal BMI. Maintain your healthy lifestyle!";
    } elseif ($bmi < 29.9) {
        $suggestion = "You are overweight. Consider regular exercise and a healthy diet.";
    } else {
        $suggestion = "You are in the obese range. Consult a fitness coach or healthcare provider for a personalized plan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IoT Weighscale & BMI - Fitness+</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/nav.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5" style="max-width:500px;">
    <h2 class="mb-4 text-center">IoT Weighscale & BMI</h2>
    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <div class="card mb-3">
        <div class="card-body">
            <form method="post" class="mb-3">
                <label for="weight" class="form-label">Weight (kg) from IoT Weighscale</label>
                <input type="number" step="0.01" name="weight" id="weight" class="form-control" value="<?php echo htmlspecialchars($weight ?? ''); ?>" required>
                <button type="submit" class="btn btn-primary mt-2">Update Weight</button>
            </form>
            <p><strong>Height:</strong> <?php echo htmlspecialchars($height); ?> cm</p>
            <?php if ($bmi): ?>
                <p><strong>BMI:</strong> <?php echo $bmi; ?></p>
                <div class="alert alert-info"><?php echo $suggestion; ?></div>
            <?php endif; ?>
        </div>
    </div>
    <div class="alert alert-secondary">
        <strong>Tip:</strong> Your ESP32 can POST the weight to this page using:<br>
        <code>POST <?php echo htmlspecialchars($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']); ?> with weight=XX.XX</code>
    </div>
</div>
</body>
</html>