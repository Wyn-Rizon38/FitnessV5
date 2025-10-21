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

// abs_workout.php

$workouts = [
    "Beginner" => [
        ["Crunches", "3 sets x 15 reps", "absWorkouts/crunches.jpg"],
        ["Knee Tucks", "3 sets x 12 reps", "absWorkouts/kneeTucks.jpg"],
        ["Heel Touches", "3 sets x 15 reps each side", "absWorkouts/heelTouch.jpg"],
        ["Plank (hold)", "3 sets x 20 seconds", "absWorkouts/plank.jpg"],
        ["Mountain Climbers (slow)", "3 sets x 12 reps each leg", "absWorkouts/mountainClimbers.jpg"]
    ],
    "Intermediate" => [
        ["Bicycle Crunches", "4 sets x 20 reps", "absWorkouts/bicycleCrunches.jpg"],
        ["Hanging Knee Raises", "4 sets x 12 reps", "absWorkouts/hangingKnee.jpg"],
        ["Side Plank (hold)", "3 sets x 30 seconds each side", "absWorkouts/sidePlank.jpg"],
        ["Russian Twists (weighted)", "3 sets x 15 reps each side", "absWorkouts/russianTwist.jpg"],
        ["Mountain Climbers (fast)", "3 sets x 20 reps each leg", "absWorkouts/mountainClimbers.jpg"]
    ],
    "Extreme" => [
        ["Hanging Leg Raises (to bar)", "5 sets x 12 reps", "absWorkouts/hangingLegRaise.png"],
        ["Dragon Flags", "4 sets x 8 reps", "absWorkouts/dragonFlag.jpg"],
        ["Weighted Sit-Ups", "4 sets x 15 reps", "absWorkouts/weightSitup.jpg"],
        ["Plank with Shoulder Taps", "3 sets x 20 reps", "absWorkouts/plankTap.jpg"],
        ["V-Ups", "4 sets x 15 reps", "absWorkouts/vUps.jpg"]
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abs Workout Plan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #000000, #1c1c1c);
            color: #FFD700;
            font-family: Arial, sans-serif;
            margin: 0;
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
            text-shadow: 0 0 8px rgba(255, 215, 0, 0.5);
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

        @media (max-width: 768px) {
            h1 { font-size: 1.8rem; }
            h2 { font-size: 1.3rem; }
            .workout-container { padding: 15px; }
        }
    </style>
</head>
<body>

<div class="workout-container">
    <a href="javascript:history.back()" class="back-btn">← Back</a>
    <h1>🔥 Abs Workout Plan</h1>
    <p>Strengthen and sculpt your core with these routines.</p>

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
