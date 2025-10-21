<?php
session_start();
include '../../db/connection.php';

// Check if user is logged in
if (!isset($_SESSION['member_logged_in']) || !isset($_SESSION['member_username'])) {
    header("Location: ../Login/member_login.php");
    exit();
}

// Get username from session
$username = $_SESSION['member_username'];
$member_name = 'Member';
$plan = '';
$dor = '';
$expiration_date = '';
$status = '';

// Fetch member details using username
$stmt = $conn->prepare("SELECT user_id, fullname, dor, plan FROM members WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($member_id, $fullname, $dor, $plan);
if ($stmt->fetch()) {
    $member_name = $fullname;
    // Calculate expiration date from dor and plan
    $today = date('Y-m-d');
    $expiration_date = '';
    if (!empty($dor) && !empty($plan) && $plan > 0) {
        $expDateObj = new DateTime($dor);
        $expDateObj->modify("+{$plan} months");
        $expiration_date = $expDateObj->format('Y-m-d');
    }
    $status = (!empty($expiration_date) && $expiration_date >= $today) ? 'Active' : 'Expired';
}
$stmt->close();

// chest_workout.php

$workouts = [
    "Beginner" => [
        ["Push-ups", "3 sets x 10 reps", "chestWorkouts/pushup.png"],
        ["Incline Push-ups (hands on bench)", "3 sets x 12 reps", "chestWorkouts/Incline_pushup.png"],
        ["Chest Dips (on parallel bars or sturdy chairs)", "2 sets x 8 reps", "chestWorkouts/chestDips.png"],
        ["Flat Dumbbell Press (light weight)", "3 sets x 10 reps", "chestWorkouts/flatDumbellPress.png"],
        ["Dumbbell Pullover", "2 sets x 12 reps", "chestWorkouts/pullover.jpg"]
    ],
    "Intermediate" => [
        ["Barbell Bench Press", "4 sets x 10 reps", "chestWorkouts/benchPress.png"],
        ["Incline Dumbbell Press", "4 sets x 10 reps", "chestWorkouts/inclineBenchPress.jpg"],
        ["Chest Dips (weighted if possible)", "3 sets x 12 reps", "chestWorkouts/weightDips.jpg"],
        ["Dumbbell Flyes (flat bench)", "3 sets x 12 reps", "chestWorkouts/dumbbell-fly.jpg"],
        ["Push-ups (slow tempo)", "3 sets to failure", "chestWorkouts/pushup.png"]
    ],
    "Extreme" => [
        ["Barbell Bench Press (heavier load)", "5 sets x 6 to 8 reps", "chestWorkouts/benchPress.png"],
        ["Incline Barbell Press", "4 sets x 8 reps", "chestWorkouts/inclineBenchPress.jpg"],
        ["Weighted Chest Dips", "4 sets x 10 reps", "chestWorkouts/weightDips.jpg"],
        ["Cable Crossovers (high to low)", "4 sets x 12 reps", "chestWorkouts/cableCrossover.jpg"],
        ["Push-ups Drop Set (normal → wide → close grip)", "3 rounds to failure", "chestWorkouts/pushup.png"]
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chest Workout Plan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #000000, #1c1c1c);
            color: #FFD700;
            font-family: Arial, sans-serif;
        }
        .workout-container {
            max-width: 1000px;
            margin: 40px auto;
            background: rgba(0, 0, 0, 0.85);
            border: 1px solid #FFD700;
            border-radius: 10px;
            padding: 20px 30px;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.3);
        }
        h1, h2 {
            text-align: center;
            text-shadow: 0 0 8px rgba(255, 215, 0, 0.5);
        }

        /* Table */
        table {
            background-color: #111 !important;
            border-color: #FFD700 !important;
        }
        th {
            background-color: #FFD700 !important;
            color: #000 !important;
            text-transform: uppercase;
            font-weight: bold;
        }
        td {
            color: #fff !important;
            background-color: #111 !important;
            vertical-align: middle;
        }
        td img {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid rgba(255, 215, 0, 0.5);
        }
        tbody tr:nth-child(odd) td {
            background-color: #1a1a1a !important;
        }
        tbody tr:hover td {
            background-color: rgba(255, 215, 0, 0.15) !important;
        }

        /* Back button */
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            background-color: #FFD700;
            color: #000;
            font-weight: bold;
            padding: 8px 20px;
            border: none;
            border-radius: 20px;
            text-decoration: none;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.6);
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            background-color: #e6c200;
            text-decoration: none;
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.9);
            color: #000;
        }
    </style>
</head>
<body>

<div class="workout-container">
    <a href="javascript:history.back()" class="back-btn">← Back</a>

    <h1>🏋️ Chest Workout Plan</h1>
    <p class="text-center">Select the level that matches your experience.</p>

    <?php foreach ($workouts as $level => $exercises): ?>
        <h2 class="mt-4"><?= htmlspecialchars($level) ?> Level</h2>
        <table class="table table-bordered table-striped text-center">
            <thead>
                <tr>
                    <th>Workout</th>
                    <th>Exercise</th>
                    <th>Sets & Reps</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exercises as $workout): ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($workout[2]) ?>" alt="<?= htmlspecialchars($workout[0]) ?>"></td>
                        <td><?= htmlspecialchars($workout[0]) ?></td>
                        <td><?= htmlspecialchars($workout[1]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</div>

</body>
</html>
