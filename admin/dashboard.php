<?php
include 'url_restrictrion.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness+ Gym Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
      th { text-transform: none !important; }
    </style>

</head>

<body>


    <!-- Main Content -->
    <div id="content-wrapper">
        <!-- nav bar -->
        <?php include 'navbar.php'; ?>

        <div class="container-fluid mt-4">
            <h2>Member Attendance</h2>

            <!-- attendance page content here -->
            <?php
            // Count members who are currently inside (timed in but not yet timed out)
            $insideCount = 0;
            $insideQry = "
        SELECT COUNT(*) as inside_total
        FROM (
            SELECT a.member_id
            FROM attendance a
            INNER JOIN (
                SELECT member_id, MAX(id) as max_id
                FROM attendance
                GROUP BY member_id
            ) b ON a.member_id = b.member_id AND a.id = b.max_id
            WHERE a.time_out IS NULL
        ) as inside_members
    ";
            $insideResult = mysqli_query($connection, $insideQry);
            if ($row = mysqli_fetch_assoc($insideResult)) {
                $insideCount = (int) $row['inside_total'];
            }
            ?>

            <span class="badge bg-info mb-3">Currently Inside: <?php echo $insideCount; ?></span>

            <!-- Attendance Form -->
            <form method="post" class="mb-4">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label for="member_id" class="form-label">Member ID</label>
                        <input type="number" name="member_id" id="member_id" class="form-control" required>
                    </div>
                    <div class="col-md-4 d-flex flex-wrap gap-2">
                        <button type="submit" name="time_in" class="btn btn-success">Time In</button>
                        <button type="submit" name="time_out" class="btn btn-danger">Time Out</button>
                    </div>
                </div>
            </form>

            <?php
            // Handle delete attendance
            if (isset($_GET['delete_attendance'])) {
                $delete_id = intval($_GET['delete_attendance']);
                $delStmt = $connection->prepare("DELETE FROM attendance WHERE id = ?");
                $delStmt->bind_param("i", $delete_id);
                if ($delStmt->execute()) {
                    echo '<div class="alert alert-success">Attendance record deleted.</div>';
                } else {
                    echo '<div class="alert alert-danger">Failed to delete attendance record.</div>';
                }
                $delStmt->close();
            }

            // Handle attendance
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_id'])) {
                $member_id = intval($_POST['member_id']);
                $now = date('Y-m-d H:i:s');

                // Check if member exists
                $check = $connection->prepare("SELECT user_id FROM members WHERE user_id = ?");
                $check->bind_param("i", $member_id);
                $check->execute();
                $check->store_result();
                if ($check->num_rows == 0) {
                    echo '<div class="alert alert-danger">Member ID does not exist.</div>';
                } else {
                    if (isset($_POST['time_in'])) {
                        // Prevent duplicate time in
                        $open = $connection->prepare("SELECT id FROM attendance WHERE member_id = ? AND time_out IS NULL");
                        $open->bind_param("i", $member_id);
                        $open->execute();
                        $open->store_result();
                        if ($open->num_rows > 0) {
                            echo '<div class="alert alert-warning">This member is already timed in and has not timed out yet.</div>';
                        } else {
                            $stmt = $connection->prepare("INSERT INTO attendance (member_id, time_in) VALUES (?, ?)");
                            $stmt->bind_param("is", $member_id, $now);
                            if ($stmt->execute()) {
                                echo '<div class="alert alert-success">Time In recorded for Member ID ' . $member_id . ' at ' . $now . '</div>';
                            } else {
                                echo '<div class="alert alert-danger">Error recording Time In.</div>';
                            }
                        }
                        $open->close();
                    } elseif (isset($_POST['time_out'])) {
                        $stmt = $connection->prepare("UPDATE attendance SET time_out=? WHERE member_id=? AND time_out IS NULL ORDER BY id DESC LIMIT 1");
                        $stmt->bind_param("si", $now, $member_id);
                        $stmt->execute();
                        if ($stmt->affected_rows > 0) {
                            echo '<div class="alert alert-info">Time Out recorded for Member ID ' . $member_id . ' at ' . $now . '</div>';
                        } else {
                            echo '<div class="alert alert-warning">No open Time In found for this member.</div>';
                        }
                    }
                }
                $check->close();
            }
            ?>

            <!-- Recent Attendance -->
            <h4 class="mt-5">Recent Attendance</h4>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Member ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Time In</th>
                            <th class="d-none d-sm-table-cell">Time Out</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $connection->query("SELECT a.*, m.fullname, m.dor, m.plan 
                                              FROM attendance a 
                                              LEFT JOIN members m ON a.member_id = m.user_id 
                                              ORDER BY a.id DESC LIMIT 10");
                        $today = date('Y-m-d');
                        while ($row = $result->fetch_assoc()) {
                            // Determine status
                            $status = '<span class="badge bg-warning text-dark">Pending</span>';
                            if (!empty($row['dor']) && !empty($row['plan'])) {
                                $planMonths = (int) $row['plan'];
                                if ($planMonths > 0) {
                                    $expDateObj = new DateTime($row['dor']);
                                    $expDateObj->modify("+$planMonths months");
                                    $expDate = $expDateObj->format('Y-m-d');
                                    $status = ($expDate >= $today)
                                        ? '<span class="badge bg-success">Active</span>'
                                        : '<span class="badge bg-danger">Expired</span>';
                                }
                            }

                            // Get date from time_in or time_out
                            $date = '';
                            if (!empty($row['time_in'])) {
                                $date = date('Y-m-d', strtotime($row['time_in']));
                            } elseif (!empty($row['time_out'])) {
                                $date = date('Y-m-d', strtotime($row['time_out']));
                            }

                            $deleteUrl = $_SERVER['PHP_SELF'] . '?delete_attendance=' . $row['id'];
                            echo "<tr>
                        <td>{$date}</td>
                        <td>{$row['member_id']}</td>
                        <td>{$row['fullname']}</td>
                        <td>{$status}</td>
                        <td>{$row['time_in']}</td>
                        <td class='d-none d-sm-table-cell'>{$row['time_out']}</td>
                        <td>
                            <a href=\"{$deleteUrl}\" 
                               class=\"btn btn-danger btn-sm\" 
                               onclick=\"return confirm('Are you sure you want to delete this attendance record?');\">
                               Delete
                            </a>
                        </td>
                    </tr>";
                        }
                        ?>

            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>