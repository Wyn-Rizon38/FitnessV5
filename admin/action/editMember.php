<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../Login/admin_login.php");
    exit;
}
include '../config.php'; // adjust path as needed

// Get member ID from URL
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Fetch member data
    $sql = "SELECT * FROM members WHERE user_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();

    if (!$member) { 
        echo "Member not found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}

$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $contact = $_POST['contact'];
    $dor = $_POST['dor'];
    $plan = intval($_POST['plan']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $fingerprint_template = !empty($_POST['fingerprint_template']) ? base64_decode($_POST['fingerprint_template']) : null;

    // Compute new expiration date
    $expDateObj = new DateTime($dor);
    $expDateObj->modify("+$plan months");
    $expDate = $expDateObj->format('Y-m-d');

    // Password confirmation check
    if (!empty($password) && $password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE members SET fullname=?, contact=?, dor=?, plan=?, expiration_date=?, password=?, fingerprint_template=? WHERE user_id=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sssisssi", $fullname, $contact, $dor, $plan, $expDate, $hashedPassword, $fingerprint_template, $user_id);
        } else {
            $sql = "UPDATE members SET fullname=?, contact=?, dor=?, plan=?, expiration_date=?, fingerprint_template=? WHERE user_id=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sssissi", $fullname, $contact, $dor, $plan, $expDate, $fingerprint_template, $user_id);
        }

        if (empty($error) && $stmt->execute()) {
            header("Location: ../members.php?msg=updated");
            exit;
        } elseif (empty($error)) {
            $error = "Error updating member.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    async function enrollFingerprint() {
        // Open the fingerprint.html window for scanning
        window.open('../action/fingerPrint.php', 'FingerprintEnroll', 'width=500,height=400');
        // After scanning, instruct user to click "Get Fingerprint" to fetch from clipboard
    }

    async function getFingerprintFromClipboard() {
        try {
            const text = await navigator.clipboard.readText();
            const match = text.match(/^FP:(\d{8}T\d{6}):(.*)$/);
            if (match) {
                // Optionally, check timestamp is within last 2 minutes
                const ts = match[1];
                const now = new Date();
                const year = parseInt(ts.substr(0,4));
                const month = parseInt(ts.substr(4,2))-1;
                const day = parseInt(ts.substr(6,2));
                const hour = parseInt(ts.substr(9,2));
                const min = parseInt(ts.substr(11,2));
                const sec = parseInt(ts.substr(13,2));
                const fpDate = new Date(Date.UTC(year, month, day, hour, min, sec));
                const diff = (now.getTime() - fpDate.getTime())/1000;
                if (diff > 120 || diff < -10) {
                    document.getElementById('fp-status').innerText = "Fingerprint data is too old. Please scan again.";
                    document.getElementById('fp-status').className = "text-danger ms-2";
                    return;
                }
                document.getElementById('fingerprint_template').value = match[2];
                document.getElementById('fp-status').innerText = "Fingerprint enrolled!";
                document.getElementById('fp-status').className = "text-success ms-2";
            } else {
                document.getElementById('fp-status').innerText = "Clipboard does not contain a valid fingerprint template. Please scan your finger first.";
                document.getElementById('fp-status').className = "text-danger ms-2";
            }
        } catch (err) {
            document.getElementById('fp-status').innerText = "Failed to read clipboard: " + err;
            document.getElementById('fp-status').className = "text-danger ms-2";
        }
    }
    </script>
</head>
<body>
<div class="container mt-5">
    <h2>Edit Member</h2>
    <div class="mb-3">
        <strong>Username:</strong> <?php echo htmlspecialchars($member['username']); ?>
    </div>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label>Fullname</label>
            <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($member['fullname']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Contact</label>
            <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($member['contact']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Registration Date</label>
            <input type="date" name="dor" class="form-control" value="<?php echo htmlspecialchars($member['dor']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Plan (months)</label>
            <input type="number" name="plan" class="form-control" value="<?php echo htmlspecialchars($member['plan']); ?>" required>
        </div>
        <div class="mb-3">
            <label>New Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control" placeholder="New Password">
        </div>
        <div class="mb-3">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm New Password">
        </div>
        <div class="mb-3">
            <label>Fingerprint</label>
            <input type="hidden" name="fingerprint_template" id="fingerprint_template" value="<?php echo isset($member['fingerprint_template']) ? base64_encode($member['fingerprint_template']) : ''; ?>">
            <button type="button" class="btn btn-success me-2" onclick="enrollFingerprint()">Enroll Fingerprint</button>
            <span id="fp-status" class="ms-2">
                <?php if (!empty($member['fingerprint_template'])) echo '<span class="text-success">Fingerprint enrolled</span>'; ?>
            </span>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="../members.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>