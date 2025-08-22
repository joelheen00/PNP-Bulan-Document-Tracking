<?php
session_start();
include("../config.php"); // Database connection

// Get form inputs
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Step 1: Fetch user record
$sql = "SELECT * FROM users WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // No user found
    die("Invalid credentials. <a href='../index.php'>Go back</a>");
}

$user = $result->fetch_assoc();

// Step 2: Verify password using password_verify
if (password_verify($password, $user['password'])) {
    // Step 3: Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // Step 4: Redirect based on role (currently same dashboard)
    if ($user['role'] === 'admin') {
        header("Location: ../pages/dashboard.php"); // Admin dashboard
    } else {
        header("Location: ../pages/dashboard.php"); // User dashboard
    }
    exit();
} else {
    die("Invalid credentials. <a href='../index.php'>Go back</a>");
}
?>
