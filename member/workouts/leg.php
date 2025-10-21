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

// legs_workout.php

$workouts = [
    "Beginner" => [
        ["Bodyweight Squats", "3 sets x 12 reps", "legWorkouts/squat.jpg"],
        ["Glute Bridge", "3 sets x 12 reps", "legWorkouts/glute.jpg"],
        ["Forward Lunges (bodyweight)", "3 sets x 10 reps each leg", "legWorkouts/forwardLunges.jpg"],
        ["Standing Calf Raises", "3 sets x 15 reps", "legWorkouts/calfRaises.jpg"],
        ["Step-Ups (low step)", "3 sets x 10 reps each leg", "legWorkouts/stepup.jpg"]
    ],
    "Intermediate" => [
        ["Barbell Back Squat", "4 sets x 8–10 reps", "legWorkouts/barbellBackSquat.jpg"],
        ["Romanian Deadlift (moderate weight)", "4 sets x 10 reps", "legWorkouts/romanianDeadlift.jpg"],
        ["Walking Lunges (weighted)", "3 sets x 12 steps each leg", "legWorkouts/weightLunges.jpg"],
        ["Bulgarian Split Squat", "3 sets x 8–10 reps each leg", "legWorkouts/bulgarian.jpg"],
        ["Seated Calf Raises", "3 sets x 15–20 reps", "legWorkouts/seatedCalfRaises.jpg"]
    ],
    "Extreme" => [
        ["Front Squats (heavy)", "5 sets x 6–8 reps", "legWorkouts/frontSquat.jpg"],
        ["Deadlifts (heavy)", "5 sets x 6 reps", "legWorkouts/deadlift.jpg"],
        ["Barbell Hip Thrust", "4 sets x 10 reps", "legWorkouts/barbellHipThrust.jpg"],
        ["Jump Squats (weighted)", "3 sets x 12 reps", "legWorkouts/jumpSquat.jpg"],
        ["Walking Weighted Lunges", "4 sets x 12 steps each leg", "legWorkouts/weightLunges.jpg"]
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legs Workout Plan</title>
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
            border-radius: 12px;
            padding: 25px 35px;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
        }

        h1, h2 {
            text-align: center;
            text-shadow: 0 0 8px rgba(255, 215, 0, 0.6);
        }

        h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }

        h2 {
            margin-top: 35px;
            font-size: 1.7rem;
        }

        p {
            text-align: center;
            font-size: 1rem;
            color: #ddd;
        }

        /* Table styling */
        table {
            background-color: #111;
            border-color: #FFD700;
        }

        th {
            background-color: #FFD700 !important;
            color: #000 !important;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 0.9rem;
        }

        td {
            color: #fff !important;
            background-color: #111 !important;
            vertical-align: middle;
        }

        tbody tr:nth-child(odd) td {
            background-color: #1a1a1a !important;
        }

        tbody tr:hover td {
            background-color: rgba(255, 215, 0, 0.15) !important;
        }

        /* Images */
        td img {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid rgba(255, 215, 0, 0.4);
        }

        /* Back button */
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            background-color: #FFD700;
            color: #000;
            font-weight: bold;
            padding: 8px 22px;
            border-radius: 20px;
            text-decoration: none;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.6);
            transition: all 0.3s ease-in-out;
        }

        .back-btn:hover {
            background-color: #e6c200;
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.9);
        }
    </style>
</head>
<body>

<div class="workout-container">
    <a href="javascript:history.back()" class="back-btn">← Back</a>
    <h1>🦵 Legs Workout Plan</h1>
    <p>Strengthen and tone your lower body with these workouts based on your level.</p>

    <?php foreach ($workouts as $level => $exercises): ?>
        <h2><?= htmlspecialchars($level) ?> Level</h2>
        <table class="table table-bordered table-striped text-center">
            <thead>
                <tr>
                    <th>Image</th>
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
