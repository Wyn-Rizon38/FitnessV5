<?php
session_start();
include '../db/connection.php';

// Initialize defaults
$member_name = 'Member';
$member_id = null;
$schedules = [];
$joined = [];
$status = '';
$dor = '';
$plan = '';

// Check if user is logged in
if (!isset($_SESSION['member_logged_in']) || !isset($_SESSION['member_username'])) {
    header("Location: Login/member_login.php");
    exit();
}

$username = $_SESSION['member_username'];

// Fetch member details using username
$stmt = $conn->prepare("SELECT user_id, fullname FROM members WHERE username = ?");
if ($stmt) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($member_id, $fullname);
    if ($stmt->fetch()) {
        $member_name = $fullname ?: $member_name;
    }
    $stmt->close();
}

// If we didn't get a member_id, redirect to login
if (empty($member_id)) {
    header("Location: Login/member_login.php");
    exit();
}

// Fetch member DOR and plan for expiration calculation
$exp_stmt = $conn->prepare("SELECT dor, plan FROM members WHERE user_id = ?");
if ($exp_stmt) {
    $exp_stmt->bind_param("i", $member_id);
    $exp_stmt->execute();
    $exp_stmt->bind_result($dor, $plan);
    if ($exp_stmt->fetch()) {
        $today = date('Y-m-d');
        $expiration_date = '';
        if (!empty($dor) && !empty($plan) && $plan > 0) {
            try {
                $expDateObj = new DateTime($dor);
                $expDateObj->modify("+{$plan} months");
                $expiration_date = $expDateObj->format('Y-m-d');
            } catch (Exception $e) {
                $expiration_date = '';
            }
        }
        $status = (!empty($expiration_date) && $expiration_date >= $today) ? 'Active' : 'Expired';
    }
    $exp_stmt->close();
}

// Fetch all class schedules
$schedules = [];
$result = $conn->query("
    SELECT cs.*, c.name as coach_name
    FROM class_schedule cs
    JOIN coach c ON cs.coach_id = c.id
    ORDER BY FIELD(cs.day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), cs.start_time
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }
    $result->free();
}

// Fetch classes joined by this member
$joined = [];
if ($stmtJ = $conn->prepare("SELECT class_id FROM class_members WHERE member_id = ?")) {
    $stmtJ->bind_param("i", $member_id);
    $stmtJ->execute();
    $resJ = $stmtJ->get_result();
    if ($resJ) {
        while ($r = $resJ->fetch_assoc()) {
            $joined[] = (int) $r['class_id'];
        }
        $resJ->free();
    }
    $stmtJ->close();
}

// Handle join/unjoin actions
function join_class($conn, $class_id, $member_id)
{
    $stmt = $conn->prepare("INSERT IGNORE INTO class_members (class_id, member_id) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ii", $class_id, $member_id);
        $stmt->execute();
        $stmt->close();
    }
}
function unjoin_class($conn, $class_id, $member_id)
{
    $stmt = $conn->prepare("DELETE FROM class_members WHERE class_id=? AND member_id=?");
    if ($stmt) {
        $stmt->bind_param("ii", $class_id, $member_id);
        $stmt->execute();
        $stmt->close();
    }
}

if ($member_id) {
    $joinId = filter_input(INPUT_GET, 'join', FILTER_VALIDATE_INT);
    $unjoinId = filter_input(INPUT_GET, 'unjoin', FILTER_VALIDATE_INT);

    if ($joinId) {
        join_class($conn, $joinId, $member_id);
        header("Location: schedule.php");
        exit;
    }
    if ($unjoinId) {
        unjoin_class($conn, $unjoinId, $member_id);
        header("Location: schedule.php");
        exit;
    }
}

// --- Auto-unjoin if membership expired or inside the class time ---
date_default_timezone_set('Asia/Manila');
$now = date('H:i:s');
$today_day = date('l');

