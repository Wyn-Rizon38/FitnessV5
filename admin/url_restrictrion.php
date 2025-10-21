<?php
session_start();
include 'config.php';
include '../db/connection.php';

// Restrict access to logged-in admins
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: Login/admin_login.php");
    exit;
}
?>