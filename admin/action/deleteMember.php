<?php
include '../../db/connection.php';

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id > 0) {
    // Delete payments first
    $stmt = $conn->prepare("DELETE FROM monthlypayments WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Then delete member
    $stmt = $conn->prepare("DELETE FROM members WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: ../members.php?msg=deleted");
    exit();
} else {
    header("Location: ../members.php?msg=error");
    exit();
}
?>