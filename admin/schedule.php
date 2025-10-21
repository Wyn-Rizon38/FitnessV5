<?php
include 'url_restrictrion.php';
include '../db/connection.php'; // Make sure your DB connection is included

// ✅ Handle delete action safely
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);

    if ($delete_id > 0) {
        // Delete related class_members first (if foreign key constraint exists)
        $conn->query("DELETE FROM class_members WHERE class_id = $delete_id");

        // Then delete the class itself
        $stmt = $conn->prepare("DELETE FROM class_schedule WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();

        header("Location: schedule.php?msg=deleted"); // <-- FIXED: redirect to schedule.php
        exit;
    }
}

// ✅ Handle add/edit actions
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = trim($_POST['class_name']);
    $coach_id = intval($_POST['coach_id']);
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $location = trim($_POST['location']);

    if (!$class_name || !$coach_id || !$day_of_week || !$start_time || !$end_time) {
        $error = "All fields except location are required.";
    } else {
        if (!empty($_POST['edit_id'])) {
            $edit_id = intval($_POST['edit_id']);
            $stmt = $conn->prepare("UPDATE class_schedule 
                SET class_name=?, coach_id=?, day_of_week=?, start_time=?, end_time=?, location=? 
                WHERE id=?");
            $stmt->bind_param("sissssi", $class_name, $coach_id, $day_of_week, $start_time, $end_time, $location, $edit_id);
            $stmt->execute();
            $stmt->close();
            header("Location: schedule.php?msg=updated"); // <-- FIXED: redirect to schedule.php
            exit;
        } else {
            $stmt = $conn->prepare("INSERT INTO class_schedule 
                (class_name, coach_id, day_of_week, start_time, end_time, location) 
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sissss", $class_name, $coach_id, $day_of_week, $start_time, $end_time, $location);
            $stmt->execute();
            $stmt->close();
            header("Location: schedule.php?msg=added"); // <-- FIXED: redirect to schedule.php
            exit;
        }
    }
}

// ✅ Fetch data
$coaches = $conn->query("SELECT * FROM coach ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$schedules = $conn->query("
    SELECT cs.*, c.name as coach_name 
    FROM class_schedule cs 
    JOIN coach c ON cs.coach_id = c.id 
    ORDER BY FIELD(cs.day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), cs.start_time
")->fetch_all(MYSQLI_ASSOC);

$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Scheduling</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/schedules.css">
    <style>
      th { text-transform: none !important; }
    </style>
    
</head>
<body>

<?php include 'navbar.php'; ?>

<div id="content-wrapper">
    <div class="container mt-4">
        <h2 class="mb-4">Class Scheduling</h2>

        <!-- Alert Messages -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?php
                switch ($_GET['msg']) {
                    case 'added': echo "✅ Class scheduled successfully!"; break;
                    case 'updated': echo "✅ Class updated successfully!"; break;
                    case 'deleted': echo "🗑️ Class deleted successfully!"; break;
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <form method="post" class="row g-3 mb-4">
            <div class="col-md-3 col-12">
                <label class="form-label">Class Name</label>
                <input type="text" name="class_name" class="form-control" required>
            </div>
            <div class="col-md-3 col-12">
                <label class="form-label">Coach</label>
                <select name="coach_id" class="form-select" required>
                    <option value="">Select Coach</option>
                    <?php foreach ($coaches as $coach): ?>
                        <option value="<?= $coach['id'] ?>"><?= htmlspecialchars($coach['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label">Day</label>
                <select name="day_of_week" class="form-select" required>
                    <option value="">Select Day</option>
                    <?php foreach ($days as $day): ?>
                        <option value="<?= $day ?>"><?= $day ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label">Start Time</label>
                <input type="time" name="start_time" class="form-control" required>
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label">End Time</label>
                <input type="time" name="end_time" class="form-control" required>
            </div>
            <div class="col-md-3 col-12">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-warning text-dark fw-bold">Add Class</button>
            </div>
        </form>

        <!-- Class Table -->
        <h3>Scheduled Classes</h3>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Class Name</th>
                        <th>Coach</th>
                        <th>Day</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Location</th>
                        <th style="min-width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schedules)): ?>
                        <tr><td colspan="7" class="text-center text-muted">No classes scheduled.</td></tr>
                    <?php else: ?>
                        <?php foreach ($schedules as $sched): ?>
                            <tr>
                                <td><?= htmlspecialchars($sched['class_name']) ?></td>
                                <td><?= htmlspecialchars($sched['coach_name']) ?></td>
                                <td><?= htmlspecialchars($sched['day_of_week']) ?></td>
                                <td><?= htmlspecialchars(date("g:i A", strtotime($sched['start_time']))) ?></td>
                                <td><?= htmlspecialchars(date("g:i A", strtotime($sched['end_time']))) ?></td>
                                <td><?= htmlspecialchars($sched['location']) ?></td>
                                <td class="text-center">
                                    <a href="schedule.php?delete=<?= $sched['id'] ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this class? This will also remove all enrolled members.');">
                                       Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>