if (!empty($schedules) && !empty($joined)) {
    foreach ($schedules as $sched) {
        $schedId = isset($sched['id']) ? (int) $sched['id'] : null;
        if ($schedId && in_array($schedId, $joined, true)) {
            // Auto-unjoin if membership expired
            if ($status === 'Expired') {
                unjoin_class($conn, $schedId, $member_id);
                $joined = array_values(array_diff($joined, [$schedId]));
                continue;
            }
            // Auto-unjoin if today and now is within class time
            if (
                isset($sched['day_of_week'], $sched['start_time'], $sched['end_time'])
                && strtolower($sched['day_of_week']) === strtolower($today_day)
                && $now >= $sched['start_time'] && $now <= $sched['end_time']
            ) {
                unjoin_class($conn, $schedId, $member_id);
                $joined = array_values(array_diff($joined, [$schedId]));
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Class Schedule</title>
    <!-- Bootstrap + icons + page CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/schedule2.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5 schedule-container">
        <div class="schedule-wrapper p-4 shadow-lg">
            <h2 class="mb-4 text-gold text-center">Class Schedule</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-dark table-striped text-center align-middle">
                    <thead class="table-gold">
                        <tr>
                            <th>Class</th>
                            <th>Coach</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($schedules)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No classes scheduled.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($schedules as $sched):
                                $schedId = isset($sched['id']) ? (int) $sched['id'] : null;
                                $start = !empty($sched['start_time']) ? date("g:i A", strtotime($sched['start_time'])) : '';
                                $end = !empty($sched['end_time']) ? date("g:i A", strtotime($sched['end_time'])) : '';
                                $coachFirst = '';
                                if (!empty($sched['coach_name'])) {
                                    $coachFirst = explode(' ', trim($sched['coach_name']))[0];
                                }
                                $day3 = !empty($sched['day_of_week']) ? substr($sched['day_of_week'], 0, 3) : '';
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($sched['class_name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($coachFirst) ?></td>
                                    <td><?= htmlspecialchars($day3) ?></td>
                                    <td><?= htmlspecialchars(trim("$start - $end")) ?></td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1 justify-content-center">
                                            <?php if ($schedId && in_array($schedId, $joined, true)): ?>
                                                <a href="schedule.php?unjoin=<?= $schedId ?>"
                                                    class="btn btn-danger btn-sm">Unjoin</a>
                                            <?php else: ?>
                                                <?php if ($status === 'Expired'): ?>
                                                    <button class="btn btn-secondary btn-sm" disabled
                                                        title="Membership expired. Renew to join classes.">Join</button>
                                                <?php else: ?>
                                                    <?php if ($schedId): ?>
                                                        <a href="schedule.php?join=<?= $schedId ?>" class="btn btn-success btn-sm">Join</a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <button class="btn btn-info btn-sm" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#members-<?= $schedId ?>">
                                                Show Members
                                            </button>
                                        </div>
                                        <!-- Collapsible member list -->
                                        <div class="collapse mt-2" id="members-<?= $schedId ?>">
                                            <div class="card card-body p-2">
                                                <?php
                                                $membersQ = $conn->prepare("SELECT m.fullname FROM class_members cm JOIN members m ON cm.member_id = m.user_id WHERE cm.class_id = ?");
                                                $membersQ->bind_param("i", $schedId);
                                                $membersQ->execute();
                                                $membersR = $membersQ->get_result();
                                                if ($membersR->num_rows > 0) {
                                                    echo "<ul class='list-unstyled mb-0'>";
                                                    while ($mem = $membersR->fetch_assoc()) {
                                                        echo "<li><i class='bi bi-person-fill'></i> " . htmlspecialchars($mem['fullname']) . "</li>";
                                                    }
                                                    echo "</ul>";
                                                } else {
                                                    echo "<span class='text-muted'>No members joined.</span>";
                                                }
                                                $membersQ->close();
                                                ?>
                                            </div>
                                        </div>
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