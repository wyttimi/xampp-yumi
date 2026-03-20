<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

// Check if user has admin role
if ($_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit;
}
?>