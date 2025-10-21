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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="css/member_status.css">
  <style>
      th { text-transform: none !important; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div id="content">
  <div id="content-header">
    <h1 class="text-center">Member's Current Status <i class="fas fa-eye"></i></h1>
  </div>

  <div class="container-fluid mt-4">
    <?php
    $activeCount = 0;
    $expiredCount = 0;
    $today = date('Y-m-d');
    $countQry = "SELECT dor, plan FROM members";
    $countResult = mysqli_query($connection, $countQry);
    while ($row = mysqli_fetch_assoc($countResult)) {
        $planMonths = (int)$row['plan'];
        $registrationDate = $row['dor'];
        $expDate = '';
        if (!empty($registrationDate) && $planMonths > 0) {
            $expDateObj = new DateTime($registrationDate);
            $expDateObj->modify("+$planMonths months");
            $expDate = $expDateObj->format('Y-m-d');
            if ($expDate >= $today) $activeCount++; else $expiredCount++;
        } else {
            $expiredCount++;
        }
    }
    ?>

    <!-- Totals -->
    <div class="mb-4 text-center">
      <span class="badge bg-success me-3">Active Members: <?php echo $activeCount; ?></span>
      <span class="badge bg-danger">Expired Members: <?php echo $expiredCount; ?></span>
    </div>

    <div class="widget-box">
      <div class="widget-title">
        <i class="fas fa-th"></i> Status Table
      </div>
      <div class="widget-content nopadding">

        <!-- Add & Search -->
        <div class="p-3 d-flex justify-content-between align-items-center">
          <a href="action/addMember.php" class="btn btn-success"><i class="fas fa-user-plus"></i> Add Member</a>
          <input type="text" id="memberSearch" class="form-control w-50" placeholder="Search members...">
        </div>

        <?php
        $qry = "SELECT * FROM members";
        $result = mysqli_query($connection, $qry);

        $activeRows = $pendingRows = $expiredRows = [];
        $today = date('Y-m-d');

        while ($row = mysqli_fetch_assoc($result)) {
          $planMonths = (int)$row['plan'];
          $registrationDate = $row['dor'];

          $expDate = '';
          if (!empty($registrationDate) && $planMonths > 0) {
              $expDateObj = new DateTime($registrationDate);
              $expDateObj->modify("+$planMonths months");
              $expDate = $expDateObj->format('Y-m-d');
          }

          if (empty($registrationDate) || $planMonths <= 0) {
              $pendingRows[] = ['row'=>$row,'registrationDate'=>$registrationDate,'expDate'=>$expDate,'statusClass'=>'status-pending','statusIcon'=>'<i class="fas fa-circle"></i>','statusText'=>'Pending Reg'];
          } elseif ($expDate >= $today) {
              $activeRows[] = ['row'=>$row,'registrationDate'=>$registrationDate,'expDate'=>$expDate,'statusClass'=>'status-active','statusIcon'=>'<i class="fas fa-circle"></i>','statusText'=>'Active'];
          } else {
              $expiredRows[] = ['row'=>$row,'registrationDate'=>$registrationDate,'expDate'=>$expDate,'statusClass'=>'status-expired','statusIcon'=>'<i class="fas fa-circle"></i>','statusText'=>'Expired'];
          }
        }

        echo "<table class='table table-bordered table-hover mt-3' id='membersTable'>
          <thead>
            <tr>
              <th>User ID</th>
              <th>Fullname</th>
              <th>Contact Number</th>
              <th>Registration Date</th>
              <th>Expiration Date</th>
              <th>Plan</th>
              <th>Membership Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>";

        foreach ([$activeRows, $pendingRows, $expiredRows] as $group) {
          foreach ($group as $item) {
              $row = $item['row'];
              $editUrl = "action/editMember.php?id=" . $row['user_id'];
              $deleteUrl = "action/deleteMember.php?id=" . $row['user_id'];
              $paymentUrl = "action/paymentMember.php?user_id=" . $row['user_id'];

              echo "<tr>
                  <td>{$row['user_id']}</td>
                  <td>{$row['fullname']}</td>
                  <td>{$row['contact']}</td>
                  <td>{$item['registrationDate']}</td>
                  <td>{$item['expDate']}</td>
                  <td>{$row['plan']} Month/s</td>
                  <td class='{$item['statusClass']}'>{$item['statusIcon']} {$item['statusText']}</td>
                  <td>
                      <a class='btn btn-primary btn-sm' href='{$paymentUrl}'><i class='fas fa-money-bill-wave'></i> Payment</a>
                      <a class='btn btn-warning btn-sm' href='{$editUrl}'><i class='fas fa-edit'></i> Edit</a>
                      <a class='btn btn-danger btn-sm' href='{$deleteUrl}' onclick=\"return confirm('Are you sure you want to delete this member?');\"><i class='fas fa-trash'></i> Delete</a>
                  </td>
                </tr>";
          }
        }
        echo "</tbody></table>";
        ?>

      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // ✅ Live Search Function
  document.getElementById('memberSearch').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#membersTable tbody tr');

    rows.forEach(row => {
      let text = row.textContent.toLowerCase();
      row.style.display = text.includes(filter) ? '' : 'none';
    });
  });

  // ✅ Sort date columns
  document.addEventListener("DOMContentLoaded", function () {
  const table = document.querySelector("table");
  if (!table) return;

  const tbody = table.querySelector("tbody");
  const rows = Array.from(tbody.querySelectorAll("tr"));

  // Find the column index for "Paid Date"
  const headers = Array.from(table.querySelectorAll("th"));
  const paidDateIndex = headers.findIndex(th => th.textContent.trim().toLowerCase().includes("paid date"));
  if (paidDateIndex === -1) return;

  // Sort rows by date (newest first)
  rows.sort((a, b) => {
    const dateA = new Date(a.children[paidDateIndex].textContent.trim());
    const dateB = new Date(b.children[paidDateIndex].textContent.trim());
    return dateB - dateA; // Descending
  });

  // Re-append sorted rows
  rows.forEach(row => tbody.appendChild(row));
});
</script>

</body>
</html>
