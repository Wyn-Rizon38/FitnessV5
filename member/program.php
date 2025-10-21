<?php
session_start();
include '../db/connection.php';

// Require member login
if (!isset($_SESSION['member_logged_in']) || !isset($_SESSION['member_username'])) {
    header("Location: Login/member_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout Programs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/program.css">
</head>
<body>

<!-- Navbar -->
<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4 text-center text-gold">Workout Program by Muscle Group</h2>
    
    <div class="row g-4 justify-content-center">
        <?php
        // Match names with your actual files in workouts folder
        $muscle_groups = [
            'Chest'     => "images/chest.jpg",
            'Back'      => "images/back.jpg",
            'Arm'       => "images/arm.png",      // file is arm.php
            'Shoulder'  => "images/shoulder.jpg", // file is shoulder.php
            'Leg'       => "images/leg.png",      // file is leg.php
            'Abs'       => "images/abs.png",      // file is abs.php
        ];

        foreach ($muscle_groups as $muscle => $img): ?>
            <div class="col-6 col-md-4 d-flex">
                <!-- Link to workouts folder -->
                <a href="workouts/<?= strtolower($muscle) ?>.php" class="text-decoration-none w-100">
                    <div class="card workout-card shadow-sm w-100">
                        <img src="<?= $img ?>" class="card-img-top" alt="<?= htmlspecialchars($muscle) ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?= htmlspecialchars($muscle) ?></h5>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
