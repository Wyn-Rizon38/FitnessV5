<?php
session_start();
include '../db/connection.php'; // Your DB connection file

// Check if user is logged in
if (!isset($_SESSION['member_username'])) {
    header("Location: login/member_login.php");
    exit();
}

// Fetch current member details
$currentUsername = $_SESSION['member_username'];
$query = "SELECT * FROM members WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $currentUsername);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();

$errorMsg = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if (empty($fullname) || empty($username)) {
        $errorMsg = "Full name and username are required.";
    } elseif (!empty($password) && $password !== $confirmPassword) {
        $errorMsg = "New password and confirm password do not match.";
    } else {
        // Update query with or without password
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $updateQuery = "UPDATE members SET fullname = ?, username = ?, password = ? WHERE user_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("sssi", $fullname, $username, $hashedPassword, $member['user_id']);
        } else {
            $updateQuery = "UPDATE members SET fullname = ?, username = ? WHERE user_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ssi", $fullname, $username, $member['user_id']);
        }

        if ($updateStmt->execute()) {
            $_SESSION['member_username'] = $username;
            header("Location: dashboard.php"); // redirect after save
            exit();
        } else {
            $errorMsg = "Error updating profile: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #000000, #1c1c1c);
            color: #FFD700;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 500px;
            margin: 60px auto;
            background: rgba(0,0,0,0.85);
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #FFD700;
            box-shadow: 0 0 15px rgba(255,215,0,0.3);
        }
        h1 {
            text-align: center;
            text-shadow: 0 0 8px rgba(255,215,0,0.5);
            margin-bottom: 20px;
        }
        .form-label {
            color: #FFD700;
            font-weight: bold;
        }
        .form-control {
            background: #111;
            color: #fff;
            border: 1px solid #FFD700;
        }
        .btn-save {
            background-color: #FFD700;
            color: #000;
            font-weight: bold;
            border: none;
            transition: 0.3s;
        }
        .btn-save:hover {
            background-color: #e6c200;
            transform: scale(1.05);
        }
        .back-btn {
            display: inline-block;
            background-color: #FFD700;
            color: #000;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            transition: 0.3s;
            margin-bottom: 15px;
        }
        .back-btn:hover {
            background-color: #e6c200;
            transform: scale(1.05);
        }
        .alert {
            background: rgba(255, 0, 0, 0.2);
            color: #ff8080;
            border: 1px solid #ff4d4d;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" class="back-btn">← Back to Home</a>
    <h1>Edit Profile</h1>

    <?php if (!empty($errorMsg)): ?>
        <div class="alert"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="fullname" class="form-label">Full Name</label>
            <input type="text" name="fullname" id="fullname" class="form-control"
                   value="<?= htmlspecialchars($member['fullname']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control"
                   value="<?= htmlspecialchars($member['username']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">New Password (optional)</label>
            <input type="password" name="password" id="password" class="form-control"
                   placeholder="Leave blank to keep current">
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                   placeholder="Re-enter new password">
        </div>

        <button type="submit" class="btn btn-save w-100">Save Changes</button>
    </form>
</div>

</body>
</html>
